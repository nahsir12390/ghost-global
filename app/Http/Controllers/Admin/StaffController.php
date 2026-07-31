<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    /**
     * Display a listing of all staff members
     */
    public function index()
    {
        $staff = User::where('is_staff', true)
            ->where('staff_deactivated_at', null)
            ->orderBy('staff_assigned_at', 'desc')
            ->paginate(15);

        return view('admin.staff.index', compact('staff'));
    }

    /**
     * Show the form to assign a new staff member
     */
    public function assign()
    {
        // Get all users who are not already staff members (exclude admins)
        $availableUsers = User::where('is_staff', false)
            ->where('is_admin', false)
            ->orderBy('name')
            ->get();

        return view('admin.staff.assign', compact('availableUsers'));
    }

    /**
     * Store a new staff assignment
     */
    public function storeAssignment(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id|integer',
            'staff_role' => 'required|in:order_manager,product_manager,all',
        ], [
            'user_id.required' => 'Please select a user to assign as staff',
            'user_id.exists' => 'The selected user does not exist',
            'staff_role.required' => 'Please select a staff role',
            'staff_role.in' => 'Invalid staff role selected',
        ]);

        try {
            $user = User::findOrFail($validated['user_id']);

            // Prevent assigning admin or already active staff
            if ($user->isAdmin() || $user->isStaff()) {
                return redirect()->back()
                    ->with('error', 'This user is already an admin or active staff member');
            }

            // Assign staff role
            $user->assignAsStaff($validated['staff_role']);

            return redirect()->route('admin.staff.index')
                ->with('success', ucfirst(str_replace('_', ' ', $validated['staff_role'])) . ' "' . $user->name . '" has been assigned successfully');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error assigning staff: ' . $e->getMessage());
        }
    }

    /**
     * Deactivate a staff member
     */
    public function deactivate(User $user)
    {
        try {
            if (!$user->isStaff()) {
                return redirect()->back()
                    ->with('error', 'This user is not an active staff member');
            }

            $staffRole = ucfirst(str_replace('_', ' ', $user->staff_role));
            $user->deactivateStaff();

            return redirect()->back()
                ->with('success', $user->name . ' has been deactivated from ' . $staffRole . ' role');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deactivating staff: ' . $e->getMessage());
        }
    }
}
