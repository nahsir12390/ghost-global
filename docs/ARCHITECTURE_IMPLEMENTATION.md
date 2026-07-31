# Checkout & Payment Architecture - Complete Implementation

## Overview
The checkout and payment system has been completely redesigned to use:
- **Separate Controllers**: CheckoutController (order creation) + PaymentController (payment handling)
- **Callback-Based Payment**: Paystack uses callback redirect, not webhooks
- **Simple Native Form Submission**: No nested HTTP requests from Livewire
- **Clean Separation of Concerns**: Each component has single responsibility

## Flow Diagram

```
1. User fills checkout form
   ↓
2. Livewire validates locally → Native HTML form POST to /checkout
   ↓
3. CheckoutController@store creates Order + OrderItems in transaction
   ↓
4. Redirects to /payment/{order}
   ↓
5. PaymentController@index displays payment method selector
   ↓
6. User selects payment method → POST to /payment/{order}
   ↓
7. PaymentController@process routes to handler:
   - Paystack: Returns paystack form view with PaystackPop script
   - Cash on Delivery: Marks as processing, shows success page
   ↓
8. For Paystack: User completes payment on Paystack modal
   ↓
9. Paystack redirects to /payment/callback with reference parameter
   ↓
10. PaymentController@callback verifies payment via HTTP request to Paystack API
   ↓
11. If successful: Updates order status → Redirects to /payment/{order}/success
    If failed: Redirects to /payment/failed
```

## Key Improvements

### Timeout Issue Resolved
**Root Cause**: Old implementation had Livewire making HTTP POST requests to checkout endpoint, creating nested HTTP calls that caused timeout.

**Solution**: 
- Livewire only validates locally
- Native HTML form submission handles POST
- No HTTP requests from Livewire component

### Controllers Structure

#### CheckoutController (`app/Http/Controllers/CheckoutController.php`)
- `index()` - Display checkout form with cart validation
- `store()` - Create Order + OrderItems in single database transaction
  - Validates all shipping information
  - Creates Order with pending status
  - Creates OrderItems from cart
  - Decrements product quantities
  - Clears session cart
  - Redirects to payment.index with order
- `success()` - Display order confirmation (alternate endpoint)

**Removed Methods**: 
- `payment()`, `processPayment()`, all payment handlers
- `paystackCallback()`, `paystackWebhook()`
- ~300 lines of payment logic

#### PaymentController (`app/Http/Controllers/PaymentController.php`)
- `index(Order $order)` - Display payment method selection
- `process(Order $order, Request $request)` - Route to payment handler
- `payWithPaystack(Order $order)` - Return Paystack form view
- `payWithCOD(Order $order)` - Complete order immediately
- `callback(Request $request)` - Handle Paystack callback (user redirected from Paystack)
  - Verify payment via Paystack API
  - Update order status
  - Send confirmation emails
- `success(Order $order)` - Display payment confirmation page
- `failed()` - Display payment error page
- Helpers:
  - `getAvailablePaymentMethods()` - Check which methods are configured
  - `sendConfirmationEmail(Order $order)` - Send emails to user and admin

### Livewire Component Changes

#### Checkout.php (`app/Livewire/Checkout.php`)
**Original submitOrder()**: 80+ lines of HTTP request logic
```php
// OLD - PROBLEMATIC
public function submitOrder()
{
    // ... 80 lines of HTTP::post() requests, complex error handling
    Http::post(route('checkout.store'), [...])
}
```

**New submitOrder()**: 3 lines - only validates locally
```php
// NEW - SIMPLE
public function submitOrder()
{
    $this->validate();
    $this->dispatch('formValid');
}
```

### View Structure

1. **checkout.blade.php** - Main checkout page that includes Livewire component
2. **livewire/checkout.blade.php** - Livewire form component with fields
3. **payment/select.blade.php** - Payment method selector
4. **payment/paystack.blade.php** - Paystack payment form with PaystackPop integration
5. **payment/success.blade.php** - Order confirmation page
6. **payment/failed.blade.php** - Payment error page

