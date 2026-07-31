<?php

namespace App\Livewire\Admin\Users;

use App\Models\Order;
use App\Models\User;
use Livewire\Component;

class Show extends Component
{
    public User $user;

    #[\Livewire\Attributes\Validate('required|string|max:255')]
    public string $name = '';

    #[\Livewire\Attributes\Validate('required|email')]
    public string $email = '';

    #[\Livewire\Attributes\Validate('nullable|string|max:20')]
    public string $phone = '';

    #[\Livewire\Attributes\Validate('nullable|string')]
    public string $address = '';

    #[\Livewire\Attributes\Validate('nullable|string|max:255')]
    public string $store_name = '';

    public bool $vendor_is_active = true;

    #[\Livewire\Attributes\Validate('nullable|string|max:255')]
    public string $verification_nin = '';

    #[\Livewire\Attributes\Validate('nullable|email|max:255')]
    public string $verification_email = '';

    #[\Livewire\Attributes\Validate('nullable|string|max:20')]
    public string $verification_phone = '';

    #[\Livewire\Attributes\Validate('nullable|string|max:1000')]
    public string $verification_notes = '';

    #[\Livewire\Attributes\Validate('nullable|string|max:100')]
    public string $bank_name = '';

    #[\Livewire\Attributes\Validate('nullable|string|max:100')]
    public string $bank_account_name = '';

    #[\Livewire\Attributes\Validate('nullable|string|min:10|max:34')]
    public string $bank_account_number = '';

    #[\Livewire\Attributes\Validate('nullable|string')]
    public string $bank_verification_status = 'pending';

    #[\Livewire\Attributes\Validate('nullable|date')]
    public ?string $bank_verified_at = null;

    public function mount(User $user): void
    {
        $this->user = $user->fresh();
        $this->name = $this->user->name ?? '';
        $this->email = $this->user->email ?? '';
        $this->phone = $this->user->phone ?? '';
        $this->address = $this->user->address ?? '';
        $this->store_name = $this->user->store_name ?? '';
        $this->vendor_is_active = $this->user->vendor_is_active ?? true;
        $this->verification_nin = $this->user->verification_nin ?? '';
        $this->verification_email = $this->user->verification_email ?? '';
        $this->verification_phone = $this->user->verification_phone ?? '';
        $this->verification_notes = $this->user->verification_notes ?? '';
        $this->bank_name = $this->user->bank_name ?? '';
        $this->bank_account_name = $this->user->bank_account_name ?? '';
        $this->bank_account_number = $this->user->bank_account_number ?? '';
        $this->bank_verification_status = $this->user->bank_verification_status ?? 'pending';
        $this->bank_verified_at = $this->user->bank_verified_at ? $this->user->bank_verified_at->format('Y-m-d\TH:i') : null;
    }

    public function update(): void
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $this->user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'store_name' => 'nullable|string|max:255',
            'vendor_is_active' => 'boolean',
            'verification_nin' => 'nullable|string|max:255',
            'verification_email' => 'nullable|email|max:255',
            'verification_phone' => 'nullable|string|max:20',
            'verification_notes' => 'nullable|string|max:1000',
            'bank_name' => 'nullable|string|max:100',
            'bank_account_name' => 'nullable|string|max:100',
            'bank_account_number' => 'nullable|string|min:10|max:34',
            'bank_verification_status' => 'nullable|string|in:pending,verified,rejected',
            'bank_verified_at' => 'nullable|date',
        ];

        $this->validate($rules);

        $updateData = [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
        ];

        if ($this->user->isVendor()) {
            $updateData['store_name'] = $this->store_name;
            $updateData['vendor_is_active'] = $this->vendor_is_active;
            $updateData['verification_nin'] = $this->verification_nin;
            $updateData['verification_email'] = $this->verification_email;
            $updateData['verification_phone'] = $this->verification_phone;
            $updateData['verification_notes'] = $this->verification_notes;
            $updateData['bank_name'] = $this->bank_name;
            $updateData['bank_account_name'] = $this->bank_account_name;
            $updateData['bank_account_number'] = $this->bank_account_number;
            $updateData['bank_verification_status'] = $this->bank_verification_status;
            $updateData['bank_verified_at'] = $this->bank_verified_at ? \Carbon\Carbon::createFromFormat('Y-m-d\TH:i', $this->bank_verified_at) : null;
        }

        $this->user->update($updateData);
        $this->user->refresh();

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'User updated successfully!',
        ]);
    }

    public function updatedBankVerificationStatus(string $value): void
    {
        $this->bank_verified_at = $value === 'verified' ? now()->format('Y-m-d\TH:i') : null;
    }

    public function toggleAdminStatus(): void
    {
        $this->user->update(['is_admin' => ! $this->user->is_admin]);
        $this->user->refresh();

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => $this->user->is_admin ? 'User promoted to admin successfully!' : 'Admin role removed successfully!',
        ]);
    }

    public function render()
    {
        if ($this->user->isVendor()) {
            $recentOrders = Order::visibleTo($this->user)
                ->with(['items' => fn ($query) => $query->where('vendor_id', $this->user->id)])
                ->latest()
                ->take(10)
                ->get();

            $stats = [
                'total_orders' => $this->user->vendorOrderItems()->distinct('order_id')->count('order_id'),
                'total_spent' => $this->user->vendorOrderItems()->whereHas('order', fn ($query) => $query->where('payment_status', 'paid'))->sum('total'),
                'pending_orders' => Order::visibleTo($this->user)->where('status', 'pending')->count(),
                'completed_orders' => Order::visibleTo($this->user)->where('status', 'completed')->count(),
                'wallet_balance' => (float) optional($this->user->wallet)->balance,
                'wallet_credits' => (float) $this->user->walletTransactions()->where('direction', 'credit')->where('status', 'completed')->sum('amount'),
                'wallet_debits' => (float) $this->user->walletTransactions()->where('direction', 'debit')->where('status', 'completed')->sum('amount'),
                'referrals_count' => $this->user->referrals()->count(),
                'rewarded_referrals_count' => $this->user->referrals()->whereNotNull('referral_rewarded_at')->count(),
            ];
        } else {
            $this->user->load(['orders' => function ($query) {
                $query->latest()->take(10);
            }]);

            $recentOrders = $this->user->orders;

            $stats = [
                'total_orders' => $this->user->orders()->count(),
                'total_spent' => $this->user->orders()->where('payment_status', 'paid')->sum('total'),
                'pending_orders' => $this->user->orders()->where('status', 'pending')->count(),
                'completed_orders' => $this->user->orders()->where('status', 'completed')->count(),
                'wallet_balance' => (float) optional($this->user->wallet)->balance,
                'wallet_credits' => (float) $this->user->walletTransactions()->where('direction', 'credit')->where('status', 'completed')->sum('amount'),
                'wallet_debits' => (float) $this->user->walletTransactions()->where('direction', 'debit')->where('status', 'completed')->sum('amount'),
                'referrals_count' => $this->user->referrals()->count(),
                'rewarded_referrals_count' => $this->user->referrals()->whereNotNull('referral_rewarded_at')->count(),
            ];
        }

        return view('livewire.admin.users.show', [
            'user' => $this->user,
            'stats' => $stats,
            'recentOrders' => $recentOrders,
            'recentWalletTransactions' => $this->user->walletTransactions()->latest()->take(5)->get(),
        ]);
    }
}
