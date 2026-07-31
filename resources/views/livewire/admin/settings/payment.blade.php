<div>
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

    <form wire:submit="save" class="space-y-8">
        <!-- Payment Gateway Settings -->
        <div class="bg-white shadow-sm rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-6 pb-4 border-b border-gray-200">Payment Gateway Settings</h3>
            
            <!-- Default Payment Gateway -->
            <div class="mb-8">
                <label class="block text-sm font-medium text-gray-700 mb-3">
                    Default Payment Gateway *
                </label>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Paystack Option -->
                    <div class="relative">
                        <input type="radio" 
                               id="paystack_gateway"
                               wire:model.change="selectedGateway"
                               value="paystack"
                               class="sr-only peer">
                        <label for="paystack_gateway" 
                               class="cursor-pointer block p-4 border-2 rounded-lg transition-all duration-200
                                      {{ $selectedGateway === 'paystack' ? 'border-red-500 bg-red-50 shadow-md' : 'border-gray-300 hover:border-gray-400 hover:shadow-sm' }}">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="h-10 w-10 rounded-md bg-purple-600 flex items-center justify-center">
                                        <svg class="h-6 w-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <span class="block text-sm font-medium text-gray-900">Paystack</span>
                                    <span class="block text-sm text-gray-500">Popular in Nigeria</span>
                                </div>
                            </div>
                        </label>
                    </div>

                    <!-- Flutterwave Option -->
                    <div class="relative">
                        <input type="radio" 
                               id="flutterwave_gateway"
                               wire:model.change="selectedGateway"
                               value="flutterwave"
                               class="sr-only peer">
                        <label for="flutterwave_gateway" 
                               class="cursor-pointer block p-4 border-2 rounded-lg transition-all duration-200
                                      {{ $selectedGateway === 'flutterwave' ? 'border-red-500 bg-red-50 shadow-md' : 'border-gray-300 hover:border-gray-400 hover:shadow-sm' }}">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="h-10 w-10 rounded-md bg-orange-500 flex items-center justify-center">
                                        <svg class="h-6 w-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <span class="block text-sm font-medium text-gray-900">Flutterwave</span>
                                    <span class="block text-sm text-gray-500">African payments</span>
                                </div>
                            </div>
                        </label>
                    </div>

                    <!-- Cash on Delivery Option -->
                    <div class="relative">
                        <input type="radio" 
                               id="cash_gateway"
                               wire:model.change="selectedGateway"
                               value="cash"
                               class="sr-only peer">
                        <label for="cash_gateway" 
                               class="cursor-pointer block p-4 border-2 rounded-lg transition-all duration-200
                                      {{ $selectedGateway === 'cash' ? 'border-red-500 bg-red-50 shadow-md' : 'border-gray-300 hover:border-gray-400 hover:shadow-sm' }}">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="h-10 w-10 rounded-md bg-green-600 flex items-center justify-center">
                                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <span class="block text-sm font-medium text-gray-900">Cash</span>
                                    <span class="block text-sm text-gray-500">Cash on delivery</span>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>
                @error('settings.default_payment_gateway')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Enable Cash on Delivery -->
<div class="flex items-center justify-between mb-6">
    <div class="flex-1">
        <label class="text-sm font-medium text-gray-700">
            Enable Cash on Delivery
        </label>
        <p class="text-sm text-gray-500 mt-1">
            Allow customers to pay with cash when their order is delivered.
        </p>
    </div>
    <button type="button"
            wire:click="toggleCashOnDelivery"
            class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 {{ ($settings['enable_cash_on_delivery'] ?? false) ? 'bg-red-600' : 'bg-gray-200' }}">
        <span class="sr-only">Toggle cash on delivery</span>
        <span class="pointer-events-none relative inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ ($settings['enable_cash_on_delivery'] ?? false) ? 'translate-x-5' : 'translate-x-0' }}"></span>
    </button>
</div>

<div class="flex items-center justify-between mb-6">
    <div class="flex-1">
        <label class="text-sm font-medium text-gray-700">
            Enable Wallet
        </label>
        <p class="text-sm text-gray-500 mt-1">
            Allow customers to top up an internal wallet and use it for checkout.
        </p>
    </div>
    <button type="button"
            wire:click="toggleWallet"
            class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 {{ ($settings['enable_wallet'] ?? false) ? 'bg-red-600' : 'bg-gray-200' }}">
        <span class="sr-only">Toggle wallet</span>
        <span class="pointer-events-none relative inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ ($settings['enable_wallet'] ?? false) ? 'translate-x-5' : 'translate-x-0' }}"></span>
    </button>
</div>

<div class="mb-6">
    <label class="block text-sm font-medium text-gray-700 mb-1">
        Wallet Minimum Top-up
    </label>
    <input type="number"
           min="0"
           step="0.01"
           wire:model.defer="settings.wallet_minimum_topup"
           class="w-full max-w-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
           placeholder="500">
    <p class="mt-1 text-sm text-gray-500">
        The minimum amount a customer can fund into their wallet in one transaction.
    </p>
    @error('settings.wallet_minimum_topup')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

