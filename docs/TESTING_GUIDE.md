# Quick Start: Testing the New Checkout Flow

## Files Changed

### Controllers
- ✅ `E_commerce/app/Http/Controllers/CheckoutController.php` - Completely refactored

### Livewire Components
- ✅ `E_commerce/app/Livewire/Checkout.php` - Simplified (address only)

### Routes
- ✅ `E_commerce/routes/web.php` - Added new routes

### Views
- ✅ `E_commerce/resources/views/checkout.blade.php` - Main checkout page
- ✅ `E_commerce/resources/views/checkout-payment.blade.php` - Payment method selector
- ✅ `E_commerce/resources/views/paystack-checkout.blade.php` - **NEW** Paystack payment modal
- ✅ `E_commerce/resources/views/checkout-success.blade.php` - Updated success page
- ✅ `E_commerce/resources/views/checkout-failed.blade.php` - Updated failed page
- ℹ️ `E_commerce/resources/views/checkout-cancel.blade.php` - No changes needed

## Testing Steps

### 1. Test Address Collection
```bash
cd c:\xampp\htdocs\dashboard\E_commerce
php artisan serve
```

1. Add items to cart
2. Navigate to `/checkout`
3. Fill in shipping address form
4. Submit form

**Expected Result**: Order created in database, redirects to `/checkout/{order}/payment`

### 2. Test Payment Method Selection
1. On payment page, select payment method
2. Click "Continue to Payment"

**Expected Result**: 
- For Paystack: Redirects to Paystack payment modal
- For COD: Updates order, redirects to success page

### 3. Test Paystack Payment
1. Select Paystack method
2. Click "Continue to Payment"
3. Click "Pay with Paystack" button
4. Complete Paystack test payment

**Expected Result**: Verifies payment, updates order status to 'paid', redirects to success

### 4. Check Database

```bash
php artisan tinker
```

```php
// Check order created
$order = App\Models\Order::latest()->first();
$order->toArray();

// Check order items
$order->items()->get();

// Check payment status
$order->payment_status;
$order->status;
```

### 5. Check Logs
```bash
tail -f storage/logs/laravel.log
```

Should see entries for:
- Order creation
- Paystack payment initiated
- Payment verification
- Order confirmation email

## Troubleshooting

### "Checkout failed: Your cart is empty"
- **Cause**: Session cart not set
- **Fix**: Add items to cart before checkout

### "No payment methods available"
- **Cause**: Paystack not configured or COD not enabled
- **Fix**: Check settings in admin panel

### "Payment verification failed"
- **Cause**: Paystack secret key invalid
- **Fix**: Check Paystack configuration in `.env` or admin settings

### Timeout on Paystack verification
- **Cause**: Paystack API unreachable
- **Fix**: Check internet connection, Paystack status

### Order created but payment page doesn't load
- **Cause**: Missing route or view
- **Fix**: Check `routes/web.php` and `resources/views/checkout-payment.blade.php` exist

## Key Endpoints

| Endpoint | Method | Purpose |
|----------|--------|---------|
| `/checkout` | GET | Display checkout form |
| `/checkout` | POST | Create order |
| `/checkout/{order}/payment` | GET | Select payment method |
| `/checkout/{order}/process-payment` | POST | Process payment |
| `/checkout/{order}/success` | GET | Success page |
| `/checkout/failed` | GET | Failed page |
| `/checkout/paystack/callback` | GET | Paystack callback |
| `/checkout/paystack/webhook` | POST | Paystack webhook |

## Database Changes

No new tables needed. Orders and OrderItems already exist.

### Order Fields Updated
- `payment_reference` - Unique reference for Paystack
- `tracking_number` - Set to order_number for tracking

## Configuration

Check that `.env` has:

```env
PAYSTACK_PUBLIC_KEY=pk_xxx
PAYSTACK_SECRET_KEY=sk_xxx
MAIL_FROM_ADDRESS=your-email@example.com
```

Or set in admin settings UI.

## Common Issues

### Issue: Order created multiple times
- **Cause**: User double-clicked submit button
- **Solution**: Add JavaScript `disabled` state to button (already done in Livewire)

### Issue: Cart not clearing after checkout
- **Cause**: Session not configured properly
- **Fix**: Check `config/session.php` and `.env`

### Issue: Email not sending
- **Cause**: Mail driver set to 'log'
- **Solution**: Check `.env` MAIL_DRIVER setting
- Note: Logs to `storage/logs/` for testing

## Next Steps

1. ✅ Test full checkout flow
2. ✅ Verify Paystack integration
3. ✅ Test email confirmations
4. ✅ Check order tracking
5. ✅ Test payment retry
6. Deploy to production
