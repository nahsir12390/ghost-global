<?php

namespace App\Http\Controllers;

use App\Helpers\SettingsHelper;
use App\Mail\WalletRefundAdminMail;
use App\Mail\WalletRefundCustomerMail;
use App\Models\Order;
use App\Models\Product;
use App\Models\WalletTransaction;
use App\Services\WalletService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    public function __construct(private readonly WalletService $walletService) {}

    public function index(Request $request)
    {
        $orders = $request->user()->orders()
            ->with(['shipments.items', 'items.product'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('my-orders', compact('orders'));
    }

    public function show(Order $order)
    {
        // Ensure user can only view their own orders
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $order->load(['shipments.items', 'items.product']);

        return view('order-details', compact('order'));
    }

    public function cancel(Request $request, Order $order): RedirectResponse
    {
        $refundTransaction = null;

        if ($order->user_id !== $request->user()->id) {
            abort(403);
        }

        if (! $order->canBeCancelledByCustomer($request->user())) {
            return back()->with('error', 'This order can no longer be cancelled.');
        }

        DB::transaction(function () use ($order, $request, &$refundTransaction) {
            $order->loadMissing('items.product.vendor');

            foreach ($order->items as $item) {
                $product = $item->product;

                if ($product && $product->tracksInventory()) {
                    $product->increment('quantity', $item->quantity);
                }
            }

            if ($order->payment_method === 'wallet' && $order->payment_status === 'paid') {
                $refundTransaction = $this->walletService->credit(
                    $request->user(),
                    (float) $order->total,
                    WalletTransaction::TYPE_REFUND,
                    'Wallet refund for cancelled order '.$order->order_number,
                    'WLT-REFUND-'.$order->id,
                    ['order_id' => $order->id]
                );

                $order->payment_status = 'refunded';
            }

            $order->status = 'cancelled';
            $order->save();
        });

        if ($refundTransaction) {
            $this->sendWalletRefundEmails($request->user(), $order->fresh(['user', 'items.product.vendor']), $refundTransaction);
        }

        return back()->with('success', $order->payment_method === 'wallet'
            ? 'Your order has been cancelled and the amount was returned to your wallet.'
            : 'Your order has been cancelled successfully.');
    }

    private function sendWalletRefundEmails($user, Order $order, WalletTransaction $transaction): void
    {
        try {
            if (config('mail.default') === 'log') {
                Log::info('Wallet refund emails logged.', [
                    'order_id' => $order->id,
                    'transaction_id' => $transaction->id,
                ]);

                return;
            }

            if ($user->email) {
                Mail::to($user->email)->send(new WalletRefundCustomerMail($user, $order, $transaction));
            }

            $adminEmail = SettingsHelper::get('site_email', config('mail.from.address'));

            if ($adminEmail) {
                Mail::to($adminEmail)->send(new WalletRefundAdminMail($user, $order, $transaction));
            }
        } catch (\Throwable $e) {
            Log::error('Failed to send wallet refund emails: '.$e->getMessage(), [
                'order_id' => $order->id,
                'transaction_id' => $transaction->id,
            ]);
        }
    }

    public function downloads(Request $request)
    {
        $products = $this->purchasedProductsQuery($request)
            ->where('product_type', Product::TYPE_DIGITAL)
            ->get();

        return view('my-downloads', compact('products'));
    }

    public function courses(Request $request)
    {
        $products = $this->purchasedProductsQuery($request)
            ->where('product_type', Product::TYPE_COURSE)
            ->get();

        return view('my-courses', compact('products'));
    }

    public function downloadProduct(Request $request, Product $product)
    {
        abort_unless($product->isDigital(), 404);
        abort_unless($this->userOwnsProduct($request, $product), 403);

        if ($product->download_file_path && Storage::disk('public')->exists($product->download_file_path)) {
            return Storage::disk('public')->download($product->download_file_path, basename($product->download_file_path));
        }

        if ($product->download_link) {
            return redirect()->away($product->download_link);
        }

        return back()->with('error', 'This digital product does not have a download attached yet.');
    }

    private function purchasedProductsQuery(Request $request)
    {
        return Product::query()
            ->whereHas('orderItems.order', function ($query) use ($request) {
                $query->where('user_id', $request->user()->id)
                    ->whereIn('status', ['ordered', 'confirmed', 'processing', 'picked_up', 'on_the_way', 'completed', 'delivered'])
                    ->whereIn('payment_status', ['pending', 'paid']);
            })
            ->latest()
            ->distinct();
    }

    private function userOwnsProduct(Request $request, Product $product): bool
    {
        return $product->orderItems()
            ->whereHas('order', function ($query) use ($request) {
                $query->where('user_id', $request->user()->id)
                    ->whereIn('status', ['ordered', 'confirmed', 'processing', 'picked_up', 'on_the_way', 'completed', 'delivered'])
                    ->whereIn('payment_status', ['pending', 'paid']);
            })
            ->exists();
    }
}
