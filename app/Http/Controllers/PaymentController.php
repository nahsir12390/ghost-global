<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Helpers\SettingsHelper;
use App\Models\WalletTransaction;
use App\Services\ReferralService;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class PaymentController extends Controller
{
    public function __construct(
        private readonly WalletService $walletService,
        private readonly ReferralService $referralService
    )
    {
    }

    /**
     * Display payment method selection page.
     */
    public function index(Order $order)
    {
        // Ensure user owns this order
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        // Only allow payment for pending orders
        if ($order->payment_status !== 'pending') {
            return redirect()->route('my.orders.show', $order)
                ->with('info', 'Order has already been paid.');
        }

        $paymentMethods = $this->getAvailablePaymentMethods($order);
        
        Log::info('Payment methods available: ' . json_encode($paymentMethods));
        Log::info('Payment methods type: ' . gettype($paymentMethods));

        return view('payment.select', compact('order', 'paymentMethods'));
    }

    /**
     * Process payment with selected method.
     */
    public function process(Order $order, Request $request)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        Log::info('Payment process initiated for order: ' . $order->order_number);
        Log::info('Payment method: ' . $request->input('payment_method'));

        $request->validate([
            'payment_method' => 'required|in:paystack,cash_on_delivery,wallet',
        ]);

        if ($request->payment_method === 'paystack') {
            Log::info('Processing Paystack payment');
            return $this->payWithPaystack($order);
        } elseif ($request->payment_method === 'wallet') {
            Log::info('Processing wallet payment');
            return $this->payWithWallet($order);
        } elseif ($request->payment_method === 'cash_on_delivery') {
            Log::info('Processing COD payment');
            return $this->payWithCOD($order);
        }

        return back()->with('error', 'Invalid payment method');
    }

    /**
     * Paystack payment form.
     */
    private function payWithPaystack(Order $order)
    {
        try {
            $publicKey = SettingsHelper::paystackPublicKey();
            
            if (!$publicKey) {
                throw new \Exception('Paystack is not configured.');
            }

            // Generate payment reference
            $reference = 'ORD-' . $order->id . '-' . time();
            $order->update(['payment_reference' => $reference]);

            Log::info('Paystack payment initiated for order: ' . $order->order_number);

            return view('payment.paystack', [
                'order' => $order,
                'publicKey' => $publicKey,
                'amount' => (int)($order->total * 100),
                'reference' => $reference,
            ]);

        } catch (\Exception $e) {
            Log::error('Paystack setup failed: ' . $e->getMessage());
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Cash on delivery payment.
     */
    private function payWithCOD(Order $order)
    {
        $order->update([
            'payment_status' => 'pending',
            'status' => 'ordered',
            'tracking_number' => $order->tracking_number ?: $order->order_number,
        ]);

        $this->sendConfirmationEmail($order);

        Log::info('COD order confirmed: ' . $order->order_number);

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
            return redirect()->route('wallet.index')
                ->with('error', 'Your wallet balance is not enough for this order. Please fund your wallet first.');
        }

        try {
            $transaction = $this->walletService->debit(
                Auth::user(),
                (float) $order->total,
                WalletTransaction::TYPE_ORDER_PAYMENT,
                'Wallet payment for order ' . $order->order_number,
                'WLT-ORDER-' . $order->id,
                ['order_id' => $order->id]
            );

            $order->update([
                'payment_method' => 'wallet',
                'payment_status' => 'paid',
                'status' => 'ordered',
                'payment_reference' => $transaction->reference,
                'tracking_number' => $order->tracking_number ?: $order->order_number,
            ]);

            $this->referralService->rewardReferrerForFirstPaidOrder($order);
            $this->sendConfirmationEmail($order);

            return redirect()->route('payment.success', $order)
                ->with('success', 'Order paid successfully from your wallet.');
        } catch (\Throwable $e) {
            Log::error('Wallet payment failed: ' . $e->getMessage(), ['order_id' => $order->id, 'exception' => $e]);

            return back()->with('error', 'Wallet payment failed: ' . $e->getMessage());
        }
    }

    /**
     * Paystack callback - user redirected here after payment.
     */
    public function callback(Request $request)
    {
        $reference = $request->query('reference');

        if (!$reference) {
            return redirect()->route('payment.failed')
                ->with('error', 'No payment reference found.');
        }

        try {
            $secretKey = SettingsHelper::paystackSecretKey();

            if (!$secretKey) {
                throw new \Exception('Paystack not configured.');
            }

            // Verify payment with Paystack
            $response = Http::withToken($secretKey)
                ->timeout(10)
                ->get('https://api.paystack.co/transaction/verify/' . urlencode($reference));

            $data = $response->json();

            if ($response->successful() && isset($data['data']['status']) && $data['data']['status'] === 'success') {
                $order = Order::where('payment_reference', $reference)->firstOrFail();

                if (! $this->isValidSuccessfulPayment($order, $reference, $data)) {
                    Log::warning('Rejected Paystack callback due to mismatched order details.', [
                        'order_id' => $order->id,
                        'reference' => $reference,
                    ]);

                    return redirect()->route('payment.failed')
                        ->with('error', 'Payment verification failed for this order.');
                }

                $this->markOrderAsPaid($order, $reference, $data);

                $this->referralService->rewardReferrerForFirstPaidOrder($order);
                $this->sendConfirmationEmail($order);

                Log::info('Payment confirmed for order: ' . $order->order_number);

                return redirect()->route('payment.success', $order)
                    ->with('success', 'Payment successful!');
            } else {
                // Payment failed
                $order = Order::where('payment_reference', $reference)->first();
                
                if ($order) {
                    $order->update([
                        'payment_status' => 'failed',
                        'status' => 'failed'
                    ]);
                }

                Log::error('Payment verification failed: ' . $reference);

                return redirect()->route('payment.failed')
                    ->with('error', 'Payment could not be verified.');
            }

        } catch (\Exception $e) {
            Log::error('Payment callback error: ' . $e->getMessage());
            return redirect()->route('payment.failed')
                ->with('error', 'Payment error: ' . $e->getMessage());
        }
    }

    /**
     * Success page.
     */
    public function success(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        return view('payment.success', compact('order'));
    }

    /**
     * Failed page.
     */
    public function failed()
    {
        return view('payment.failed');
    }

    /**
     * Get available payment methods.
     */
    private function getAvailablePaymentMethods(?Order $order = null)
    {
        $methods = [];

        $paystackSecret = SettingsHelper::paystackSecretKey();
        if ($paystackSecret && strlen($paystackSecret) > 20) {
            $methods['paystack'] = 'Paystack';
        }

        if (SettingsHelper::isCashOnDeliveryEnabled()) {
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

    /**
     * Send confirmation email.
     */
    private function sendConfirmationEmail(Order $order)
    {
        try {
            if (config('mail.default') === 'log') {
                Log::info('Confirmation email for order: ' . $order->order_number);
                return;
            }

            Mail::to($order->shipping_email)->send(new \App\Mail\OrderConfirmationMail($order));

            $adminEmail = SettingsHelper::get('site_email', config('mail.from.address'));
            if ($adminEmail) {
                Mail::to($adminEmail)->send(new \App\Mail\AdminOrderNotificationMail($order));
            }

        } catch (\Exception $e) {
            Log::error('Email send failed: ' . $e->getMessage());
        }
    }

    /**
     * Check payment status without verifying
     */
    public function checkStatus(Request $request)
    {
        $request->validate([
            'reference' => 'required|string',
            'order_id' => 'required|integer',
        ]);

        try {
            $reference = $request->input('reference');
            $orderId = $request->input('order_id');
            
            Log::info('Checking payment status', ['reference' => $reference, 'order_id' => $orderId]);
            
            $secretKey = SettingsHelper::paystackSecretKey();

            if (!$secretKey) {
                Log::error('Paystack secret key not configured');
                return response()->json([
                    'success' => false,
                    'message' => 'Paystack is not configured.'
                ], 400);
            }

            // Check payment status with Paystack
            $response = Http::withToken($secretKey)
                ->timeout(10)
                ->get('https://api.paystack.co/transaction/verify/' . urlencode($reference));

            $data = $response->json();
            
            Log::info('Paystack status check response', [
                'status_code' => $response->status(),
                'transaction_status' => $data['data']['status'] ?? null
            ]);

            // If payment is successful, return true
            $order = Order::find($orderId);

            if (! $order || $order->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            if (
                $response->successful()
                && isset($data['data']['status'])
                && $data['data']['status'] === 'success'
                && $this->isValidSuccessfulPayment($order, $reference, $data)
            ) {
                Log::info('Payment status check: SUCCESS', ['reference' => $reference]);
                return response()->json([
                    'success' => true,
                    'message' => 'Payment completed'
                ]);
            } else {
                Log::info('Payment status check: NOT SUCCESSFUL', ['reference' => $reference, 'status' => $data['data']['status'] ?? 'unknown']);
                return response()->json([
                    'success' => false,
                    'message' => 'Payment not completed'
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Payment status check error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Status check error'
            ], 500);
        }
    }

    /**
     * Verify Paystack payment via AJAX
     */
    public function verify(Request $request)
    {
        $request->validate([
            'reference' => 'required|string',
            'order_id' => 'required|integer',
        ]);

        try {
            $reference = $request->input('reference');
            $orderId = $request->input('order_id');
            
            Log::info('Payment verification started', ['reference' => $reference, 'order_id' => $orderId]);
            
            $secretKey = SettingsHelper::paystackSecretKey();

            if (!$secretKey) {
                Log::error('Paystack secret key not configured');
                return response()->json([
                    'success' => false,
                    'message' => 'Paystack is not configured.'
                ], 400);
            }

            // Verify payment with Paystack
            $response = Http::withToken($secretKey)
                ->timeout(10)
                ->get('https://api.paystack.co/transaction/verify/' . urlencode($reference));

            $data = $response->json();
            
            Log::info('Paystack API response', [
                'status_code' => $response->status(),
                'data' => $data
            ]);

            $order = Order::findOrFail($orderId);

            if ($response->successful() && isset($data['data']['status']) && $data['data']['status'] === 'success') {
                Log::info('Order found', ['order_id' => $orderId, 'user_id' => $order->user_id]);

                // Verify user owns this order
                if ($order->user_id !== Auth::id()) {
                    Log::warning('Unauthorized payment verification attempt', ['order_id' => $orderId]);
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthorized'
                    ], 403);
                }

                if (! $this->isValidSuccessfulPayment($order, $reference, $data)) {
                    Log::warning('Rejected Paystack verification due to mismatched order details.', [
                        'order_id' => $order->id,
                        'reference' => $reference,
                    ]);

                    return response()->json([
                        'success' => false,
                        'message' => 'Payment details did not match this order.'
                    ], 422);
                }

                $this->markOrderAsPaid($order, $reference, $data);

                $this->referralService->rewardReferrerForFirstPaidOrder($order);
                $this->sendConfirmationEmail($order);

                Log::info('Payment confirmed for order: ' . $order->order_number);

                return response()->json([
                    'success' => true,
                    'message' => 'Payment verified successfully',
                    'redirect' => route('payment.success', $order)
                ]);
            } else {
                // Payment failed
                if ($order->user_id === Auth::id()) {
                    $order->update([
                        'payment_status' => 'failed',
                        'status' => 'failed'
                    ]);
                }

                Log::error('Payment verification failed: ' . $reference, ['response' => $data]);

                return response()->json([
                    'success' => false,
                    'message' => 'Payment could not be verified.'
                ], 400);
            }

        } catch (Throwable $e) {
            Log::error('Payment verification error: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'success' => false,
                'message' => 'Payment verification error: ' . $e->getMessage()
            ], 500);
        }
    }

    private function isValidSuccessfulPayment(Order $order, string $reference, array $payload): bool
    {
        $transaction = $payload['data'] ?? [];
        $transactionReference = (string) ($transaction['reference'] ?? '');
        $paidAmount = (int) ($transaction['amount'] ?? 0);
        $expectedAmount = (int) round(((float) $order->total) * 100);

        return $order->payment_status === 'pending'
            && ! empty($order->payment_reference)
            && hash_equals((string) $order->payment_reference, $reference)
            && hash_equals((string) $order->payment_reference, $transactionReference)
            && $paidAmount === $expectedAmount;
    }

    private function markOrderAsPaid(Order $order, string $reference, array $payload): void
    {
        $order->update([
            'payment_status' => 'paid',
            'status' => 'ordered',
            'payment_id' => $payload['data']['id'] ?? null,
            'payment_reference' => $reference,
            'tracking_number' => $order->tracking_number ?: $order->order_number,
        ]);
    }
}
