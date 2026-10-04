<?php

namespace App\Http\Controllers;

use App\Helpers\SettingsHelper;
use App\Models\Order;
use App\Models\WalletTransaction;
use App\Services\ReferralService;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Throwable;

class PaymentController extends Controller
{
    public function __construct(
        private readonly WalletService $walletService,
        private readonly ReferralService $referralService
    ) {}

    public function index(Order $order)
    {
        $this->authorizeOwner($order);

        if ($order->payment_status !== 'pending') {
            return redirect()->route('my.orders.show', $order)->with('info', 'This order is not awaiting payment.');
        }

        return view('payment.select', [
            'order' => $order,
            'paymentMethods' => $this->getAvailablePaymentMethods($order),
        ]);
    }

    public function process(Order $order, Request $request)
    {
        $this->authorizeOwner($order);
        abort_unless($order->payment_status === 'pending', 422, 'This order is not awaiting payment.');

        $validated = $request->validate([
            'payment_method' => 'required|in:paystack,cash_on_delivery,wallet',
        ]);

        abort_unless(array_key_exists($validated['payment_method'], $this->getAvailablePaymentMethods($order)), 422, 'This payment method is not available for this order.');

        return match ($validated['payment_method']) {
            'paystack' => $this->payWithPaystack($order),
            'wallet' => $this->payWithWallet($order),
            'cash_on_delivery' => $this->payWithCOD($order),
        };
    }

    private function payWithPaystack(Order $order)
    {
        $publicKey = SettingsHelper::paystackPublicKey();
        $secretKey = SettingsHelper::paystackSecretKey();

        if (! $publicKey || ! $secretKey) {
            return back()->with('error', 'Paystack is not configured.');
        }

        $reference = 'ORD-'.$order->id.'-'.Str::upper(Str::random(12));
        $order->update([
            'payment_method' => 'paystack',
            'payment_reference' => $reference,
        ]);

        return view('payment.paystack', [
            'order' => $order,
            'publicKey' => $publicKey,
            'amount' => $this->expectedAmount($order),
            'reference' => $reference,
            'currency' => $this->expectedCurrency($order),
        ]);
    }

    private function payWithCOD(Order $order)
    {
        $order->update([
            'payment_method' => 'cash_on_delivery',
            'payment_status' => 'pending',
            'status' => 'ordered',
            'tracking_number' => $order->tracking_number ?: $order->order_number,
        ]);

        $this->sendConfirmationEmail($order);

        return redirect()->route('payment.success', $order)
            ->with('success', 'Order confirmed! You will pay upon delivery.');
    }

    private function payWithWallet(Order $order)
    {
        if (! SettingsHelper::isWalletEnabled()) {
            return back()->with('error', 'Wallet payments are currently unavailable.');
        }

        $wallet = $this->walletService->getOrCreateWallet(Auth::user());
        if ((float) $wallet->balance < (float) $order->total) {
            return redirect()->route('wallet.index')->with('error', 'Your wallet balance is not enough for this order.');
        }

        try {
            $transaction = $this->walletService->debit(
                Auth::user(),
                (float) $order->total,
                WalletTransaction::TYPE_ORDER_PAYMENT,
                'Wallet payment for order '.$order->order_number,
                'WLT-ORDER-'.$order->id,
                ['order_id' => $order->id]
            );

            $order->update([
                'payment_method' => 'wallet',
                'payment_status' => 'paid',
                'status' => 'ordered',
                'payment_reference' => $transaction->reference,
                'tracking_number' => $order->tracking_number ?: $order->order_number,
            ]);

            $this->afterFirstSuccessfulPayment($order);

            return redirect()->route('payment.success', $order)->with('success', 'Order paid successfully from your wallet.');
        } catch (Throwable $e) {
            Log::error('Wallet payment failed', ['order_id' => $order->id, 'exception' => $e]);
            return back()->with('error', 'Wallet payment failed. Please try again.');
        }
    }