<!-- Test Mode -->
<div class="flex items-center justify-between">
    <div class="flex-1">
        <label class="text-sm font-medium text-gray-700">
            Test Mode
        </label>
        <p class="text-sm text-gray-500 mt-1">
            Enable test mode to use sandbox credentials for payment gateways.
        </p>
    </div>
    <button type="button"
            wire:click="toggleTestMode"
            class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 {{ ($settings['test_mode'] ?? false) ? 'bg-red-600' : 'bg-gray-200' }}">
        <span class="sr-only">Toggle test mode</span>
        <span class="pointer-events-none relative inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ ($settings['test_mode'] ?? false) ? 'translate-x-5' : 'translate-x-0' }}"></span>
    </button>
</div>

        <!-- Paystack Configuration -->
        @if($selectedGateway === 'paystack' || $selectedGateway === 'paystack')
        <div class="bg-white shadow-sm rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-6 pb-4 border-b border-gray-200">Paystack Configuration</h3>
            
            <div class="space-y-6">
                @if($settings['test_mode'])
                    <!-- Test Keys -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Test Public Key
                        </label>
                        <input type="password" 
                               wire:model.defer="settings.paystack_test_public_key"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                               placeholder="pk_test_...">
                        @error('settings.paystack_test_public_key')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Test Secret Key
                        </label>
                        <input type="password" 
                               wire:model.defer="settings.paystack_test_secret_key"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                               placeholder="sk_test_...">
                        @error('settings.paystack_test_secret_key')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                @else
                    <!-- Live Keys -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Live Public Key
                        </label>
                        <input type="password" 
                               wire:model.defer="settings.paystack_public_key"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                               placeholder="pk_live_...">
                        @error('settings.paystack_public_key')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Live Secret Key
                        </label>
                        <input type="password" 
                               wire:model.defer="settings.paystack_secret_key"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                               placeholder="sk_live_...">
                        @error('settings.paystack_secret_key')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                @endif

                <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex">
                        <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">How to get Paystack keys</h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <p>1. Login to your <a href="https://dashboard.paystack.com/" target="_blank" class="font-medium underline">Paystack Dashboard</a></p>
                                <p>2. Go to Settings → API Keys & Webhooks</p>
                                <p>3. Copy your Public and Secret keys</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Flutterwave Configuration -->
        @if($selectedGateway === 'flutterwave')
        <div class="bg-white shadow-sm rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-6 pb-4 border-b border-gray-200">Flutterwave Configuration</h3>
            
            <div class="space-y-6">
                @if($settings['test_mode'])
                    <!-- Test Keys -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Test Public Key
                        </label>
                        <input type="password" 
                               wire:model.defer="settings.flutterwave_test_public_key"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                               placeholder="FLWPUBK_TEST_...">
                        @error('settings.flutterwave_test_public_key')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Test Secret Key
                        </label>
                        <input type="password" 
                               wire:model.defer="settings.flutterwave_test_secret_key"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                               placeholder="FLWSECK_TEST_...">
                        @error('settings.flutterwave_test_secret_key')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                @else
                    <!-- Live Keys -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Live Public Key
                        </label>
                        <input type="password" 
                               wire:model.defer="settings.flutterwave_public_key"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                               placeholder="FLWPUBK_LIVE_...">
                        @error('settings.flutterwave_public_key')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Live Secret Key
                        </label>
                        <input type="password" 
                               wire:model.defer="settings.flutterwave_secret_key"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                               placeholder="FLWSECK_LIVE_...">
                        @error('settings.flutterwave_secret_key')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                @endif

                <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex">
                        <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">How to get Flutterwave keys</h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <p>1. Login to your <a href="https://dashboard.flutterwave.com/" target="_blank" class="font-medium underline">Flutterwave Dashboard</a></p>
                                <p>2. Go to Settings → API Keys</p>
                                <p>3. Copy your Public and Secret keys</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Form Actions -->
        <div class="bg-white shadow-sm rounded-lg p-6">
            <div class="flex justify-end space-x-3">
                <button type="button"
                        wire:click="loadSettings"
                        class="px-4 py-2 text-gray-700 font-medium bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                    Cancel
                </button>
                <button type="submit"
                        wire:loading.attr="disabled"
                        class="px-4 py-2 text-white font-medium bg-red-600 rounded-lg hover:bg-red-700 transition flex items-center gap-2">
                    <span wire:loading.remove>Save Payment Settings</span>
                    <span wire:loading>
                        <svg class="animate-spin h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Saving...
                    </span>
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    // Auto-refresh when settings are saved
    Livewire.on('settings-saved', () => {
        setTimeout(() => {
            window.location.reload();
        }, 1500);
    });
</script>
@endpush
