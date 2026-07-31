# Quick Reference: Checkout & Payment Flow

## File Quick Links

### Controllers
- `app/Http/Controllers/CheckoutController.php` - Order creation
- `app/Http/Controllers/PaymentController.php` - Payment handling (NEW)

### Views
- `resources/views/checkout.blade.php` - Main checkout page
- `resources/views/livewire/checkout.blade.php` - Checkout form
- `resources/views/payment/select.blade.php` - Payment method selector (NEW)
- `resources/views/payment/paystack.blade.php` - Paystack payment form (NEW)
- `resources/views/payment/success.blade.php` - Order confirmation (NEW)
- `resources/views/payment/failed.blade.php` - Payment error page (NEW)

### Routes
- `routes/web.php` - Route definitions

### Models
- `app/Models/Order.php` - Order model
- `app/Models/OrderItem.php` - Order item model

---

## The Flow at a Glance

```
CHECKOUT PROCESS:
  User clicks checkout → Livewire form → Native POST to /checkout
  ↓
  CheckoutController@store
    • Create Order record
    • Create OrderItems
    • Decrement product quantity
    • Clear session cart
    • Redirect to /payment/{order}
  ↓
PAYMENT SELECTION:
  PaymentController@index
    • Show payment method selector
    • Order summary
  ↓
PAYMENT PROCESSING:
  User selects method → POST to /payment/{order}
  ↓
  PaymentController@process
    • Route to handler
  ↓
  For Paystack:
    payWithPaystack() → Return form with PaystackPop
    User completes payment → Paystack redirects to /payment/callback
    callback() → Verify payment → Update order → Redirect to success
  
  For COD:
    payWithCOD() → Mark as processing → Send email → Redirect to success
  ↓
SUCCESS/FAILURE:
  PaymentController@success or @failed
    • Show confirmation or error
```

---

## Key Methods

### CheckoutController

```php
// Display checkout form
public function index()

// Create order and redirect to payment
public function store(Request $request)

// Optional confirmation page
public function success(Order $order)
```

### PaymentController

```php
// Show payment method selector
public function index(Order $order)

// Route to appropriate handler
public function process(Order $order, Request $request)

// Handle Paystack payment form
private function payWithPaystack(Order $order)

// Handle cash on delivery
private function payWithCOD(Order $order)

// Verify payment after Paystack callback
public function callback(Request $request)

// Success page
public function success(Order $order)

// Failed page
public function failed()
```

---

## Common Tasks

### Add a New Payment Method

1. Create method in PaymentController:
```php
private function payWithMyMethod(Order $order)
{
    // Your payment logic
}
```

2. Add case in process():
```php
elseif ($request->payment_method === 'my_method') {
    return $this->payWithMyMethod($order);
}
```

3. Add option in getAvailablePaymentMethods():
```php
if (SettingsHelper::isMyMethodEnabled()) {
    $methods['my_method'] = 'My Payment Method';
}
```

4. Create view for your method if needed

### Change Checkout Validation

Edit `CheckoutController@store()`:
```php
$validated = $request->validate([
    // Add new validation rules here
]);
```

### Modify Email Notification

Edit `PaymentController@sendConfirmationEmail()`:
```php
Mail::to($order->shipping_email)->send(
    new YourCustomMail($order)
);
```

### Add Post-Payment Logic

In `PaymentController@callback()` or `PaymentController@payWithCOD()`:
```php
// After order status is updated
$order->update(['payment_status' => 'paid']);

// Add your logic here
// e.g., dispatch jobs, send webhooks, etc.
```

---

## Settings Required

Check database Settings table for:

```
paystack_public_key      → Paystack public key
paystack_secret_key      → Paystack secret key
enable_cash_on_delivery  → Boolean (true/false)
tax_rate                 → Tax percentage (e.g., 7.5)
shipping_fee             → Base shipping cost
free_shipping_threshold  → Order subtotal for free shipping
site_email               → Admin email for notifications
site_currency            → Currency code (default: NGN)
```

---

## Security Checks Built-in

✅ User ownership validation
```php
if ($order->user_id !== Auth::id()) {
    abort(403);
}
```

✅ Payment status validation
```php
if ($order->payment_status !== 'pending') {
    return redirect()->route('orders.show', $order);
}
```

✅ Database transactions
```php
DB::beginTransaction();
// atomic operations
DB::commit();
```

✅ CSRF protection (automatic with Livewire and forms)

---

## Debugging Tips

### Order Not Created?
- Check `storage/logs/laravel.log` for errors
- Verify cart session: `session('cart')`
- Check stock availability
- Ensure user is authenticated

### Payment Not Verifying?
- Verify Paystack keys in Settings
- Check payment reference format: `ORD-{order_id}-{timestamp}`
- Check Paystack API response in logs
- Ensure callback route is not protected by auth

### Email Not Sending?
- Check MAIL_DRIVER in .env
- For 'log' driver, check `storage/logs/laravel.log`
- For 'smtp', verify SMTP credentials
- Check OrderConfirmationMail exists in `app/Mail/`

### Livewire Validation Errors?
- Check `$this->validate()` rules match form inputs
- Use `wire:model.defer` on inputs
- Ensure form uses `wire:submit="submitOrder"`

---

## Testing Paystack

### Test Cards (Development)
- Successful: 4111 1111 1111 1111
- Invalid: 4111 1111 1111 1110

### Test Flow
1. Go to /checkout
2. Fill shipping info
3. Click "Place Order"
4. Select "Paystack"
5. Enter test card
6. Should redirect to /payment/callback
7. Should show success page

### Verify in Database
```sql
SELECT * FROM orders ORDER BY id DESC LIMIT 1;
SELECT * FROM order_items WHERE order_id = [order_id];
```

---

## Performance Notes

- Checkout creation: < 1 second (DB transaction)
- Payment selection: < 500ms (display only)
- Paystack verification: < 5 seconds (API call with 10s timeout)
- Settings cached: 1 hour TTL
- No nested HTTP requests: Timeouts eliminated

---

## Architecture Advantages

✅ **Single Responsibility** - Each controller does one thing
✅ **Callback Model** - Simple, reliable payment verification
✅ **No Webhooks** - No complex external event handling
✅ **Easy to Test** - Clear, isolated components
✅ **Easy to Extend** - Add methods without changing existing code
✅ **Fast** - No unnecessary HTTP calls
✅ **Secure** - Multiple layers of validation
✅ **Maintainable** - Clear, documented flow

---

## Important Notes

⚠️ **Callback Route**: Not protected by auth middleware (Paystack redirects externally)
⚠️ **Settings**: Update via database or admin panel, not hardcoded
⚠️ **Emails**: Configure MAIL_DRIVER in .env before testing
⚠️ **Paystack Keys**: Keep secret key safe, never commit to repository
⚠️ **Logs**: Check storage/logs/laravel.log for debugging

---

## Reference Files

- Architecture Details: [ARCHITECTURE_IMPLEMENTATION.md](ARCHITECTURE_IMPLEMENTATION.md)
- Full Implementation: [IMPLEMENTATION_COMPLETE.md](IMPLEMENTATION_COMPLETE.md)
- Verification Report: [IMPLEMENTATION_VERIFICATION_REPORT.md](IMPLEMENTATION_VERIFICATION_REPORT.md)
