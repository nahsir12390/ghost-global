<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\VendorVerificationStatusChangedMail;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::where('is_admin', false)
            ->withCount(['orders', 'products'])
            ->latest()
            ->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function export(Request $request)
    {
        $roleFilter = $request->string('role')->toString();
        $statusFilter = $request->string('status')->toString();
        $search = trim($request->string('search')->toString());

        $filenameParts = ['users-export', now()->format('Y-m-d_H-i')];

        if ($roleFilter !== '') {
            $filenameParts[] = $roleFilter;
        }

        $filename = implode('-', $filenameParts) . '.xls';

        $users = User::query()
            ->where('is_admin', false)
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('store_name', 'like', "%{$search}%")
                        ->orWhere('verification_phone', 'like', "%{$search}%")
                        ->orWhere('verification_email', 'like', "%{$search}%");
                });
            })
            ->when($roleFilter !== '', function ($query) use ($roleFilter) {
                $query->where('role', $roleFilter);
            })
            ->when($statusFilter !== '', function ($query) use ($statusFilter) {
                if ($statusFilter === 'verified') {
                    $query->where(function ($statusQuery) {
                        $statusQuery->whereNotNull('email_verified_at')
                            ->orWhere('verification_status', 'approved');
                    });

                    return;
                }

                if ($statusFilter === 'pending_vendor') {
                    $query->where('role', 'vendor')->where('verification_status', 'pending');

                    return;
                }

                if ($statusFilter === 'inactive_vendor') {
                    $query->where('role', 'vendor')->where('vendor_is_active', false);

                    return;
                }

                if ($statusFilter === 'unverified') {
                    $query->where(function ($statusQuery) {
                        $statusQuery->whereNull('email_verified_at')
                            ->orWhere(function ($vendorQuery) {
                                $vendorQuery->where('role', 'vendor')->where('verification_status', '!=', 'approved');
                            });
                    });
                }
            })
            ->orderBy('role')
            ->orderBy('name')
            ->get([
                'name',
                'email',
                'phone',
                'role',
                'store_name',
                'address',
                'verification_status',
                'vendor_is_active',
                'verification_email',
                'verification_phone',
                'bank_name',
                'bank_account_name',
                'bank_account_number',
                'created_at',
            ]);

        $summary = [
            'total' => $users->count(),
            'customers' => $users->where('role', 'customer')->count(),
            'vendors' => $users->where('role', 'vendor')->count(),
            'active_vendors' => $users->where('role', 'vendor')->where('vendor_is_active', true)->count(),
        ];

        $content = view('admin.users.export', [
            'users' => $users,
            'summary' => $summary,
            'roleFilter' => $roleFilter,
            'statusFilter' => $statusFilter,
            'search' => $search,
            'generatedAt' => now(),
        ])->render();

        return response($content, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0, no-cache, no-store, must-revalidate',
            'Pragma' => 'public',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        if ($user->isVendor()) {
            $user->load(['products' => function ($query) {
                $query->latest()->take(10);
            }]);

            $totalOrders = $user->vendorOrderItems()->distinct('order_id')->count('order_id');
            $totalSpent = $user->vendorOrderItems()
                ->whereHas('order', fn ($query) => $query->where('payment_status', 'paid'))
                ->sum('total');
        } else {
            $user->load(['orders' => function ($query) {
                $query->latest()->take(10);
            }]);

            $totalOrders = $user->orders()->count();
            $totalSpent = $user->orders()->where('payment_status', 'paid')->sum('total');
        }

        return view('admin.users.show', compact('user', 'totalOrders', 'totalSpent'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'store_name' => 'nullable|string|max:255',
            'verification_nin' => 'nullable|string|max:255',
            'verification_email' => 'nullable|email|max:255',
            'verification_phone' => 'nullable|string|max:20',
            'verification_notes' => 'nullable|string|max:1000',
            'vendor_is_active' => 'nullable|boolean',
        ]);

        if (! $user->isVendor()) {
            $validated['store_name'] = null;
            $validated['verification_nin'] = null;
            $validated['verification_email'] = null;
            $validated['verification_phone'] = null;
            $validated['verification_notes'] = null;
            unset($validated['vendor_is_active']);
        } else {
            $validated['vendor_is_active'] = $request->boolean('vendor_is_active');
        }

        $user->update($validated);

        return redirect()->route('admin.users.show', $user)
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Don't allow deletion of admin users
        if ($user->isAdmin()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Cannot delete admin user.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }

    /**
     * Toggle user active status.
     */
    public function toggleStatus(Request $request, User $user)
    {
        $request->validate([
            'status' => 'required|boolean',
        ]);

        // For Breeze, we don't have an is_active field, but we can add one
        // Or you can implement a ban system
        // For now, we'll just update email_verified_at for demonstration
        
        if ($request->status) {
            $user->update(['email_verified_at' => now()]);
        } else {
            $user->update(['email_verified_at' => null]);
        }

        return response()->json([
            'success' => true,
            'message' => 'User status updated successfully.',
            'status' => $request->status,
        ]);
    }

    /**
     * View user orders.
     */
    public function orders(User $user)
    {
        if ($user->isVendor()) {
            $orders = Order::visibleTo($user)
                ->with(['items' => function ($query) use ($user) {
                    $query->where('vendor_id', $user->id);
                }])
                ->latest()
                ->paginate(15);
        } else {
            $orders = $user->orders()
                ->with('items')
                ->latest()
                ->paginate(15);
        }

        return view('admin.users.orders', compact('user', 'orders'));
    }

    public function updateVerification(Request $request, User $user)
    {
        abort_unless($user->isVendor(), 404);

        $validated = $request->validate([
            'verification_status' => 'nullable|required_without:bank_verification_status|in:pending,approved,rejected',
            'bank_verification_status' => 'nullable|required_without:verification_status|in:pending,verified,rejected',
            'verification_notes' => 'nullable|string|max:1000',
        ]);

        $oldStatus = $user->verification_status;
        $newStatus = $validated['verification_status'] ?? null;
        $updateData = [];

        if ($newStatus !== null) {
            $updateData['verification_status'] = $newStatus;
            $updateData['verification_notes'] = $validated['verification_notes'] ?? null;
            $updateData['verified_at'] = $newStatus === 'approved' ? now() : null;
        }

        if (isset($validated['bank_verification_status'])) {
            $updateData['bank_verification_status'] = $validated['bank_verification_status'];
            $updateData['bank_verified_at'] = $validated['bank_verification_status'] === 'verified' ? now() : null;
        }

        $user->update($updateData);

        if ($newStatus !== null && $oldStatus !== $newStatus) {
            try {
                Mail::to($user->email)->send(
                    new VendorVerificationStatusChangedMail(
                        $user,
                        $newStatus,
                        $validated['verification_notes'] ?? null
                    )
                );
                Log::info('Vendor verification status email sent', [
                    'vendor_id' => $user->id,
                    'vendor_email' => $user->email,
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to send vendor verification status email', [
                    'vendor_id' => $user->id,
                    'vendor_email' => $user->email,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $message = $newStatus !== null
            ? 'Vendor verification updated successfully.'
            : 'Vendor bank verification updated successfully.';

        return redirect()->route('admin.users.show', $user)
            ->with('success', $message);
    }
}
