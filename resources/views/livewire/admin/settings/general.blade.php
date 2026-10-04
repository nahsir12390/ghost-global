<div class="p-6">
    <form wire:submit="save" class="space-y-6" x-data="{ maintenanceMode: @entangle('settings.maintenance_mode').defer }">
        <!-- Page Header -->
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-lg font-medium text-gray-900">General Settings</h2>
                <p class="text-sm text-gray-600">Configure basic site settings</p>
                @if(session('success'))
                    <div class="mt-2 p-3 bg-green-100 border border-green-400 text-green-700 rounded">
                        {{ session('success') }}
                    </div>
                @endif
            </div>
            <button type="submit" 
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                <svg wire:loading wire:target="save" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <svg wire:loading.remove wire:target="save" class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Save Changes
            </button>
        </div>

        <!-- Site Information -->
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <h3 class="text-md font-medium text-gray-900 mb-4">Site Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Site Name -->
                <div>
                    <label for="site_name" class="block text-sm font-medium text-gray-700 mb-1">
                        Site Name *
                    </label>
                    <input type="text" id="site_name" name="site_name" wire:model.defer="settings.site_name"
                           class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    @error('settings.site_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Site Email -->
                <div>
                    <label for="site_email" class="block text-sm font-medium text-gray-700 mb-1">
                        Site Email *
                    </label>
                    <input type="email" id="site_email" name="site_email" wire:model.defer="settings.site_email"
                           class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    @error('settings.site_email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Site Phone -->
                <div>
                    <label for="site_phone" class="block text-sm font-medium text-gray-700 mb-1">
                        Site Phone
                    </label>
                    <input type="text" id="site_phone" name="site_phone" wire:model.defer="settings.site_phone"
                           class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-md">
                </div>

                <!-- Site Address -->
                <div class="md:col-span-2">
                    <label for="site_address" class="block text-sm font-medium text-gray-700 mb-1">
                        Site Address
                    </label>
                    <textarea id="site_address" name="site_address" wire:model.defer="settings.site_address" rows="2"
                              class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-md"></textarea>
                </div>

                <!-- Support WhatsApp Number -->
                <div>
                    <label for="support_whatsapp_number" class="block text-sm font-medium text-gray-700 mb-1">
                        Support WhatsApp Number
                    </label>
                    <input type="text" id="support_whatsapp_number" name="support_whatsapp_number" wire:model.defer="settings.support_whatsapp_number"
                           class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-md"
                           placeholder="+234 812 345 6789">
                    <p class="mt-1 text-sm text-gray-500">
                        Used for the WhatsApp support chat buttons.
                    </p>
                    @error('settings.support_whatsapp_number')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Currency Settings -->
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <h3 class="text-md font-medium text-gray-900 mb-4">Currency Settings</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Currency Code -->
                <div>
                    <label for="site_currency" class="block text-sm font-medium text-gray-700 mb-1">
                        Currency Code *
                    </label>
                    <select id="site_currency" name="site_currency" wire:model.change="settings.site_currency"
                            class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        <option value="NGN">NGN - Nigerian Naira</option>
                        <option value="USD">USD - US Dollar</option>
                        <option value="EUR">EUR - Euro</option>
                        <option value="GBP">GBP - British Pound</option>
                        <option value="GHS">GHS - Ghanaian Cedi</option>
                        <option value="KES">KES - Kenyan Shilling</option>
                    </select>
                    @error('settings.site_currency')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Currency Symbol -->
                <div>
                    <label for="site_currency_symbol" class="block text-sm font-medium text-gray-700 mb-1">
                        Currency Symbol *
                    </label>
                    <input type="text" id="site_currency_symbol" name="site_currency_symbol" wire:model.defer="settings.site_currency_symbol"
                           class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    @error('settings.site_currency_symbol')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

            </div>
        </div>

        <!-- Checkout Defaults -->
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <h3 class="text-md font-medium text-gray-900 mb-2">Checkout Defaults</h3>
            <p class="mb-5 text-sm text-gray-500">These values are used to prefill the checkout shipping and billing location fields.</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="checkout_default_country" class="block text-sm font-medium text-gray-700 mb-1">Default Country</label>
                    <input type="text" id="checkout_default_country" wire:model.defer="settings.checkout_default_country"
                           class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    @error('settings.checkout_default_country')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="checkout_default_state" class="block text-sm font-medium text-gray-700 mb-1">Default State</label>
                    <input type="text" id="checkout_default_state" wire:model.defer="settings.checkout_default_state"
                           class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    @error('settings.checkout_default_state')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="checkout_default_city" class="block text-sm font-medium text-gray-700 mb-1">Default City</label>
                    <input type="text" id="checkout_default_city" wire:model.defer="settings.checkout_default_city"
                           class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    @error('settings.checkout_default_city')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="checkout_default_postal_code" class="block text-sm font-medium text-gray-700 mb-1">Default Postal Code</label>
                    <input type="text" id="checkout_default_postal_code" wire:model.defer="settings.checkout_default_postal_code"
                           class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    @error('settings.checkout_default_postal_code')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="md:col-span-2">
                    <label for="checkout_default_address" class="block text-sm font-medium text-gray-700 mb-1">Default Street Address</label>
                    <input type="text" id="checkout_default_address" wire:model.defer="settings.checkout_default_address"
                           class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-md"
                           placeholder="Optional. Leave empty if customers should always type their street address.">
                    @error('settings.checkout_default_address')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <!-- Platform Fee Settings -->
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <h3 class="text-md font-medium text-gray-900 mb-2">Platform Service Fee Settings</h3>
            <p class="mb-5 text-sm text-gray-500">These rates are used at checkout based on the order subtotal.</p>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label for="platform_fee_tier_1_max" class="block text-sm font-medium text-gray-700 mb-1">Tier 1 Maximum (₦)</label>
                    <input type="number" id="platform_fee_tier_1_max" wire:model.defer="settings.platform_fee_tier_1_max" step="0.01" min="0"
                           class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    @error('settings.platform_fee_tier_1_max')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="platform_fee_tier_1_rate" class="block text-sm font-medium text-gray-700 mb-1">Tier 1 Fee (%)</label>
                    <input type="number" id="platform_fee_tier_1_rate" wire:model.defer="settings.platform_fee_tier_1_rate" step="0.01" min="0" max="100"
                           class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    @error('settings.platform_fee_tier_1_rate')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="platform_fee_tier_2_max" class="block text-sm font-medium text-gray-700 mb-1">Tier 2 Maximum (₦)</label>
                    <input type="number" id="platform_fee_tier_2_max" wire:model.defer="settings.platform_fee_tier_2_max" step="0.01" min="0"
                           class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    @error('settings.platform_fee_tier_2_max')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="platform_fee_tier_2_rate" class="block text-sm font-medium text-gray-700 mb-1">Tier 2 Fee (%)</label>
                    <input type="number" id="platform_fee_tier_2_rate" wire:model.defer="settings.platform_fee_tier_2_rate" step="0.01" min="0" max="100"
                           class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    @error('settings.platform_fee_tier_2_rate')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="platform_fee_tier_3_max" class="block text-sm font-medium text-gray-700 mb-1">Tier 3 Maximum (₦)</label>
                    <input type="number" id="platform_fee_tier_3_max" wire:model.defer="settings.platform_fee_tier_3_max" step="0.01" min="0"
                           class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    @error('settings.platform_fee_tier_3_max')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="platform_fee_tier_3_rate" class="block text-sm font-medium text-gray-700 mb-1">Tier 3 Fee (%)</label>
                    <input type="number" id="platform_fee_tier_3_rate" wire:model.defer="settings.platform_fee_tier_3_rate" step="0.01" min="0" max="100"
                           class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    @error('settings.platform_fee_tier_3_rate')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="md:col-span-2">
                    <label for="platform_fee_tier_4_rate" class="block text-sm font-medium text-gray-700 mb-1">Above Tier 3 Fee (%)</label>
                    <input type="number" id="platform_fee_tier_4_rate" wire:model.defer="settings.platform_fee_tier_4_rate" step="0.01" min="0" max="100"
                           class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    @error('settings.platform_fee_tier_4_rate')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <!-- Site Appearance -->
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <h3 class="text-md font-medium text-gray-900 mb-4">Site Appearance</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label for="site_tagline" class="block text-sm font-medium text-gray-700 mb-1">Site Tagline</label>
                    <input type="text" id="site_tagline" wire:model.defer="settings.site_tagline" maxlength="160"
                           class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-md"
                           placeholder="A short reusable promise for this marketplace">
                    <p class="mt-1 text-sm text-gray-500">Displayed in shared branding areas and reusable across deployments.</p>
                    @error('settings.site_tagline')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <!-- Site Description -->
                <div class="md:col-span-2">
                    <label for="site_description" class="block text-sm font-medium text-gray-700 mb-1">
                        Site Description
                    </label>
                    <textarea id="site_description" name="site_description" wire:model.defer="settings.site_description" rows="3"
                              class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-md"></textarea>
                    <p class="mt-1 text-sm text-gray-500">
                        Brief description of your store for SEO purposes
                    </p>
                </div>

                <!-- Site Logo URL -->
                <div>
                    <label for="site_logo" class="block text-sm font-medium text-gray-700 mb-1">
                        Site Logo URL
                    </label>
                    <input type="text" id="site_logo" name="site_logo" wire:model.defer="settings.site_logo"
                           class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    <p class="mt-1 text-sm text-gray-500">
                        URL to your site logo image
                    </p>
                </div>

                <!-- Favicon URL -->
                <div>
                    <label for="site_favicon" class="block text-sm font-medium text-gray-700 mb-1">
                        Favicon URL
                    </label>
                    <input type="text" id="site_favicon" name="site_favicon" wire:model.defer="settings.site_favicon"
                           class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    <p class="mt-1 text-sm text-gray-500">
                        URL to your favicon (16x16 or 32x32 pixels)
                    </p>
                </div>
            </div>
        </div>

        <!-- Maintenance Mode -->
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-md font-medium text-gray-900">Maintenance Mode</h3>
                    <p class="text-sm text-gray-600">
                        When enabled, only administrators can access the site
                    </p>
                </div>
                <div>
                    <input type="hidden" name="maintenance_mode" wire:model.defer="settings.maintenance_mode">
                    <button type="button" 
                            class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                            @click="maintenanceMode = !maintenanceMode"
                            :class="{ 'bg-red-600': maintenanceMode, 'bg-gray-200': !maintenanceMode }">
                        <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out" 
                              :class="{ 'translate-x-5': maintenanceMode, 'translate-x-0': !maintenanceMode }"></span>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
