# Staff Management Implementation Guide

## Setup Instructions

### 1. Run the Migration

After creating the migration file, run:

```bash
php artisan migrate
```

This will add the following columns to the `users` table:
- `staff_role`: enum (order_manager, product_manager, all)
- `staff_assigned_at`: timestamp
- `staff_deactivated_at`: timestamp  
- `is_staff`: boolean index

---

## 2. Register Middleware in `bootstrap/app.php`

Add these middleware to the middleware aliases:

```php
'staff.only' => \App\Http\Middleware\StaffOnly::class,
'staff.orders' => \App\Http\Middleware\CanManageOrders::class,
'staff.products' => \App\Http\Middleware\CanManageProducts::class,
```

---

## 3. Workflow

### Step 1: User Registration
- User registers as normal customer through public registration
- User gets `role = 'customer'` and `is_staff = false`

### Step 2: Admin Assigns Staff Role
- Admin navigates to user management panel
- Admin clicks "Make Staff" button
- Admin selects staff role:
  - **Order Manager**: Can only manage orders
  - **Product Manager**: Can only manage products  
  - **All**: Can manage both orders and products
- System updates user fields:
  - `is_staff = true`
  - `staff_role = selected_role`
  - `staff_assigned_at = now()`

### Step 3: Staff Access Dashboard
- Staff member logs in
- Can only access permitted sections based on role
- Order managers access order management
- Product managers access product management

---

## 4. User Model Methods

### Checking Staff Status

```php
$user = auth()->user();

// Basic staff checks
$user->isStaff();                    // Is user active staff?
$user->isOrderManager();              // Can manage orders?
$user->isProductManager();            // Can manage products?

// Permission checks
$user->canManageOrders();             // Admin or order manager?
$user->canManageProducts();           // Admin or product manager?
$user->canManageBoth();               // Admin or all roles?
```

### Assigning/Removing Staff

```php
// Assign user as staff
$user->assignAsStaff('order_manager');   // or 'product_manager' or 'all'

// Deactivate staff
$user->deactivateStaff();

// Check if active
if ($user->isStaff()) {
    // Staff can access...
}
```

---

## 5. Route Protection

### Basic Staff-Only Routes

```php
// In routes/web.php or routes/admin.php
Route::middleware(['auth', 'staff.only'])->group(function () {
    Route::get('/staff/dashboard', [StaffDashboardController::class, 'show']);
});
```

### Order Management Routes

```php
Route::middleware(['auth', 'staff.orders'])->group(function () {
    Route::get('/staff/orders', [StaffOrderController::class, 'index']);
    Route::get('/staff/orders/{order}', [StaffOrderController::class, 'show']);
    Route::post('/staff/orders/{order}/status', [StaffOrderController::class, 'updateStatus']);
});
```

### Product Management Routes

```php
Route::middleware(['auth', 'staff.products'])->group(function () {
    Route::get('/staff/products', [StaffProductController::class, 'index']);
    Route::get('/staff/products/create', [StaffProductController::class, 'create']);
    Route::post('/staff/products', [StaffProductController::class, 'store']);
    Route::get('/staff/products/{product}/edit', [StaffProductController::class, 'edit']);
    Route::put('/staff/products/{product}', [StaffProductController::class, 'update']);
});
```

---

## 6. Admin Panel Integration

### In Users Show View

Add a staff management section to `resources/views/livewire/admin/users/show.blade.php`:

```blade
@if(!$user->isVendor())
    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
        <h3 class="text-lg font-semibold text-gray-900">Staff Management</h3>
        
        @if($user->isStaff())
            <div class="mt-4 space-y-3">
                <div class="rounded-xl bg-blue-50 p-4">
                    <p class="text-sm font-semibold text-blue-900">
                        Staff Role: 
                        <span class="font-bold">
                            @if($user->staff_role === 'order_manager')
                                Order Manager
                            @elseif($user->staff_role === 'product_manager')
                                Product Manager
                            @else
                                All (Orders & Products)
                            @endif
                        </span>
                    </p>
                    <p class="mt-2 text-xs text-blue-700">
                        Assigned: {{ $user->staff_assigned_at?->format('M d, Y H:i') }}
                    </p>
                </div>
                
                <button wire:click="deactivateStaff" 
                        class="w-full rounded-xl border border-red-300 px-4 py-2.5 text-sm font-semibold text-red-700 hover:bg-red-50">
                    Deactivate Staff
                </button>
            </div>
        @else
            <div class="mt-4 space-y-3">
                <select wire:model="selectedStaffRole" class="w-full rounded-xl border border-gray-300 px-4 py-2.5">
                    <option value="">Select Staff Role</option>
                    <option value="order_manager">Order Manager</option>
                    <option value="product_manager">Product Manager</option>
                    <option value="all">All (Orders & Products)</option>
                </select>
                
                <button wire:click="assignAsStaff" 
                        :disabled="!selectedStaffRole"
                        class="w-full rounded-xl border border-blue-300 px-4 py-2.5 text-sm font-semibold text-blue-700 hover:bg-blue-50 disabled:opacity-50">
                    Assign as Staff
                </button>
            </div>
        @endif
    </div>
@endif
```

