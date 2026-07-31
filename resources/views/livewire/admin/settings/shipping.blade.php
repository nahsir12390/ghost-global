<div>
    <!-- Success Message -->
    @if(session()->has('success'))
        <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded-lg">
            <div class="flex items-center">
                <svg class="h-5 w-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    <!-- Error Message -->
    @if(session()->has('error'))
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-lg">
            <div class="flex items-center">
                <svg class="h-5 w-5 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    <!-- Validation Errors -->
    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-lg">
            <div class="flex">
                <svg class="h-5 w-5 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="flex-1">
                    <p class="text-sm font-medium text-red-800 mb-2">Please fix the following errors:</p>
                    <ul class="list-disc list-inside text-sm text-red-700">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <form wire:submit.prevent="save" class="space-y-6">
        <!-- Page Header -->
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-lg font-medium text-gray-900">Shipping Settings</h2>
                <p class="text-sm text-gray-600">Configure shipping methods and rates</p>
            </div>
            <button type="submit" 
                    wire:loading.attr="disabled"
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                <span wire:loading.remove>
                    <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Save Changes
                </span>
                <span wire:loading>
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Saving...
                </span>
            </button>
        </div>

        <!-- Enable Shipping -->
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-md font-medium text-gray-900">Enable Shipping</h3>
                    <p class="text-sm text-gray-600">
                        Enable or disable shipping calculation for orders
                    </p>
                </div>
                <button type="button" 
                        wire:click="toggleShippingEnabled"
                        class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 {{ ($settings['shipping_enabled'] ?? false) ? 'bg-red-600' : 'bg-gray-200' }}">
                    <span class="sr-only">Toggle shipping</span>
                    <span class="pointer-events-none relative inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ ($settings['shipping_enabled'] ?? false) ? 'translate-x-5' : 'translate-x-0' }}"></span>
                </button>
            </div>
        </div>

        <!-- Shipping Method -->
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <h3 class="text-md font-medium text-gray-900 mb-4">Shipping Method</h3>
            <div class="space-y-4">
                <!-- Shipping Method Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Shipping Method *
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <label class="relative border rounded-lg p-4 flex cursor-pointer hover:bg-gray-50 transition {{ $settings['shipping_method'] === 'flat_rate' ? 'border-red-500 bg-red-50' : 'border-gray-300' }}">
                            <input type="radio" wire:model.change="settings.shipping_method" value="flat_rate" 
                                   class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300">
                            <div class="ml-3 flex-1">
                                <span class="block text-sm font-medium text-gray-900">Flat Rate</span>
                                <span class="block text-sm text-gray-500">Fixed shipping fee for all orders</span>
                            </div>
                        </label>
                        
                        <label class="relative border rounded-lg p-4 flex cursor-pointer hover:bg-gray-50 transition {{ $settings['shipping_method'] === 'free' ? 'border-red-500 bg-red-50' : 'border-gray-300' }}">
                            <input type="radio" wire:model.change="settings.shipping_method" value="free" 
                                   class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300">
                            <div class="ml-3 flex-1">
                                <span class="block text-sm font-medium text-gray-900">Free Shipping</span>
                                <span class="block text-sm text-gray-500">Free delivery for all orders</span>
                            </div>
                        </label>
                        
                        <label class="relative border rounded-lg p-4 flex cursor-pointer hover:bg-gray-50 transition {{ $settings['shipping_method'] === 'calculated' ? 'border-red-500 bg-red-50' : 'border-gray-300' }}">
                            <input type="radio" wire:model.change="settings.shipping_method" value="calculated" 
                                   class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300">
                            <div class="ml-3 flex-1">
                                <span class="block text-sm font-medium text-gray-900">Calculated</span>
                                <span class="block text-sm text-gray-500">Calculate based on weight/distance</span>
                            </div>
                        </label>
                    </div>
                    @error('settings.shipping_method')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Shipping Rates -->
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <h3 class="text-md font-medium text-gray-900 mb-4">Shipping Rates</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Delivery Fee -->
                <div>
                    <label for="shipping_fee" class="block text-sm font-medium text-gray-700 mb-1">
                        Delivery Fee (₦)
                    </label>
                    <input type="number" id="shipping_fee" wire:model.defer="settings.shipping_fee" step="0.01" min="0"
                           class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    @error('settings.shipping_fee')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">
                        Delivery fee for orders below the free delivery threshold
                    </p>
                </div>

                <!-- Free Delivery Threshold -->
                <div>
                    <label for="free_shipping_threshold" class="block text-sm font-medium text-gray-700 mb-1">
                        Free Delivery Threshold (₦)
                    </label>
                    <input type="number" id="free_shipping_threshold" wire:model.defer="settings.free_shipping_threshold" step="0.01" min="0"
                           class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    @error('settings.free_shipping_threshold')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">
                        Orders at or above this amount get free delivery
                    </p>
                </div>

                <!-- Estimated Delivery Days -->
                <div>
                    <label for="estimated_delivery_days" class="block text-sm font-medium text-gray-700 mb-1">
                        Estimated Delivery Days
                    </label>
                    <input type="number" id="estimated_delivery_days" wire:model.defer="settings.estimated_delivery_days" min="1"
                           class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    @error('settings.estimated_delivery_days')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">
                        Average days for order delivery
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Additional Info when Free Shipping is selected -->
        @if($settings['shipping_method'] === 'free')
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex">
                    <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-green-800">Free Shipping Active</h3>
                        <div class="mt-1 text-sm text-green-700">
                            <p>Free delivery is enabled for all orders.</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </form>
</div>

@push('scripts')
<script>
    // Optional: Auto-refresh or notification when settings are saved
    Livewire.on('settings-saved', () => {
        // You can add a toast notification here if you have one
    });
</script>
@endpush
