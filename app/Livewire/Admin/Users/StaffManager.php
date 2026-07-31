<?php

namespace App\Livewire\Admin\Users;

use App\Models\User;
use Livewire\Component;

class StaffManager extends Component
{
    public $user;
    public $selectedStaffRole = '';
    public $showConfirmDeactivate = false;

    public function mount(User $user)
    {
        abort_unless(auth()->user()->isAdmin(), 403);
        $this->user = $user;
    }

    public function assignAsStaff()
    {
        $this->validate([
            'selectedStaffRole' => 'required|in:order_manager,product_manager,all',
        ]);

        try {
            $this->user->assignAsStaff($this->selectedStaffRole);
            $this->selectedStaffRole = '';
            
            session()->flash('success', sprintf(
                '%s has been assigned as %s',
                $this->user->name,
                $this->getStaffRoleLabel($this->user->staff_role)
            ));
            
            $this->dispatch('staff-assigned');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to assign staff: ' . $e->getMessage());
        }
    }

    public function deactivateStaff()
    {
        try {
            $this->user->deactivateStaff();
            $this->showConfirmDeactivate = false;
            
            session()->flash('success', 'Staff member has been deactivated');
            $this->dispatch('staff-deactivated');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to deactivate staff: ' . $e->getMessage());
        }
    }

    public function getStaffRoleLabel($role)
    {
        return match($role) {
            'order_manager' => 'Order Manager',
            'product_manager' => 'Product Manager',
            'all' => 'All (Orders & Products)',
            default => 'Unknown'
        };
    }

    public function render()
    {
        return view('livewire.admin.users.staff-manager');
    }
}
