<div>
    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Total Users</p>
            <p class="mt-3 text-3xl font-bold text-slate-900">{{ $summary['total_users'] ?? 0 }}</p>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Customers</p>
            <p class="mt-3 text-3xl font-bold text-blue-700">{{ $summary['customers'] ?? 0 }}</p>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Vendors</p>
            <p class="mt-3 text-3xl font-bold text-violet-700">{{ $summary['vendors'] ?? 0 }}</p>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Pending Vendors</p>
            <p class="mt-3 text-3xl font-bold text-amber-600">{{ $summary['pending_vendors'] ?? 0 }}</p>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="mb-6">
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
            <!-- Search -->
            <div class="w-full xl:max-w-md">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input wire:model.live.debounce.500ms="search" 
                           type="search" 
                           placeholder="Search users or stores..."
                           class="pl-10 pr-4 py-3 border border-slate-200 rounded-2xl w-full focus:ring-2 focus:ring-red-500 focus:border-red-500">
                </div>
            </div>

            <div class="grid w-full gap-4 sm:grid-cols-2 xl:max-w-xl xl:grid-cols-3">
                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">Account Type</label>
                    <select wire:model.change="roleFilter" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                        <option value="">All Users</option>
                        <option value="customer">Customers</option>
                        <option value="vendor">Vendors</option>
                    </select>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">Status</label>
                    <select wire:model.change="statusFilter" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                        <option value="">Any Status</option>
                        <option value="verified">Verified</option>
                        <option value="unverified">Needs Review</option>
                        <option value="pending_vendor">Pending Vendors</option>
                        <option value="inactive_vendor">Inactive Vendors</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <div class="grid w-full gap-3 sm:grid-cols-2 xl:grid-cols-1">
                        <a
                            href="{{ route('admin.users.export', ['role' => $roleFilter, 'status' => $statusFilter, 'search' => $search]) }}"
                            class="inline-flex w-full items-center justify-center rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700 transition hover:bg-emerald-100"
                        >
                            Export Current View
                        </a>
                        <a
                            href="{{ route('admin.users.export', ['role' => 'vendor']) }}"
                            class="inline-flex w-full items-center justify-center rounded-2xl border border-violet-200 bg-violet-50 px-4 py-3 text-sm font-semibold text-violet-700 transition hover:bg-violet-100"
                        >
                            Export Vendors
                        </a>
                    </div>
                </div>

            <!-- Actions -->
            <div class="flex items-end">
                @isset($selectedUsers)
                    @if(is_array($selectedUsers) && count($selectedUsers) > 0)
                        <button wire:click="confirmDelete"
                                class="inline-flex w-full items-center justify-center px-4 py-3 border border-red-300 rounded-2xl shadow-sm text-sm font-medium text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500">
                            <svg class="-ml-1 mr-2 h-5 w-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Delete Selected ({{ count($selectedUsers) }})
                        </button>
                    @else
                        <div class="rounded-2xl border border-dashed border-slate-200 px-4 py-3 text-center text-sm text-slate-500 w-full">
                            Bulk actions appear when you select users
                        </div>
                    @endif
                @endisset
            </div>
        </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <div class="flex items-center">
                                <input type="checkbox" 
                                       wire:model.change="selectAll"
                                       @isset($selectAll) @if($selectAll) checked @endif @endisset
                                       class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <button wire:click="sortBy('name')" class="flex items-center space-x-1">
                                <span>User</span>
                                @isset($sortField)@if($sortField === 'name')
                                    <svg class="w-4 h-4 {{ isset($sortDirection) && $sortDirection === 'asc' ? 'rotate-180' : '' }}">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                    </svg>
                                @endif@endisset
                            </button>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <button wire:click="sortBy('email')" class="flex items-center space-x-1">
                                <span>Email</span>
                                @isset($sortField)@if($sortField === 'email')
                                    <svg class="w-4 h-4 {{ isset($sortDirection) && $sortDirection === 'asc' ? 'rotate-180' : '' }}">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                    </svg>
                                @endif@endisset
                            </button>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Orders
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <button wire:click="sortBy('created_at')" class="flex items-center space-x-1">
                                <span>Joined</span>
                                @isset($sortField)@if($sortField === 'created_at')
                                    <svg class="w-4 h-4 {{ isset($sortDirection) && $sortDirection === 'asc' ? 'rotate-180' : '' }}">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                    </svg>
                                @endif@endisset
                            </button>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50">
                            <!-- Checkbox -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="checkbox" 
                                       wire:model.change="selectedUsers"
                                       value="{{ $user->id }}"
                                       class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                            </td>

                            <!-- User Name -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-red-100 flex items-center justify-center">
                                            <span class="text-red-600 font-medium">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            <a href="{{ route('admin.users.show', $user) }}" 
                                               class="text-gray-900 hover:text-red-600">
                                                {{ $user->name }}
                                            </a>
                                        </div>
                                        <div class="mt-1 flex items-center gap-2">
                                            @if($user->isVendor())
                                                <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800">Vendor</span>
                                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $user->verification_status === 'approved' ? 'bg-green-100 text-green-800' : ($user->verification_status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800') }}">
                                                    {{ ucfirst($user->verification_status ?? 'pending') }}
                                                </span>
                                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $user->isVendorActive() ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ $user->isVendorActive() ? 'Active' : 'Inactive' }}
                                                </span>
                                                @if($user->bank_account_number && $user->bank_verification_status !== 'verified')
                                                    <span class="inline-flex items-center rounded-full bg-violet-100 px-2.5 py-0.5 text-xs font-medium text-violet-800">
                                                        {{ $user->bank_verification_status === 'rejected' ? '⚠ Bank Rejected' : '⏳ Bank Pending' }}
                                                    </span>
                                                @elseif($user->bank_verification_status === 'verified')
                                                    <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">
                                                        ✓ Bank Verified
                                                    </span>
                                                @endif
                                            @else
                                                <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-700">Customer</span>
                                            @endif
                                        </div>
                                        @if($user->isVendor() && $user->store_name)
                                            <div class="text-sm text-gray-500">{{ $user->store_name }}</div>
                                        @elseif($user->phone)
                                            <div class="text-sm text-gray-500">{{ $user->phone }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Email -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $user->email }}</div>
                            </td>

                            <!-- Orders -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $user->isVendor() ? $user->products_count : $user->orders_count }}</div>
                                <div class="text-xs text-gray-500">{{ $user->isVendor() ? 'products' : 'orders' }}</div>
                            </td>

                            <!-- Joined Date -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $user->created_at->format('M d, Y') }}
                            </td>

                            <!-- Status -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($user->isVendor())
                                    @php($vendorStatus = $user->verification_status ?? 'pending')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $vendorStatus === 'approved' ? 'bg-green-100 text-green-800' : ($vendorStatus === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800') }}">
                                        <svg class="mr-1.5 h-2 w-2 {{ $vendorStatus === 'approved' ? 'text-green-400' : ($vendorStatus === 'rejected' ? 'text-red-400' : 'text-amber-400') }}" fill="currentColor" viewBox="0 0 8 8">
                                            <circle cx="4" cy="4" r="3" />
                                        </svg>
                                        {{ $vendorStatus === 'approved' ? 'Verified Vendor' : ($vendorStatus === 'rejected' ? 'Declined' : 'Pending Review') }}
                                    </span>
                                @elseif($user->email_verified_at)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <svg class="mr-1.5 h-2 w-2 text-green-400" fill="currentColor" viewBox="0 0 8 8">
                                            <circle cx="4" cy="4" r="3" />
                                        </svg>
                                        Verified
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <svg class="mr-1.5 h-2 w-2 text-yellow-400" fill="currentColor" viewBox="0 0 8 8">
                                            <circle cx="4" cy="4" r="3" />
                                        </svg>
                                        Unverified
                                    </span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2">
                                    <!-- View -->
                                    <a href="{{ route('admin.users.show', $user) }}" 
                                       class="text-gray-600 hover:text-gray-900 p-1">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                    
                                    <!-- Edit -->
                                    <a href="{{ route('admin.users.edit', $user) }}" 
                                       class="text-blue-600 hover:text-blue-900 p-1">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>

                                    <!-- Toggle Admin Status -->
                                    <button wire:click="toggleAdminStatus({{ $user->id }})"
                                            class="text-purple-600 hover:text-purple-900 p-1"
                                            title="{{ $user->is_admin ? 'Remove admin' : 'Make admin' }}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A9 9 0 1119.5 8M12 12v4m0 0l-2-2m2 2l2-2" />
                                        </svg>
                                    </button>

                                    @if($user->isVendor() && $user->verification_status !== 'approved')
                                        <button wire:click="updateVendorVerification({{ $user->id }}, 'approved')"
                                                class="text-green-600 hover:text-green-900 p-1"
                                                title="Approve vendor">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </button>
                                    @endif

                                    @if($user->isVendor() && $user->verification_status !== 'rejected')
                                        <button wire:click="updateVendorVerification({{ $user->id }}, 'rejected')"
                                                class="text-amber-600 hover:text-amber-900 p-1"
                                                title="Reject vendor">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    @endif

                                    <!-- Delete -->
                                    <button wire:click="$dispatch('confirm-delete', { id: {{ $user->id }} })"
                                            class="text-red-600 hover:text-red-900 p-1">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5 0c-.83 0-1.5.67-1.5 1.5s.67 1.5 1.5 1.5 1.5-.67 1.5-1.5-.67-1.5-1.5-1.5z" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No users found</h3>
                                <p class="mt-1 text-sm text-gray-500">
                                    @if($search)
                                        Try adjusting your search to find what you're looking for.
                                    @else
                                        No users have registered yet.
                                    @endif
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    <!-- Delete Confirmation Modal -->
    @isset($showDeleteModal)@if($showDeleteModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

                <!-- Modal panel -->
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.346 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Delete @isset($userIdToDelete){{ $userIdToDelete ? 'User' : 'Users' }}@else Users @endisset
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">
                                        @if($userIdToDelete)
                                            Are you sure you want to delete this user? This action cannot be undone.
                                        @else
                                            Are you sure you want to delete {{ count($selectedUsers) }} selected users? This action cannot be undone.
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        @if($userIdToDelete)
                            <button type="button" 
                                    wire:click="deleteUser"
                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                                Delete
                            </button>
                        @else
                            <button type="button" 
                                    wire:click="deleteSelected"
                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                                Delete
                            </button>
                        @endif
                        <button type="button" 
                                wire:click="$set('showDeleteModal', false); $set('userIdToDelete', null)"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif@endisset
</div>
