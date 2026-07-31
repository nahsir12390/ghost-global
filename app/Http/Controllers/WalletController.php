<?php

namespace App\Http\Controllers;

use App\Helpers\SettingsHelper;
use App\Models\WalletTransaction;
use App\Services\WalletService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class WalletController extends Controller
{
    public function __construct(private readonly WalletService $walletService)
    {
    }

    public function index(Request $request): View
    {
        $wallet = $this->walletService->getOrCreateWallet($request->user());
        $transactions = $request->user()
            ->walletTransactions()
            ->latest()
            ->paginate(15);

        return view('wallet.index', [
            'wallet' => $wallet,
            'transactions' => $transactions,
            'minimumTopup' => SettingsHelper::walletMinimumTopup(),
        ]);
    }

    public function topUp(Request $request)
    {
        abort_unless(SettingsHelper::isWalletEnabled(), 404);

        $minimumTopup = SettingsHelper::walletMinimumTopup();

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:' . $minimumTopup],
        ], [
            'amount.min' => 'The minimum wallet top-up is ' . SettingsHelper::currency($minimumTopup) . '.',
        ]);

        $publicKey = SettingsHelper::paystackPublicKey();

        if (! $publicKey) {
            return back()->with('error', 'Paystack is not configured yet for wallet top-up.');
        }

        $transaction = $this->walletService->createPendingTopUp(
            $request->user(),
            (float) $validated['amount'],
            'paystack',
            'Wallet top-up via Paystack.'
        );

        return view('wallet.paystack', [
            'transaction' => $transaction,
            'publicKey' => $publicKey,
            'amount' => (int) round(((float) $transaction->amount) * 100),
        ]);
    }

    public function callback(Request $request): RedirectResponse
    {
        abort_unless(SettingsHelper::isWalletEnabled(), 404);

        $reference = (string) $request->query('reference', '');

        if ($reference === '') {
            return redirect()->route('wallet.index')->with('error', 'No wallet payment reference was received.');
        }

        $transaction = WalletTransaction::query()
            ->where('reference', $reference)
            ->where('type', WalletTransaction::TYPE_TOPUP)
            ->first();

        if (! $transaction) {
            return redirect()->route('wallet.index')->with('error', 'Wallet top-up transaction could not be found.');
        }

        if ((int) $transaction->user_id !== (int) Auth::id()) {
            abort(403);
        }

        if ($transaction->status === 'completed') {
            return redirect()->route('wallet.index')->with('success', 'Your wallet top-up has already been confirmed.');
        }

        try {
            $secretKey = SettingsHelper::paystackSecretKey();

            if (! $secretKey) {
                throw new \RuntimeException('Paystack is not configured.');
            }

            $response = Http::withToken($secretKey)
                ->timeout(10)
                ->get('https://api.paystack.co/transaction/verify/' . urlencode($reference));

            $payload = $response->json();
            $paystackData = $payload['data'] ?? [];
            $paidAmount = (int) ($paystackData['amount'] ?? 0);
            $expectedAmount = (int) round(((float) $transaction->amount) * 100);

            if (
                $response->successful()
                && ($paystackData['status'] ?? null) === 'success'
                && ($paystackData['reference'] ?? '') === $reference
                && $paidAmount === $expectedAmount
            ) {
                $completedTransaction = $this->walletService->completeTopUp(
                    $transaction,
                    (string) ($paystackData['reference'] ?? $reference),
                    ['paystack_transaction_id' => $paystackData['id'] ?? null]
                );

                $this->sendWalletTopupEmails($request->user(), $completedTransaction);

                return redirect()->route('wallet.index')->with('success', 'Wallet funded successfully.');
            }

            $this->walletService->failTopUp($transaction, ['verification_response' => $payload]);

            return redirect()->route('wallet.index')->with('error', 'Wallet top-up could not be verified.');
        } catch (\Throwable $e) {
            Log::error('Wallet top-up callback failed: ' . $e->getMessage(), ['exception' => $e]);

            return redirect()->route('wallet.index')->with('error', 'Wallet top-up failed: ' . $e->getMessage());
        }
    }

    private function sendWalletTopupEmails($user, WalletTransaction $transaction): void
    {
        try {
            if (config('mail.default') === 'log') {
                Log::info('Wallet top-up emails logged.', [
                    'user_id' => $user->id,
                    'transaction_id' => $transaction->id,
                ]);
                return;
            }

            Mail::to($user->email)->send(new \App\Mail\WalletTopupCustomerMail($user, $transaction));

            $adminEmail = SettingsHelper::get('site_email', config('mail.from.address'));

            if ($adminEmail) {
                Mail::to($adminEmail)->send(new \App\Mail\WalletTopupAdminMail($user, $transaction));
            }
        } catch (\Throwable $e) {
            Log::error('Failed to send wallet top-up emails: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'transaction_id' => $transaction->id,
                'exception' => $e,
            ]);
        }
    }
}
