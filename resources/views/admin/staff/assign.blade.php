@extends('layouts.admin')

@section('title', 'Assign Staff Member')

@section('breadcrumb')
    <a href="{{ route('admin.staff.index') }}" class="text-red-600 hover:text-red-700">Staff Management</a>
    <span class="mx-2">/</span>
    <span class="text-gray-700">Assign New Staff</span>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-900">Assign New Staff Member</h2>
        <p class="text-gray-600 text-sm mt-2">Select a user and assign them a staff role with specific permissions</p>
    </div>

    <!-- Form Card -->
    <form method="POST" action="{{ route('admin.staff.store') }}" class="card-modern">
        @csrf

        <div class="p-6 space-y-6">
            <!-- User Selection -->
            <div x-data="{ 
                searchQuery: '', 
                selectedUserId: '',
                users: {{ json_encode($availableUsers->map(function($u) { return ['id' => $u->id, 'name' => $u->name, 'email' => $u->email, 'role' => $u->role]; })->values()) }},
                get filteredUsers() {
                    return this.users.filter(user => 
                        user.name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                        user.email.toLowerCase().includes(this.searchQuery.toLowerCase())
                    );
                },
                selectUser(userId, userName) {
                    this.selectedUserId = userId;
                    document.getElementById('user_id').value = userId;
                    this.searchQuery = '';
                }
            }">
                <label class="block text-sm font-semibold text-gray-900 mb-3">
                    Select User to Assign
                </label>

                @if($availableUsers->count() > 0)
                    <!-- Search Box -->
                    <div class="mb-4">
                        <div class="relative">
                            <svg class="absolute left-3 top-3 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <input 
                                type="text" 
                                x-model="searchQuery"
                                placeholder="Search by name or email..." 
                                class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all duration-200 outline-none">
                        </div>
                    </div>

                    <!-- Selected User Display -->
                    <template x-if="selectedUserId">
                        <div class="mb-4 p-4 rounded-xl bg-red-50 border border-red-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600">Selected User</p>
                                    <p class="text-lg font-semibold text-gray-900">
                                        <template x-for="user in users" :key="user.id">
                                            <span x-show="user.id == selectedUserId" x-text="user.name"></span>
                                        </template>
                                    </p>
                                    <p class="text-sm text-red-600">
                                        <template x-for="user in users" :key="user.id">
                                            <span x-show="user.id == selectedUserId" x-text="user.email"></span>
                                        </template>
                                    </p>
                                </div>
                                <button type="button" @click="selectedUserId = ''; document.getElementById('user_id').value = '';" class="text-gray-400 hover:text-gray-600 transition-colors">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </template>

                    <!-- Users List -->
                    <div class="border border-gray-200 rounded-xl overflow-hidden">
                        <div class="max-h-96 overflow-y-auto">
                            <template x-if="filteredUsers.length > 0">
                                <div class="divide-y divide-gray-200">
                                    <template x-for="user in filteredUsers" :key="user.id">
                                        <button 
                                            type="button"
                                            @click="selectUser(user.id, user.name)"
                                            :class="selectedUserId == user.id ? 'bg-red-50 border-l-4 border-red-600' : 'hover:bg-gray-50'"
                                            class="w-full text-left px-4 py-3 transition-colors duration-200 flex items-center justify-between">
                                            <div class="flex items-center space-x-3 flex-1">
                                                <div :class="selectedUserId == user.id ? 'from-red-500 to-red-600' : 'from-gray-400 to-gray-500'" class="w-10 h-10 bg-gradient-to-br rounded-lg flex items-center justify-center shadow-md flex-shrink-0">
                                                    <span class="text-white font-semibold text-sm" x-text="user.name.charAt(0).toUpperCase()"></span>
                                                </div>
                                                <div class="min-w-0">
                                                    <p class="text-sm font-semibold text-gray-900" x-text="user.name"></p>
                                                    <div class="flex items-center space-x-2">
                                                        <p class="text-xs text-gray-500 truncate" x-text="user.email"></p>
                                                        <span :class="user.role === 'customer' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800'" class="text-xs font-medium px-2 py-0.5 rounded-full whitespace-nowrap" x-text="user.role.charAt(0).toUpperCase() + user.role.slice(1)"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <template x-if="selectedUserId == user.id">
                                                <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                            </template>
                                        </button>
                                    </template>
                                </div>
                            </template>

                            <template x-if="filteredUsers.length === 0">
                                <div class="p-8 text-center">
                                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                    <p class="text-sm text-gray-500">No users found</p>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Hidden Input for Form Submission -->
                    <input type="hidden" id="user_id" name="user_id" x-model="selectedUserId">

                    @error('user_id')
                        <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                    @enderror
                @else
                    <div class="rounded-xl bg-amber-50 border border-amber-200 p-4">
                        <p class="text-amber-800 text-sm">
                            No available users to assign as staff. All users are either admins or already staff members.
                        </p>
                    </div>
                @endif
            </div>

            <!-- Staff Role Selection -->
            @if($availableUsers->count() > 0)
                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-4">
                        Select Staff Role
                    </label>
                    <div class="space-y-3">
                        <!-- Order Manager -->
                        <label class="flex items-start p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-red-300 hover:bg-red-50 transition-all duration-200"
                               :class="selectedRole === 'order_manager' ? 'border-red-500 bg-red-50' : ''">
                            <input type="radio" name="staff_role" value="order_manager" 
                                   @checked(old('staff_role') === 'order_manager')
                                   required
                                   class="mt-1 w-4 h-4 text-red-600 focus:ring-red-500">
                            <div class="ml-3">
                                <p class="text-sm font-semibold text-gray-900">📋 Order Manager</p>
                                <p class="text-sm text-gray-600 mt-1">
                                    Can view, manage, and update customer orders. Can update order status and payment information.
                                </p>
                                <div class="mt-2 text-xs text-gray-700 space-y-1">
                                    <p><span class="font-medium">✓ Permissions:</span> View orders, Update status, Process payments, View order details</p>
                                </div>
                            </div>
                        </label>

                        <!-- Product Manager -->
                        <label class="flex items-start p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-amber-300 hover:bg-amber-50 transition-all duration-200"
                               :class="selectedRole === 'product_manager' ? 'border-amber-500 bg-amber-50' : ''">
                            <input type="radio" name="staff_role" value="product_manager" 
                                   @checked(old('staff_role') === 'product_manager')
                                   required
                                   class="mt-1 w-4 h-4 text-amber-600 focus:ring-amber-500">
                            <div class="ml-3">
                                <p class="text-sm font-semibold text-gray-900">📦 Product Manager</p>
                                <p class="text-sm text-gray-600 mt-1">
                                    Can add, edit, delete and manage products. Can update inventory, pricing, and product details.
                                </p>
                                <div class="mt-2 text-xs text-gray-700 space-y-1">
                                    <p><span class="font-medium">✓ Permissions:</span> Create products, Edit details, Manage inventory, Update pricing</p>
                                </div>
                            </div>
                        </label>

                        <!-- All (Orders & Products) -->
                        <label class="flex items-start p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-purple-300 hover:bg-purple-50 transition-all duration-200"
                               :class="selectedRole === 'all' ? 'border-purple-500 bg-purple-50' : ''">
                            <input type="radio" name="staff_role" value="all" 
                                   @checked(old('staff_role') === 'all')
                                   required
                                   class="mt-1 w-4 h-4 text-purple-600 focus:ring-purple-500">
                            <div class="ml-3">
                                <p class="text-sm font-semibold text-gray-900">🔑 All (Orders & Products)</p>
                                <p class="text-sm text-gray-600 mt-1">
                                    Full access to both order and product management. Can manage everything except admin settings.
                                </p>
                                <div class="mt-2 text-xs text-gray-700 space-y-1">
                                    <p><span class="font-medium">✓ Permissions:</span> All Order + All Product permissions</p>
                                </div>
                            </div>
                        </label>
                    </div>
                    @error('staff_role')
                        <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Information Box -->
                <div class="rounded-xl bg-blue-50 border border-blue-200 p-4">
                    <div class="flex">
                        <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-blue-700">Important</p>
                            <p class="text-sm text-blue-600 mt-1">
                                Staff members will receive a notification about their new role. They will have limited access based on their assigned permissions.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex space-x-3 pt-4 border-t border-gray-200">
                    <button type="submit" class="btn-primary">
                        <svg class="w-4 h-4 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Assign Staff Member
                    </button>
                    <a href="{{ route('admin.staff.index') }}" class="btn-secondary">
                        Cancel
                    </a>
                </div>
            @else
                <!-- No Users Available - Show Back Button Only -->
                <div class="pt-4 border-t border-gray-200">
                    <a href="{{ route('admin.staff.index') }}" class="btn-secondary">
                        Back to Staff List
                    </a>
                </div>
            @endif
        </div>
    </form>
</div>

<!-- Alpine.js for radio button styling -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const radios = document.querySelectorAll('input[name="staff_role"]');
    
    radios.forEach(radio => {
        radio.addEventListener('change', function() {
            // Update visual feedback
            document.querySelectorAll('label').forEach(label => {
                const input = label.querySelector('input[type="radio"]');
                if (input && input.checked) {
                    label.classList.add('border-2');
                }
            });
        });
    });
});
</script>
@endsection
