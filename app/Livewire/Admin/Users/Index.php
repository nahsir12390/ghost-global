<?php

namespace App\Livewire\Admin\Users;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $roleFilter = '';
    public $statusFilter = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $selectedUsers = [];
    public $selectAll = false;
    public $showDeleteModal = false;
    public $userIdToDelete = null;
    public array $summary = [];

    protected $queryString = [
        'search' => ['except' => ''],
        'roleFilter' => ['except' => ''],
        'statusFilter' => ['except' => ''],
    ];

    protected $listeners = [
        'userUpdated' => '$refresh',
        'confirm-delete' => 'confirmDeleteUser'
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingRoleFilter()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function mount(): void
    {
        $this->summary = $this->buildSummary();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function toggleSelectAll()
    {
        if ($this->selectAll) {
            $this->selectedUsers = $this->getQuery()->pluck('id')->toArray();
        } else {
            $this->selectedUsers = [];
        }
    }

    public function updatedSelectAll(): void
    {
        $this->toggleSelectAll();
    }

    public function updatedSelectedUsers(): void
    {
        $totalUsers = (clone $this->getQuery())->count();
        $this->selectAll = $totalUsers > 0 && count($this->selectedUsers) === $totalUsers;
    }

    public function confirmDelete()
    {
        $this->showDeleteModal = true;
    }

    public function confirmDeleteUser($id)
    {
        $this->userIdToDelete = $id;
        $this->showDeleteModal = true;
    }

    public function deleteSelected()
    {
        // Don't allow deletion of admin users
        $adminUsers = User::whereIn('id', $this->selectedUsers)
            ->where('is_admin', true)
            ->count();

        if ($adminUsers > 0) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Cannot delete admin users.'
            ]);
            $this->showDeleteModal = false;
            return;
        }

        User::whereIn('id', $this->selectedUsers)->delete();
        
        $this->selectedUsers = [];
        $this->selectAll = false;
        $this->showDeleteModal = false;
        
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Selected users deleted successfully!'
        ]);

        $this->summary = $this->buildSummary();
    }

    public function deleteUser()
    {
        if (!$this->userIdToDelete) {
            return;
        }

        $user = User::find($this->userIdToDelete);
        
        if (!$user) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'User not found.'
            ]);
            $this->showDeleteModal = false;
            return;
        }

        if ($user->is_admin) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Cannot delete admin users.'
            ]);
            $this->showDeleteModal = false;
            return;
        }

        $user->delete();
        
        $this->userIdToDelete = null;
        $this->showDeleteModal = false;
        $this->dispatch('userUpdated');
        
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'User deleted successfully!'
        ]);

        $this->summary = $this->buildSummary();
    }

    public function toggleAdminStatus($userId)
    {
        $user = User::find($userId);
        if ($user) {
            $user->update(['is_admin' => !$user->is_admin]);
            $this->dispatch('userUpdated');
            
            $status = $user->is_admin ? 'admin' : 'regular user';
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => "User updated to {$status}!"
            ]);

            $this->summary = $this->buildSummary();
        }
    }

    public function updateVendorVerification($userId, $status)
    {
        $user = User::where('is_admin', false)->find($userId);

        if (!$user || !$user->isVendor()) {
            return;
        }

        $user->update([
            'verification_status' => $status,
            'verified_at' => $status === 'approved' ? now() : null,
            'vendor_is_active' => $status === 'approved',
        ]);

        $this->dispatch('userUpdated');
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Vendor verification updated successfully!'
        ]);

        $this->summary = $this->buildSummary();
    }

    private function buildSummary(): array
    {
        $base = User::query()->where('is_admin', false);

        return [
            'total_users' => (clone $base)->count(),
            'customers' => (clone $base)->where('role', 'customer')->count(),
            'vendors' => (clone $base)->where('role', 'vendor')->count(),
            'pending_vendors' => (clone $base)->where('role', 'vendor')->where('verification_status', 'pending')->count(),
        ];
    }

    public function getQuery()
    {
        return User::query()
            ->withCount(['orders', 'products'])
            ->where('is_admin', false)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%')
                      ->orWhere('store_name', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->roleFilter, function ($query) {
                $query->where('role', $this->roleFilter);
            })
            ->when($this->statusFilter !== '', function ($query) {
                if ($this->statusFilter === 'verified') {
                    $query->where(function ($statusQuery) {
                        $statusQuery->whereNotNull('email_verified_at')
                            ->orWhere('verification_status', 'approved');
                    });
                    return;
                }

                if ($this->statusFilter === 'pending_vendor') {
                    $query->where('role', 'vendor')->where('verification_status', 'pending');
                    return;
                }

                if ($this->statusFilter === 'inactive_vendor') {
                    $query->where('role', 'vendor')->where('vendor_is_active', false);
                    return;
                }

                if ($this->statusFilter === 'unverified') {
                    $query->where(function ($statusQuery) {
                        $statusQuery->whereNull('email_verified_at')
                            ->orWhere(function ($vendorQuery) {
                                $vendorQuery->where('role', 'vendor')->where('verification_status', '!=', 'approved');
                            });
                    });
                }
            })
            ->orderBy($this->sortField, $this->sortDirection);
    }

    public function render()
    {
        return view('livewire.admin.users.index', [
            'users' => $this->getQuery()->paginate(15),
            'summary' => $this->summary,
            'selectedUsers' => $this->selectedUsers,
            'selectAll' => $this->selectAll,
            'showDeleteModal' => $this->showDeleteModal,
            'userIdToDelete' => $this->userIdToDelete,
            'sortField' => $this->sortField,
            'sortDirection' => $this->sortDirection,
            'search' => $this->search,
            'roleFilter' => $this->roleFilter,
            'statusFilter' => $this->statusFilter,
        ]);
    }
}
