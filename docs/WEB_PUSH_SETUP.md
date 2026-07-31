# Web Push Notifications Setup Guide

## Overview
Your e-commerce application now has Web Push Notification support integrated. This allows you to send real-time notifications to users' browsers for:
- New order confirmations
- Order status updates  
- Admin announcements

## Setup Steps

### Step 1: Generate VAPID Keys

VAPID keys are required for Web Push Protocol. Generate them using one of these methods:

#### Method A: Quick PHP Script (Recommended)
```bash
php generate-vapid-keys.php
```
Copy the keys displayed in your terminal.

#### Method B: Online Generator
If the PHP script doesn't work, use: https://github.com/web-push-libs/web-push/wiki/VAPID-Keys
Or try: https://vapid-key-generator.herokuapp.com/ (if available)

#### Method C: OpenSSL Commands
```bash
# If you have OpenSSL installed (comes with Git Bash on Windows)
openssl ecparam -name prime256v1 -genkey -noout | base64 | tr -d '\n' > private.txt
# Private key is now in private.txt
```

### Step 2: Configure Environment

Add the VAPID keys to your `.env` file:

```env
PUSH_PUBLIC_KEY=your_public_key_from_vapidkey
PUSH_PRIVATE_KEY=your_private_key_from_vapidkey
```

### Step 3: Run Migrations (Already Done)

The required database tables have already been created:
- `push_subscriptions` - Stores user push subscriptions
- `push_notifications` - Logs all push notifications sent

### Step 4: Clear Cache

```bash
php artisan config:cache
php artisan cache:clear
```

### Step 5: Build Assets

```bash
npm run build
```

## How It Works

### Frontend (Automatic)

When an authenticated user visits any page:
1. Service Worker is registered (`/service-worker.js`)
2. Browser requests permission for notifications (if not already granted)
3. User subscription is saved to the server
4. Ready to receive push notifications

### Backend (Automatic)

When orders are created or updated:
1. `OrderObserver` triggers automatically
2. Notifications are sent via `PushNotificationService`
3. Customer receives notification about their order
4. Admins receive notifications about new orders

## Features Implemented

### Notification Types

#### 1. New Order Confirmation
- **When**: Customer creates a new order
- **Recipients**: Customer + All Admins
- **Data**: Order number, total amount

#### 2. Order Status Updated  
- **When**: Admin updates order status
- **Recipients**: Customer who placed the order
- **Data**: New status, order number

#### 3. Custom Admin Messages
- Can send manual notifications via API:
  ```php
  $pushService = app(\App\Services\PushNotificationService::class);
  $pushService->sendToUser(
      $user,
      'Notification Title',
      'Notification Body',
      'admin_message',
      ['key' => 'value']
  );
  ```

### API Endpoints

All endpoints require authentication (`auth:api` middleware).

#### Subscribe to Notifications
```
POST /api/push/subscribe
Content-Type: application/json

{
  "endpoint": "https://fcm.googleapis.com/...",
  "publicKey": "...",
  "authToken": "..."
}
```

#### Unsubscribe
```
POST /api/push/unsubscribe

{
  "endpoint": "https://fcm.googleapis.com/..."
}
```

#### Check Subscription Status
```
GET /api/push/status
```

Response:
```json
{
  "subscribed": true,
  "count": 2
}
```

#### List All Subscriptions
```
GET /api/push/list
```

#### Remove Specific Subscription
```
POST /api/push/remove

{
  "subscription_id": 1
}
```

## Customizing Notifications

### Edit Notification Content

Edit [OrderObserver.php](../app/Observers/OrderObserver.php):

```php
$this->pushService->sendToUser(
    $order->user,
    'Custom Title', // Change this
    'Custom message: ' . $order->order_number, // Change this
    'order_received',
    ['order_id' => $order->id]
);
```

### Add New Notification Types

1. Create new event handler in Observer
2. Call `PushNotificationService::sendToUser()`
3. Define notification type string (e.g. 'new_product_alert')
4. Add URL route handling in `PushNotificationService::getNotificationUrl()`

Example:
```php
$this->pushService->sendToUser(
    $user,
    'New Product Available',
    'Check out our latest ' . $category->name,
    'new_product',
    ['product_id' => $product->id, 'category' => $category->name]
);
```

## Service Worker

The service worker (`/public/service-worker.js`) handles:
- Receiving push notifications from server
- Displaying notifications to user
- Handling notification clicks
- Opening relevant URLs when clicked
- Managing cache for offline support

### Notification Click Behavior

When user clicks a notification:
1. App opens in new window/tab if not already open
2. Navigates to the URL specified in notification data
3. For orders: opens the order tracking page

## Push Notification Service

Located at `App\Services\PushNotificationService`

### Key Methods

```php
// Send to single user
$service->sendToUser(
    User $user,
    string $title,
    string $body,
    string $type = 'general',
    ?array $data = null,
    ?string $icon = null,
    ?string $badge = null
);

// Send to multiple users
$service->sendToUsers(
    array $users,
    string $title,
    string $body,
    ...
);
```

## Database Tables

### push_subscriptions
- `user_id` - FK to users table
- `endpoint` - Browser push service endpoint
- `key_auth` - Authentication key for push
- `key_p256dh` - Encryption key  
- `user_agent` - Browser info
- `is_active` - Boolean, can be deactivated
- `created_at`, `updated_at`

### push_notifications
- `user_id` - FK to users (nullable for broadcasts)
- `type` - Notification type (order_received, order_updated, etc)
- `title` - Notification title
- `body` - Notification body text
- `icon`, `badge` - Images
- `data` - JSON extra data
- `status` - pending, sent, failed
- `attempts` - Number of send attempts
- `error_message` - If failed

## Browser Support

Web Push Notifications work in:
- ✅ Chrome/Edge (Desktop & Mobile)
- ✅ Firefox (Desktop & Mobile)
- ✅ Safari (macOS 16+, iOS 16.4+)
- ✅ Opera
- ❌ IE (not supported)

## Troubleshooting

### Service Worker Not Registering
- Check browser console for errors
- Verify HTTPS is used (required for service workers)
- Check that `/service-worker.js` is publicly accessible

### Notifications Not Showing
- Verify user allowed notification permission
- Check browser notification settings
- Inspect browser console for errors
- Verify `PUSH_PUBLIC_KEY` is set in `.env`

### Check Logs
```bash
tail -f storage/logs/laravel.log
```

## Security Notes

1. **VAPID Keys**: Keep `PUSH_PRIVATE_KEY` secret! Only use in backend.
2. **User Data**: Subscriptions are tied to specific users
3. **Endpoint Validation**: Subscriptions auto-deactivate if endpoint returns 410 (Gone)
4. **CSRF Protected**: All API endpoints require CSRF tokens

## Advanced Usage

### Send Broadcast Notification
```php
$users = User::where('is_admin', true)->get();
$service->sendToUsers($users, 'Alert', 'Something happened!');
```

### Schedule Notifications
```php
// In a command or scheduled job
dispatch(new SendPushNotification($user, $title, $body));
```

### Track Notification Delivery
```php
$sent = PushNotification::where('status', 'sent')->count();
$failed = PushNotification::where('status', 'failed')->count();
```

## Next Steps

1. Set up VAPID keys at https://vapidkey.com/
2. Add keys to `.env` file
3. Run `php artisan config:cache`
4. Test by placing an order - you should get a browser notification!

---

**Note**: The system is production-ready. For large-scale deployments, consider:
- Using a queue worker to handle notification sending
- Setting up batch notification processing
- Monitoring subscription health
- Implementing notification analytics

