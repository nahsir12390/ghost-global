<?php

namespace App\Services;

use App\Helpers\SettingsHelper;
use App\Models\Order;
use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class ReferralService
{
    public const SESSION_KEY = 'referral_code';

    public function rememberReferralCode(?string $code): bool
    {
        $referrer = $this->findReferrerByCode($code);

        if (! $referrer) {
            return false;
        }

        Session::put(self::SESSION_KEY, $referrer->referral_code);

        return true;
    }

    public function currentReferralCode(): ?string
    {
        return Session::get(self::SESSION_KEY);
    }

    public function clearReferralCode(): void
    {
        Session::forget(self::SESSION_KEY);
    }

    public function findReferrerByCode(?string $code): ?User
    {
        $normalized = strtoupper(trim((string) $code));

        if ($normalized === '') {
            return null;
        }

        return User::query()
            ->where('referral_code', $normalized)
            ->first();
    }

    public function assignReferrer(User $user, ?string $code = null): void
    {
        if ($user->referred_by_id) {
            return;
        }

        $referrer = $this->findReferrerByCode($code ?: $this->currentReferralCode());

        if (! $referrer || $referrer->id === $user->id) {
            return;
        }

        $user->forceFill([
            'referred_by_id' => $referrer->id,
        ])->save();

        $this->clearReferralCode();
    }

    public function rewardReferrerForFirstPaidOrder(Order $order): ?WalletTransaction
    {
        if (! SettingsHelper::isReferralEnabled()) {
            return null;
        }

        $rewardAmount = SettingsHelper::referralRewardAmount();

        if ($rewardAmount <= 0) {
            return null;
        }

        return DB::transaction(function () use ($order, $rewardAmount) {
            $order = Order::query()->lockForUpdate()->findOrFail($order->id);
            $customer = User::query()->lockForUpdate()->find($order->user_id);

            if (! $customer || ! $customer->referred_by_id || $customer->referral_rewarded_at) {
                return null;
            }

            if ($order->payment_status !== 'paid') {
                return null;
            }

            $hasPriorPaidOrder = Order::query()
                ->where('user_id', $customer->id)
                ->where('payment_status', 'paid')
                ->where('id', '!=', $order->id)
                ->exists();

            if ($hasPriorPaidOrder) {
                return null;
            }

            $referrer = User::query()->find($customer->referred_by_id);

            if (! $referrer) {
                return null;
            }

            $walletTransaction = app(WalletService::class)->credit(
                $referrer,
                $rewardAmount,
                WalletTransaction::TYPE_REFERRAL_BONUS,
                'Referral bonus for first paid order by ' . $customer->name,
                'WLT-REF-' . $order->id . '-' . $referrer->id,
                [
                    'order_id' => $order->id,
                    'referred_user_id' => $customer->id,
                    'referrer_user_id' => $referrer->id,
                ]
            );

            $customer->forceFill([
                'referral_rewarded_at' => now(),
                'referral_reward_order_id' => $order->id,
            ])->save();

            Log::info('Referral bonus credited.', [
                'order_id' => $order->id,
                'customer_id' => $customer->id,
                'referrer_id' => $referrer->id,
                'amount' => $rewardAmount,
            ]);

            return $walletTransaction;
        });
    }
}
