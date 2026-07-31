<div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
    <div class="flex items-center justify-between border-b border-gray-200 pb-4 mb-6">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">Staff Management</h3>
            <p class="mt-1 text-sm text-gray-600">Assign user as staff member or manage their permissions</p>
        </div>
    </div>

    @if($user->isStaff())
        <!-- Staff Active State -->
        <div class="space-y-4">
            <div class="rounded-xl border-l-4 border-blue-500 bg-blue-50 p-4">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-semibold text-blue-900">Current Role</p>
                        <p class="mt-2 text-lg font-bold text-blue-900">
                            @if($user->staff_role === 'order_manager')
                                📋 Order Manager
                            @elseif($user->staff_role === 'product_manager')
                                📦 Product Manager
                            @else
                                🔑 All (Orders & Products)
                            @endif
                        </p>
                        <p class="mt-1 text-xs text-blue-700">
                            Assigned: <strong>{{ $user->staff_assigned_at?->format('M d, Y H:i') }}</strong>
                        </p>
                    </div>
                    <div class="inline-flex items-center gap-2 rounded-full bg-blue-200 px-3 py-1">
                        <span class="w-2 h-2 bg-blue-600 rounded-full"></span>
                        <span class="text-xs font-semibold text-blue-900">Active</span>
                    </div>
                </div>
            </div>

            <!-- Staff Permissions Info -->
            <div class="rounded-xl bg-gray-50 p-4">
                <p class="text-xs font-semibold uppercase text-gray-700 mb-3">Permissions</p>
                <div class="space-y-2">
                    @if($user->isOrderManager())
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-sm text-gray-700">Can manage orders</span>
                        </div>
                    @else
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            <span class="text-sm text-gray-500">Cannot manage orders</span>
                        </div>
                    @endif

                    @if($user->isProductManager())
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-sm text-gray-700">Can manage products</span>
                        </div>
                    @else
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            <span class="text-sm text-gray-500">Cannot manage products</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Deactivate Button -->
            @if(!$showConfirmDeactivate)
                <button wire:click="$toggle('showConfirmDeactivate')" 
                        class="w-full rounded-xl border border-red-300 px-4 py-2.5 text-sm font-semibold text-red-700 hover:bg-red-50 transition-colors">
                    Deactivate Staff
                </button>
            @else
                <div class="rounded-xl border border-red-200 bg-red-50 p-4">
                    <p class="text-sm font-semibold text-red-900 mb-3">
                        Are you sure? This will remove {{ $user->name }}'s staff access.
                    </p>
                    <div class="flex gap-2">
                        <button wire:click="deactivateStaff" 
                                class="flex-1 rounded-lg border border-red-500 bg-red-500 px-4 py-2 text-sm font-semibold text-white hover:bg-red-600">
                            Yes, Deactivate
                        </button>
                        <button wire:click="$toggle('showConfirmDeactivate')" 
                                class="flex-1 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                            Cancel
                        </button>
                    </div>
                </div>
            @endif
        </div>
    @else
        <!-- Staff Inactive State - Assignment Form -->
        <div class="space-y-4">
            <div class="rounded-xl bg-gray-50 p-4">
                <p class="text-sm font-semibold text-gray-700 mb-3">Select Staff Role</p>
                
                <div class="space-y-3">
                    <label class="flex items-center gap-3 rounded-lg border-2 border-gray-200 p-3 cursor-pointer hover:border-blue-300 transition-colors"
                           :class="selectedStaffRole === 'order_manager' ? 'border-blue-500 bg-blue-50' : ''">
                        <input type="radio" wire:model.change="selectedStaffRole" value="order_manager" 
                               class="w-4 h-4 rounded-full text-blue-600">
                        <div>
                            <p class="font-medium text-gray-900">📋 Order Manager</p>
                            <p class="text-xs text-gray-600">Can manage and process customer orders</p>
                        </div>
                    </label>

                    <label class="flex items-center gap-3 rounded-lg border-2 border-gray-200 p-3 cursor-pointer hover:border-blue-300 transition-colors"
                           :class="selectedStaffRole === 'product_manager' ? 'border-blue-500 bg-blue-50' : ''">
                        <input type="radio" wire:model.change="selectedStaffRole" value="product_manager" 
                               class="w-4 h-4 rounded-full text-blue-600">
                        <div>
                            <p class="font-medium text-gray-900">📦 Product Manager</p>
                            <p class="text-xs text-gray-600">Can add, edit, and manage products</p>
                        </div>
                    </label>

                    <label class="flex items-center gap-3 rounded-lg border-2 border-gray-200 p-3 cursor-pointer hover:border-blue-300 transition-colors"
                           :class="selectedStaffRole === 'all' ? 'border-blue-500 bg-blue-50' : ''">
                        <input type="radio" wire:model.change="selectedStaffRole" value="all" 
                               class="w-4 h-4 rounded-full text-blue-600">
                        <div>
                            <p class="font-medium text-gray-900">🔑 All (Orders & Products)</p>
                            <p class="text-xs text-gray-600">Full access to orders and product management</p>
                        </div>
                    </label>
                </div>
            </div>

            <button wire:click="assignAsStaff" 
                    :disabled="!selectedStaffRole"
                    class="w-full rounded-xl border border-blue-300 bg-blue-50 px-4 py-2.5 text-sm font-semibold text-blue-700 hover:bg-blue-100 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                Assign as Staff
            </button>

            <p class="text-xs text-gray-600 text-center">
                Users must register and create an account before being assigned as staff
            </p>
        </div>
    @endif
</div>
