<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\SettingsHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;

class CheckoutController extends Controller
{
    /**
     * Display checkout page.
     */
    public function index()
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to checkout.');
        }
        
        // Check if cart has items
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart')->with('error', 'Your cart is empty.');
        }
        
        // Check stock for all items
        $outOfStockItems = [];
        foreach ($cart as $productId => $item) {
            $product = Product::with('vendor')->find($productId);
            if (!$product) {
                $outOfStockItems[] = 'Product not found!';
            } elseif ($product->tracksInventory() && $product->quantity < $item['quantity']) {
                $outOfStockItems[] = $product->name . ' is out of stock! Only ' . $product->quantity . ' available.';
            } elseif (! $product->vendorIsAvailable()) {
                $outOfStockItems[] = $product->name . ': ' . $product->unavailableReason();
            }
        }
        
        if (!empty($outOfStockItems)) {
            return redirect()->route('cart')->with('error', implode('<br>', $outOfStockItems));
        }
        
        $user = Auth::user();
        
        return view('checkout', compact('user'));
    }

    /**
     * Process checkout - Create order only (simplified).
     */
    public function store(Request $request)
    {
        // Get cart items
        $cart = session()->get('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('cart')->with('error', 'Your cart is empty.');
        }

        // Validate request
        $requiresShipping = collect($cart)->contains(fn ($item) => (bool) ($item['requires_shipping'] ?? true));

        $rules = [
            'shipping_first_name' => 'required|string|max:255',
            'shipping_last_name' => 'required|string|max:255',
            'shipping_email' => 'required|email',
            'shipping_phone' => 'required|string|max:20',
            'shipping_address' => $requiresShipping ? 'required|string' : 'nullable|string',
            'shipping_city' => $requiresShipping ? 'required|string|max:100' : 'nullable|string|max:100',
            'shipping_state' => $requiresShipping ? 'required|string|max:100' : 'nullable|string|max:100',
            'shipping_country' => $requiresShipping ? 'required|string|max:100' : 'nullable|string|max:100',
            'shipping_postal_code' => $requiresShipping ? 'required|string|max:20' : 'nullable|string|max:20',
            'same_as_shipping' => 'boolean',
            'notes' => 'nullable|string|max:500',
            
            // Billing address validation if not same as shipping
            'billing_first_name' => 'required_if:same_as_shipping,false|nullable|string|max:255',
            'billing_last_name' => 'required_if:same_as_shipping,false|nullable|string|max:255',
            'billing_email' => 'required_if:same_as_shipping,false|nullable|email',
            'billing_phone' => 'required_if:same_as_shipping,false|nullable|string|max:20',
            'billing_address' => 'required_if:same_as_shipping,false|nullable|string',
            'billing_city' => 'required_if:same_as_shipping,false|nullable|string|max:100',
            'billing_state' => 'required_if:same_as_shipping,false|nullable|string|max:100',
            'billing_country' => 'required_if:same_as_shipping,false|nullable|string|max:100',
            'billing_postal_code' => 'required_if:same_as_shipping,false|nullable|string|max:20',
        ];

        $validated = $request->validate($rules);

        // Calculate totals
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        foreach ($cart as $productId => $item) {
            $product = Product::with('vendor')->find($productId);

            if (! $product) {
                return redirect()->route('cart')->with('error', 'A product in your cart is no longer available.');
            }

            if ($product->tracksInventory() && $product->quantity < $item['quantity']) {
                return redirect()->route('cart')->with('error', $product->name . ' is out of stock! Only ' . $product->quantity . ' available.');
            }

            if (! $product->vendorIsAvailable()) {
                return redirect()->route('cart')->with('error', $product->name . ': ' . $product->unavailableReason());
            }
        }

        if ($requiresShipping) {
            $breakdown = SettingsHelper::calculateOrderBreakdown($subtotal);
        } else {
            $serviceFeeRate = (float) SettingsHelper::platformServiceFeeRate($subtotal);
            $serviceFee = round(($subtotal * $serviceFeeRate) / 100, 2);
            $breakdown = [
                'service_fee' => $serviceFee,
                'shipping' => 0,
                'total' => round($subtotal + $serviceFee, 2),
            ];
        }
        $tax = $breakdown['service_fee'];
        $shipping = $breakdown['shipping'];
        $total = $breakdown['total'];

        // Create order data
        $orderData = [
            'user_id' => Auth::id(),
            'subtotal' => $subtotal,
            'tax' => $tax,
            'shipping' => $shipping,
            'total' => $total,
            'status' => 'ordered',
            'payment_status' => 'pending',
            'notes' => $validated['notes'] ?? null,
            'shipping_first_name' => $validated['shipping_first_name'],
            'shipping_last_name' => $validated['shipping_last_name'],
            'shipping_email' => $validated['shipping_email'],
            'shipping_phone' => $validated['shipping_phone'],
            'shipping_address' => $requiresShipping ? $validated['shipping_address'] : null,
            'shipping_city' => $requiresShipping ? $validated['shipping_city'] : null,
            'shipping_state' => $requiresShipping ? $validated['shipping_state'] : null,
            'shipping_country' => $requiresShipping ? $validated['shipping_country'] : null,
            'shipping_postal_code' => $requiresShipping ? $validated['shipping_postal_code'] : null,
            'same_as_shipping' => $validated['same_as_shipping'] ?? true,
        ];

        // Add billing address if different
        if (!($validated['same_as_shipping'] ?? true)) {
            $orderData['billing_first_name'] = $validated['billing_first_name'];
            $orderData['billing_last_name'] = $validated['billing_last_name'];
            $orderData['billing_email'] = $validated['billing_email'];
            $orderData['billing_phone'] = $validated['billing_phone'];
            $orderData['billing_address'] = $validated['billing_address'];
            $orderData['billing_city'] = $validated['billing_city'];
            $orderData['billing_state'] = $validated['billing_state'];
            $orderData['billing_country'] = $validated['billing_country'];
            $orderData['billing_postal_code'] = $validated['billing_postal_code'];
        }

        // Start transaction
        DB::beginTransaction();
        
        try {
            // Create order
            $order = Order::create($orderData);
            
            // Set tracking number
            $order->update(['tracking_number' => $order->order_number]);

            // Create order items and update product quantities
            foreach ($cart as $item) {
                $product = Product::with('vendor')->find($item['product_id']);
                
                if ($product) {
                    if ($product->tracksInventory() && $product->quantity < $item['quantity']) {
                        throw new \Exception($product->name . ' is out of stock!');
                    }

                    if (! $product->vendorIsAvailable()) {
                        throw new \Exception($product->name . ': ' . $product->unavailableReason());
                    }
                    
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'vendor_id' => $product->vendor_id,
                        'product_name' => $product->name,
                        'price' => $item['price'],
                        'quantity' => $item['quantity'],
                        'total' => $item['price'] * $item['quantity'],
                        'options' => array_merge((array) ($item['options'] ?? []), [
                            'product_type' => $product->product_type,
                        ]),
                    ]);
                    
                    if ($product->tracksInventory()) {
                        $product->decrement('quantity', $item['quantity']);
                    }
                }
            }

            // Commit transaction
            DB::commit();

            // Clear cart
            session()->forget('cart');

            Log::info('Order created: ' . $order->order_number);

            // Redirect to payment page
            return redirect()->route('payment.index', $order->id);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Checkout failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Checkout failed: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display checkout success page.
     */
    public function success(Order $order)
    {
        // Ensure user can only view their own orders
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $order->loadMissing('items.product');

        return view('payment.success', compact('order'));
    }
}