    public function callback(Request $request)
    {
        $reference = (string) $request->query('reference', '');
        if ($reference === '') {
            return redirect()->route('payment.failed')->with('error', 'No payment reference found.');
        }

        $order = Order::where('payment_reference', $reference)->first();
        if (! $order) {
            return redirect()->route('payment.failed')->with('error', 'Payment reference does not match an order.');
        }

        try {
            $result = $this->verifyWithPaystack($order, $reference);

            if ($result['success']) {
                $this->completePaystackPayment($order, $reference, $result['payload']);
                return redirect()->route('payment.success', $order)->with('success', 'Payment successful!');
            }

            if ($result['terminal_failure']) {
                $this->markPaymentFailed($order);
            }

            return redirect()->route('payment.failed')->with('error', $result['message']);
        } catch (Throwable $e) {
            Log::error('Payment callback error', ['reference' => $reference, 'exception' => $e]);
            return redirect()->route('payment.failed')->with('error', 'We could not confirm your payment right now. Please check your order before trying again.');
        }
    }

    public function success(Order $order)
    {
        $this->authorizeOwner($order);
        return view('payment.success', compact('order'));
    }

    public function failed()
    {
        return view('payment.failed');
    }

    public function checkStatus(Request $request)
    {
        $validated = $request->validate([
            'reference' => 'required|string|max:150',
            'order_id' => 'required|integer',
        ]);

        $order = Order::find($validated['order_id']);
        if (! $order || $order->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        if (! hash_equals((string) $order->payment_reference, (string) $validated['reference'])) {
            return response()->json(['success' => false, 'message' => 'Payment reference mismatch.'], 422);
        }

        if ($order->payment_status === 'paid') {
            return response()->json(['success' => true, 'message' => 'Payment completed']);
        }

        try {
            $result = $this->verifyWithPaystack($order, $validated['reference']);
            return response()->json([
                'success' => $result['success'],
                'message' => $result['success'] ? 'Payment completed' : $result['message'],
            ]);
        } catch (Throwable $e) {
            Log::error('Payment status check error', ['order_id' => $order->id, 'exception' => $e]);
            return response()->json(['success' => false, 'message' => 'Unable to check payment status right now.'], 503);
        }
    }

    public function verify(Request $request)
    {
        $validated = $request->validate([
            'reference' => 'required|string|max:150',
            'order_id' => 'required|integer',
        ]);

        $order = Order::find($validated['order_id']);
        if (! $order || $order->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        if (! hash_equals((string) $order->payment_reference, (string) $validated['reference'])) {
            return response()->json(['success' => false, 'message' => 'Payment reference mismatch.'], 422);
        }

        if ($order->payment_status === 'paid') {
            return response()->json([
                'success' => true,
                'message' => 'Payment already verified.',
                'redirect' => route('payment.success', $order),
            ]);
        }

        try {
            $result = $this->verifyWithPaystack($order, $validated['reference']);

            if (! $result['success']) {
                if ($result['terminal_failure']) $this->markPaymentFailed($order);
                return response()->json(['success' => false, 'message' => $result['message']], $result['terminal_failure'] ? 400 : 202);
            }

            $this->completePaystackPayment($order, $validated['reference'], $result['payload']);

            return response()->json([
                'success' => true,
                'message' => 'Payment verified successfully',
                'redirect' => route('payment.success', $order),
            ]);
        } catch (Throwable $e) {
            Log::error('Payment verification error', ['order_id' => $order->id, 'exception' => $e]);
            return response()->json(['success' => false, 'message' => 'Unable to verify payment right now.'], 503);
        }
    }

    private function verifyWithPaystack(Order $order, string $reference): array
    {
        $secretKey = SettingsHelper::paystackSecretKey();
        if (! $secretKey) throw new \RuntimeException('Paystack is not configured.');

        $response = Http::withToken($secretKey)
            ->acceptJson()
            ->timeout(15)
            ->retry(2, 250)
            ->get('https://api.paystack.co/transaction/verify/'.rawurlencode($reference));

        if (! $response->successful()) {
            throw new \RuntimeException('Paystack verification request failed.');
        }

        $payload = $response->json();
        $status = strtolower((string) data_get($payload, 'data.status', ''));

        if ($status === 'success') {
            return [
                'success' => $this->isValidSuccessfulPayment($order, $reference, $payload),
                'terminal_failure' => false,
                'message' => 'Payment details did not match this order.',
                'payload' => $payload,
            ];
        }

        $terminal = in_array($status, ['failed', 'abandoned', 'reversed'], true);

        return [
            'success' => false,
            'terminal_failure' => $terminal,
            'message' => $terminal ? 'Payment was not successful.' : 'Payment is still being processed.',
            'payload' => $payload,
        ];
    }

    private function isValidSuccessfulPayment(Order $order, string $reference, array $payload): bool
    {
        $transactionReference = (string) data_get($payload, 'data.reference', '');
        $paidAmount = (int) data_get($payload, 'data.amount', 0);
        $currency = strtoupper((string) data_get($payload, 'data.currency', ''));

        return $order->payment_status === 'pending'
            && filled($order->payment_reference)
            && hash_equals((string) $order->payment_reference, $reference)
            && hash_equals((string) $order->payment_reference, $transactionReference)
            && $paidAmount === $this->expectedAmount($order)
            && $currency === $this->expectedCurrency($order);
    }

    private function completePaystackPayment(Order $order, string $reference, array $payload): void
    {
        $order->refresh();
        if ($order->payment_status === 'paid') return;
        if (! $this->isValidSuccessfulPayment($order, $reference, $payload)) {
            throw new \RuntimeException('Verified transaction does not match this order.');
        }

        $order->update([
            'payment_method' => 'paystack',
            'payment_status' => 'paid',
            'status' => 'ordered',
            'payment_id' => data_get($payload, 'data.id'),
            'payment_reference' => $reference,
            'tracking_number' => $order->tracking_number ?: $order->order_number,
        ]);

        $this->afterFirstSuccessfulPayment($order);
    }

    private function markPaymentFailed(Order $order): void
    {
        if ($order->payment_status !== 'paid') {
            $order->update(['payment_status' => 'failed']);
        }
    }

    private function afterFirstSuccessfulPayment(Order $order): void
    {
        $this->referralService->rewardReferrerForFirstPaidOrder($order);
        $this->sendConfirmationEmail($order);
    }

    private function getAvailablePaymentMethods(?Order $order = null): array
    {
        $methods = [];

        if (SettingsHelper::paystackPublicKey() && SettingsHelper::paystackSecretKey()) {
            $methods['paystack'] = 'Paystack';
        }

        // Worldwide Ghost Global orders are prepaid. Keep COD only for legacy/local orders without buyer_email.
        if (SettingsHelper::isCashOnDeliveryEnabled() && ! $order?->buyer_email) {
            $methods['cash_on_delivery'] = 'Cash on Delivery';
        }

        if (SettingsHelper::isWalletEnabled() && Auth::check()) {
            $wallet = $this->walletService->getOrCreateWallet(Auth::user());
            if (! $order || (float) $wallet->balance >= (float) $order->total) {
                $methods['wallet'] = 'Wallet Balance';
            }
        }

        return $methods;
    }

    private function expectedAmount(Order $order): int
    {
        return (int) round(((float) $order->total) * 100);
    }

    private function expectedCurrency(Order $order): string
    {
        // Current storefront prices are stored and displayed in NGN.
        return $order->buyer_email ? 'NGN' : strtoupper(SettingsHelper::currencyCode());
    }

    private function authorizeOwner(Order $order): void
    {
        abort_unless((int) $order->user_id === (int) Auth::id(), 403);
    }

    private function sendConfirmationEmail(Order $order): void
    {
        try {
            if (config('mail.default') === 'log') {
                Log::info('Confirmation email for order: '.$order->order_number);
                return;
            }

            if ($order->contact_email) {
                Mail::to($order->contact_email)->send(new \App\Mail\OrderConfirmationMail($order));
            }

            $adminEmail = SettingsHelper::get('site_email', config('mail.from.address'));
            if ($adminEmail) {
                Mail::to($adminEmail)->send(new \App\Mail\AdminOrderNotificationMail($order));
            }
        } catch (Throwable $e) {
            Log::error('Order confirmation email failed', ['order_id' => $order->id, 'exception' => $e]);
        }
    }
}