### Livewire Component Methods

In your user show component:

```php
public $selectedStaffRole = '';

public function assignAsStaff()
{
    $this->user->assignAsStaff($this->selectedStaffRole);
    $this->selectedStaffRole = '';
    session()->flash('success', 'User assigned as ' . $this->selectedStaffRole);
}

public function deactivateStaff()
{
    $this->user->deactivateStaff();
    session()->flash('success', 'Staff member deactivated');
}
```

---

## 7. Database Queries

### Get Active Staff Members

```php
$activeStaff = User::where('is_staff', true)
    ->whereNull('staff_deactivated_at')
    ->get();
```

### Get Order Managers

```php
$orderManagers = User::where('is_staff', true)
    ->whereIn('staff_role', ['order_manager', 'all'])
    ->whereNull('staff_deactivated_at')
    ->get();
```

### Get Product Managers

```php
$productManagers = User::where('is_staff', true)
    ->whereIn('staff_role', ['product_manager', 'all'])
    ->whereNull('staff_deactivated_at')
    ->get();
```

### Get Recently Deactivated Staff

```php
$recentlyDeactivated = User::where('is_staff', false)
    ->whereNotNull('staff_deactivated_at')
    ->whereDate('staff_deactivated_at', '>=', now()->subDays(30))
    ->get();
```

---

## 8. Seeding Staff Members (Optional)

Create a seeder for testing:

```php
// In database/seeders/UserSeeder.php
$staffUser = User::create([
    'name' => 'John Order Manager',
    'email' => 'order.manager@keffi.com',
    'password' => Hash::make('password'),
    'role' => 'customer',
]);

$staffUser->assignAsStaff('order_manager');

$productManager = User::create([
    'name' => 'Jane Product Manager',
    'email' => 'product.manager@keffi.com',
    'password' => Hash::make('password'),
    'role' => 'customer',
]);

$productManager->assignAsStaff('product_manager');
```

---

## 9. API Usage Examples

### Check Staff Permissions in Controller

```php
namespace App\Http\Controllers;

class StaffOrderController extends Controller
{
    public function index()
    {
        // Middleware already verified user can manage orders
        $user = auth()->user();
        
        if ($user->isAdmin()) {
            $orders = Order::all();
        } else {
            // Staff sees only their assigned orders
            // (implement your business logic)
            $orders = Order::all();
        }
        
        return view('staff.orders.index', compact('orders'));
    }
}
```

### Authorization in Livewire Component

```php
namespace App\Livewire;

class OrderManager extends Component
{
    public function mount()
    {
        abort_unless(
            auth()->user()->canManageOrders(),
            403,
            'Unauthorized'
        );
    }
}
```

---

## 10. Key Features Summary

| Feature | Admin | Order Manager | Product Manager | All Roles |
|---------|-------|---------------|-----------------|-----------|
| Manage Users | ✓ | ✗ | ✗ | ✗ |
| Manage Orders | ✓ | ✓ | ✗ | ✓ |
| Manage Products | ✓ | ✗ | ✓ | ✓ |
| Dashboard | ✓ | ✓ | ✓ | ✓ |
| Deactivate Staff | ✓ | ✗ | ✗ | ✗ |

---

## 11. Next Steps

1. ✅ Run migration: `php artisan migrate`
2. ✅ Register middleware in `bootstrap/app.php`
3. ✅ Update your routes with middleware protection
4. ✅ Add staff assignment UI to admin users panel
5. ✅ Create staff dashboard and controllers
6. ✅ Test with different user roles
