<?php

namespace App\Services;

use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class WalletService
{
    public function getOrCreateWallet(User $user): Wallet
    {
        return Wallet::firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0]
        );
    }

    public function createPendingTopUp(
        User $user,
        float $amount,
        string $provider = 'paystack',
        ?string $description = null,
        array $meta = []
    ): WalletTransaction {
        $wallet = $this->getOrCreateWallet($user);

        return WalletTransaction::create([
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'reference' => $this->generateReference('WLT-TOPUP'),
            'type' => WalletTransaction::TYPE_TOPUP,
            'direction' => 'credit',
            'amount' => round($amount, 2),
            'balance_before' => (float) $wallet->balance,
            'balance_after' => (float) $wallet->balance,
            'status' => 'pending',
            'payment_provider' => $provider,
            'description' => $description ?: 'Wallet top-up initiated.',
            'meta' => $meta,
        ]);
    }

    public function completeTopUp(WalletTransaction $transaction, ?string $externalReference = null, array $meta = []): WalletTransaction
    {
        return DB::transaction(function () use ($transaction, $externalReference, $meta) {
            $transaction = WalletTransaction::query()->lockForUpdate()->findOrFail($transaction->id);

            if ($transaction->status === 'completed') {
                return $transaction;
            }

            if ($transaction->status !== 'pending') {
                throw new RuntimeException('This wallet top-up can no longer be completed.');
            }

            $wallet = Wallet::query()->lockForUpdate()->findOrFail($transaction->wallet_id);
            $balanceBefore = (float) $wallet->balance;
            $balanceAfter = round($balanceBefore + (float) $transaction->amount, 2);

            $wallet->update(['balance' => $balanceAfter]);

            $transaction->update([
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'status' => 'completed',
                'external_reference' => $externalReference ?: $transaction->external_reference,
                'meta' => array_merge($transaction->meta ?? [], $meta),
                'completed_at' => now(),
            ]);

            return $transaction->fresh();
        });
    }

    public function failTopUp(WalletTransaction $transaction, array $meta = []): WalletTransaction
    {
        if ($transaction->status !== 'pending') {
            return $transaction;
        }

        $transaction->update([
            'status' => 'failed',
            'meta' => array_merge($transaction->meta ?? [], $meta),
        ]);

        return $transaction->fresh();
    }

    public function debit(
        User $user,
        float $amount,
        string $type,
        string $description,
        ?string $reference = null,
        array $meta = []
    ): WalletTransaction {
        return DB::transaction(function () use ($user, $amount, $type, $description, $reference, $meta) {
            $wallet = Wallet::query()
                ->lockForUpdate()
                ->firstOrCreate(['user_id' => $user->id], ['balance' => 0]);

            $amount = round($amount, 2);
            $balanceBefore = (float) $wallet->balance;

            if ($amount <= 0) {
                throw new RuntimeException('Wallet debit amount must be greater than zero.');
            }

            if ($balanceBefore < $amount) {
                throw new RuntimeException('Insufficient wallet balance.');
            }

            $balanceAfter = round($balanceBefore - $amount, 2);
            $wallet->update(['balance' => $balanceAfter]);

            return WalletTransaction::create([
                'user_id' => $user->id,
                'wallet_id' => $wallet->id,
                'reference' => $reference ?: $this->generateReference('WLT-DEBIT'),
                'type' => $type,
                'direction' => 'debit',
                'amount' => $amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'status' => 'completed',
                'description' => $description,
                'meta' => $meta,
                'completed_at' => now(),
            ]);
        });
    }

    public function credit(
        User $user,
        float $amount,
        string $type,
        string $description,
        ?string $reference = null,
        array $meta = []
    ): WalletTransaction {
        return DB::transaction(function () use ($user, $amount, $type, $description, $reference, $meta) {
            $wallet = Wallet::query()
                ->lockForUpdate()
                ->firstOrCreate(['user_id' => $user->id], ['balance' => 0]);

            $amount = round($amount, 2);
            $balanceBefore = (float) $wallet->balance;

            if ($amount <= 0) {
                throw new RuntimeException('Wallet credit amount must be greater than zero.');
            }

            $balanceAfter = round($balanceBefore + $amount, 2);
            $wallet->update(['balance' => $balanceAfter]);

            return WalletTransaction::create([
                'user_id' => $user->id,
                'wallet_id' => $wallet->id,
                'reference' => $reference ?: $this->generateReference('WLT-CREDIT'),
                'type' => $type,
                'direction' => 'credit',
                'amount' => $amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'status' => 'completed',
                'description' => $description,
                'meta' => $meta,
                'completed_at' => now(),
            ]);
        });
    }

    private function generateReference(string $prefix): string
    {
        return $prefix . '-' . strtoupper(Str::random(10));
    }
}