### Routes Configuration

```php
// Checkout Routes (Auth Required)
GET  /checkout              → CheckoutController@index
POST /checkout              → CheckoutController@store
GET  /checkout/{order}/success → CheckoutController@success

// Payment Routes (Auth Required)
GET  /payment/{order}       → PaymentController@index
POST /payment/{order}       → PaymentController@process
GET  /payment/{order}/success → PaymentController@success
GET  /payment/failed        → PaymentController@failed

// Payment Callback (NO AUTH - External Paystack Redirect)
GET  /payment/callback      → PaymentController@callback
```

## Database & Models

### Order Model
- All fields present: `payment_status`, `payment_method`, `payment_reference`, `payment_id`
- Relationships: `hasMany(OrderItem)`, `belongsTo(User)`
- No migration changes needed

### OrderItem Model
- Correct structure with product tracking and totals
- Properly casted decimal fields
- Relationships: `belongsTo(Order)`, `belongsTo(Product)`

## Payment Methods

### Paystack Integration
- **Type**: Callback-based (no webhooks)
- **Flow**:
  1. User clicks "Pay with Paystack"
  2. PaystackPop modal opens (client-side)
  3. User enters payment details
  4. Paystack redirects to `/payment/callback` with reference
  5. Callback verifies payment via Paystack API
  6. Order status updated
  7. User redirected to success page

- **Configuration**: Via SettingsHelper
  - `paystackPublicKey()` - Public key for frontend
  - `paystackSecretKey()` - Secret key for API verification

### Cash on Delivery
- **Type**: Direct order completion
- **Flow**:
  1. User selects "Cash on Delivery"
  2. Order status immediately set to "processing"
  3. User redirected to success page
  4. Confirmation email sent

- **Configuration**: Via SettingsHelper
  - `isCashOnDeliveryEnabled()` - Whether COD is available

## Security Measures

1. **User Ownership Validation**: All routes check `order->user_id === Auth::id()`
2. **Payment Status Checks**: Only process pending orders
3. **Database Transactions**: Order creation is atomic
4. **CSRF Protection**: Native forms have @csrf
5. **Auth Middleware**: Checkout and payment selection require authentication
6. **Callback Validation**: Verifies payment with Paystack API before marking as paid

## Settings Integration

Uses `SettingsHelper` for all configuration:
```php
SettingsHelper::paystackPublicKey()
SettingsHelper::paystackSecretKey()
SettingsHelper::isCashOnDeliveryEnabled()
SettingsHelper::shippingFee()
SettingsHelper::freeShippingThreshold()
SettingsHelper::get('tax_rate', 7.5)
SettingsHelper::get('site_email')
```

## Email Notifications

Sent by PaymentController:
1. **OrderConfirmationMail** - To customer at `order->shipping_email`
2. **AdminOrderNotificationMail** - To admin at `site_email` setting

Sent when payment completes (for Paystack) or immediately (for COD).

## Testing Flow

1. **Add item to cart** → Navigate to checkout
2. **Fill shipping information** → Click "Place Order" or "Pay ₦X.XX"
3. **Review order** → Redirected to payment method selector
4. **Select payment method**:
   - **Paystack**: See payment form, complete payment, redirected to callback
   - **COD**: Immediately redirected to success page
5. **Verify order created** in database with correct status and items
6. **Verify stock decremented** in products table

## Caching & Performance

- Settings cached with 1-hour TTL
- Database transaction ensures atomic order creation
- No repeated HTTP requests for same order

## Error Handling

1. **Checkout Validation**: Form validation errors shown in checkout view
2. **Empty Cart**: Redirect to cart if empty
3. **Stock Issues**: Redirect to cart with specific out-of-stock message
4. **Payment Errors**: Redirect to payment.failed with error message
5. **Paystack Configuration**: User-friendly message if not configured

## Notes

- All Paystack test keys should be in database Settings
- COD can be enabled/disabled via settings
- Email driver configured in .env (log, mail, etc.)
- All timestamps tracked: created_at, updated_at
- Order numbers auto-generated via Order model
