@extends('layouts.admin')

@section('title', 'General Settings')
@section('breadcrumb', 'Settings / General')

@section('content')
@php
    $teamMembers = old('team_members', \App\Helpers\SettingsHelper::aboutTeamMembers());
@endphp
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">General Settings</h1>
            <p class="text-gray-600">Configure basic site settings and information</p>
        </div>
        <a href="{{ route('admin.settings.index') }}" 
           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Settings
        </a>
    </div>

    <!-- Settings Form -->
    <div class="bg-white shadow-sm rounded-lg">
        <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="space-y-6 p-6">
            @csrf
            @method('PUT')

            <!-- Site Information -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Site Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Site Name -->
                    <div>
                        <label for="site_name" class="block text-sm font-medium text-gray-700 mb-1">
                            Site Name *
                        </label>
                        <input type="text" id="site_name" name="site_name" value="{{ old('site_name', \App\Helpers\SettingsHelper::get('site_name', config('app.name'))) }}"
                               class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        @error('site_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Site Email -->
                    <div>
                        <label for="site_email" class="block text-sm font-medium text-gray-700 mb-1">
                            Site Email *
                        </label>
                        <input type="email" id="site_email" name="site_email" value="{{ old('site_email', \App\Helpers\SettingsHelper::get('site_email', config('mail.from.address'))) }}"
                               class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        @error('site_email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Site Phone -->
                    <div>
                        <label for="site_phone" class="block text-sm font-medium text-gray-700 mb-1">
                            Site Phone
                        </label>
                        <input type="text" id="site_phone" name="site_phone" value="{{ old('site_phone', \App\Helpers\SettingsHelper::get('site_phone')) }}"
                               class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        @error('site_phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Site Address -->
                    <div class="md:col-span-2">
                        <label for="site_address" class="block text-sm font-medium text-gray-700 mb-1">
                            Site Address
                        </label>
                        <textarea id="site_address" name="site_address" rows="2"
                                  class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ old('site_address', \App\Helpers\SettingsHelper::get('site_address')) }}</textarea>
                        @error('site_address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Support WhatsApp Number -->
                    <div>
                        <label for="support_whatsapp_number" class="block text-sm font-medium text-gray-700 mb-1">
                            Support WhatsApp Number
                        </label>
                        <input type="text" id="support_whatsapp_number" name="support_whatsapp_number" value="{{ old('support_whatsapp_number', \App\Helpers\SettingsHelper::get('support_whatsapp_number')) }}"
                               class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-md"
                               placeholder="+234 812 345 6789">
                        <p class="mt-1 text-sm text-gray-500">
                            Used for the WhatsApp support chat buttons.
                        </p>
                        @error('support_whatsapp_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Currency Settings -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Currency Settings</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Currency Code -->
                    <div>
                        <label for="site_currency" class="block text-sm font-medium text-gray-700 mb-1">
                            Currency Code *
                        </label>
                        <select id="site_currency" name="site_currency"
                                class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-md">
                            <option value="NGN" {{ old('site_currency', \App\Helpers\SettingsHelper::get('site_currency', 'NGN')) == 'NGN' ? 'selected' : '' }}>NGN - Nigerian Naira</option>
                            <option value="USD" {{ old('site_currency', \App\Helpers\SettingsHelper::get('site_currency', 'NGN')) == 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                            <option value="EUR" {{ old('site_currency', \App\Helpers\SettingsHelper::get('site_currency', 'NGN')) == 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                            <option value="GBP" {{ old('site_currency', \App\Helpers\SettingsHelper::get('site_currency', 'NGN')) == 'GBP' ? 'selected' : '' }}>GBP - British Pound</option>
                            <option value="GHS" {{ old('site_currency', \App\Helpers\SettingsHelper::get('site_currency', 'NGN')) == 'GHS' ? 'selected' : '' }}>GHS - Ghanaian Cedi</option>
                            <option value="KES" {{ old('site_currency', \App\Helpers\SettingsHelper::get('site_currency', 'NGN')) == 'KES' ? 'selected' : '' }}>KES - Kenyan Shilling</option>
                        </select>
                        @error('site_currency')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Currency Symbol -->
                    <div>
                        <label for="site_currency_symbol" class="block text-sm font-medium text-gray-700 mb-1">
                            Currency Symbol *
                        </label>
                        <input type="text" id="site_currency_symbol" name="site_currency_symbol" value="{{ old('site_currency_symbol', \App\Helpers\SettingsHelper::get('site_currency_symbol', '₦')) }}"
                               class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        @error('site_currency_symbol')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                </div>
            </div>

            <!-- Checkout Defaults -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-2">Checkout Defaults</h3>
                <p class="mb-4 text-sm text-gray-500">These values are used to prefill the checkout shipping and billing location fields.</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="checkout_default_country" class="block text-sm font-medium text-gray-700 mb-1">Default Country</label>
                        <input type="text" id="checkout_default_country" name="checkout_default_country" value="{{ old('checkout_default_country', \App\Helpers\SettingsHelper::checkoutDefaultCountry()) }}"
                               class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        @error('checkout_default_country')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="checkout_default_state" class="block text-sm font-medium text-gray-700 mb-1">Default State</label>
                        <input type="text" id="checkout_default_state" name="checkout_default_state" value="{{ old('checkout_default_state', \App\Helpers\SettingsHelper::checkoutDefaultState()) }}"
                               class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        @error('checkout_default_state')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="checkout_default_city" class="block text-sm font-medium text-gray-700 mb-1">Default City</label>
                        <input type="text" id="checkout_default_city" name="checkout_default_city" value="{{ old('checkout_default_city', \App\Helpers\SettingsHelper::checkoutDefaultCity()) }}"
                               class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        @error('checkout_default_city')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="checkout_default_postal_code" class="block text-sm font-medium text-gray-700 mb-1">Default Postal Code</label>
                        <input type="text" id="checkout_default_postal_code" name="checkout_default_postal_code" value="{{ old('checkout_default_postal_code', \App\Helpers\SettingsHelper::checkoutDefaultPostalCode()) }}"
                               class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        @error('checkout_default_postal_code')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="checkout_default_address" class="block text-sm font-medium text-gray-700 mb-1">Default Street Address</label>
                        <input type="text" id="checkout_default_address" name="checkout_default_address" value="{{ old('checkout_default_address', \App\Helpers\SettingsHelper::checkoutDefaultAddress()) }}"
                               class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-md"
                               placeholder="Optional. Leave empty if customers should always type their street address.">
                        @error('checkout_default_address')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <!-- Platform Fee Settings -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-2">Platform Service Fee Settings</h3>
                <p class="mb-4 text-sm text-gray-500">These rates are used at checkout based on the order subtotal.</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="platform_fee_tier_1_max" class="block text-sm font-medium text-gray-700 mb-1">Tier 1 Maximum (₦)</label>
                        <input type="number" id="platform_fee_tier_1_max" name="platform_fee_tier_1_max" value="{{ old('platform_fee_tier_1_max', \App\Helpers\SettingsHelper::get('platform_fee_tier_1_max', '10000')) }}"
                               step="0.01" min="0" class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        @error('platform_fee_tier_1_max')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="platform_fee_tier_1_rate" class="block text-sm font-medium text-gray-700 mb-1">Tier 1 Fee (%)</label>
                        <input type="number" id="platform_fee_tier_1_rate" name="platform_fee_tier_1_rate" value="{{ old('platform_fee_tier_1_rate', \App\Helpers\SettingsHelper::get('platform_fee_tier_1_rate', '7.5')) }}"
                               step="0.01" min="0" max="100" class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        @error('platform_fee_tier_1_rate')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="platform_fee_tier_2_max" class="block text-sm font-medium text-gray-700 mb-1">Tier 2 Maximum (₦)</label>
                        <input type="number" id="platform_fee_tier_2_max" name="platform_fee_tier_2_max" value="{{ old('platform_fee_tier_2_max', \App\Helpers\SettingsHelper::get('platform_fee_tier_2_max', '50000')) }}"
                               step="0.01" min="0" class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        @error('platform_fee_tier_2_max')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="platform_fee_tier_2_rate" class="block text-sm font-medium text-gray-700 mb-1">Tier 2 Fee (%)</label>
                        <input type="number" id="platform_fee_tier_2_rate" name="platform_fee_tier_2_rate" value="{{ old('platform_fee_tier_2_rate', \App\Helpers\SettingsHelper::get('platform_fee_tier_2_rate', '5')) }}"
                               step="0.01" min="0" max="100" class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        @error('platform_fee_tier_2_rate')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="platform_fee_tier_3_max" class="block text-sm font-medium text-gray-700 mb-1">Tier 3 Maximum (₦)</label>
                        <input type="number" id="platform_fee_tier_3_max" name="platform_fee_tier_3_max" value="{{ old('platform_fee_tier_3_max', \App\Helpers\SettingsHelper::get('platform_fee_tier_3_max', '200000')) }}"
                               step="0.01" min="0" class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        @error('platform_fee_tier_3_max')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="platform_fee_tier_3_rate" class="block text-sm font-medium text-gray-700 mb-1">Tier 3 Fee (%)</label>
                        <input type="number" id="platform_fee_tier_3_rate" name="platform_fee_tier_3_rate" value="{{ old('platform_fee_tier_3_rate', \App\Helpers\SettingsHelper::get('platform_fee_tier_3_rate', '3')) }}"
                               step="0.01" min="0" max="100" class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        @error('platform_fee_tier_3_rate')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div class="md:col-span-2">
                        <label for="platform_fee_tier_4_rate" class="block text-sm font-medium text-gray-700 mb-1">Above Tier 3 Fee (%)</label>
                        <input type="number" id="platform_fee_tier_4_rate" name="platform_fee_tier_4_rate" value="{{ old('platform_fee_tier_4_rate', \App\Helpers\SettingsHelper::get('platform_fee_tier_4_rate', '2')) }}"
                               step="0.01" min="0" max="100" class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        @error('platform_fee_tier_4_rate')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <!-- Site Appearance -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Site Appearance</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Site Description -->
                    <div class="md:col-span-2">
                        <label for="site_description" class="block text-sm font-medium text-gray-700 mb-1">
                            Site Description
                        </label>
                        <textarea id="site_description" name="site_description" rows="3"
                                  class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ old('site_description', \App\Helpers\SettingsHelper::get('site_description')) }}</textarea>
                        <p class="mt-1 text-sm text-gray-500">
                            Brief description of your store for SEO purposes
                        </p>
                        @error('site_description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Site Logo URL -->
                    <div>
                        <label for="site_logo" class="block text-sm font-medium text-gray-700 mb-1">
                            Site Logo URL
                        </label>
                        <input type="text" id="site_logo" name="site_logo" value="{{ old('site_logo', \App\Helpers\SettingsHelper::get('site_logo')) }}"
                               class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        <p class="mt-1 text-sm text-gray-500">
                            URL to your site logo image (e.g., /storage/logo.png)
                        </p>
                        @error('site_logo')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Favicon URL -->
                    <div>
                        <label for="site_favicon" class="block text-sm font-medium text-gray-700 mb-1">
                            Favicon URL
                        </label>
                        <input type="text" id="site_favicon" name="site_favicon" value="{{ old('site_favicon', \App\Helpers\SettingsHelper::get('site_favicon')) }}"
                               class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        <p class="mt-1 text-sm text-gray-500">
                            URL to your favicon (16x16 or 32x32 pixels)
                        </p>
                        @error('site_favicon')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- About Page Team -->
            <div class="border-b border-gray-200 pb-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">About Page Team</h3>
                        <p class="text-sm text-gray-600">Add the people who should appear on the public about page.</p>
                    </div>
                    <button type="button" id="add-team-member"
                            class="inline-flex items-center justify-center px-4 py-2 border border-red-200 rounded-md shadow-sm text-sm font-medium text-red-700 bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Team Member
                    </button>
                </div>

                <div id="team-members-list" class="space-y-4">
                    @foreach($teamMembers as $index => $member)
                        <div class="team-member border border-gray-200 rounded-lg p-4 bg-gray-50">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-sm font-semibold text-gray-900">Team Member</h4>
                                <button type="button" class="remove-team-member inline-flex items-center text-sm text-red-600 hover:text-red-800">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Remove
                                </button>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                                    <input type="text" name="team_members[{{ $index }}][name]" value="{{ $member['name'] ?? '' }}"
                                           class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                           placeholder="Full name">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                                    <input type="text" name="team_members[{{ $index }}][role]" value="{{ $member['role'] ?? '' }}"
                                           class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                           placeholder="Founder, Manager, Developer">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Upload Image</label>
                                    <input type="file" name="team_members[{{ $index }}][photo]" accept="image/jpeg,image/png,image/gif,image/webp"
                                           class="block w-full text-sm text-gray-700 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100">
                                    <p class="mt-1 text-xs text-gray-500">JPG, PNG, GIF, or WEBP up to 4MB.</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Current Image Path or URL</label>
                                    <input type="text" name="team_members[{{ $index }}][image]" value="{{ $member['image'] ?? '' }}"
                                           class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                           placeholder="images/member-photo.jpg or https://example.com/photo.jpg">
                                    <p class="mt-1 text-xs text-gray-500">Uploading a new file replaces this value.</p>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">About Them</label>
                                    <textarea name="team_members[{{ $index }}][bio]" rows="3"
                                              class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                              placeholder="Short bio shown on the about page">{{ $member['bio'] ?? '' }}</textarea>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @error('team_members')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Maintenance Mode -->
            <div class="pb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Maintenance Mode</h3>
                        <p class="text-sm text-gray-600">
                            When enabled, only administrators can access the site
                        </p>
                    </div>
                    <div>
                        <input type="hidden" name="maintenance_mode" value="0">
                        <input type="checkbox" id="maintenance_mode" name="maintenance_mode" value="1" 
                               {{ old('maintenance_mode', \App\Helpers\SettingsHelper::get('maintenance_mode', false)) ? 'checked' : '' }}
                               class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end">
                <button type="submit" 
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Save Changes
                </button>
            </div>
        </form>
    </div>

    <!-- Current Settings Display -->
    <div class="bg-white shadow-sm rounded-lg">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Current Settings</h3>
        </div>
        <div class="p-6">
            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Site Name</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ \App\Helpers\SettingsHelper::get('site_name', config('app.name')) }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Site Email</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ \App\Helpers\SettingsHelper::get('site_email', config('mail.from.address')) }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Currency</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ \App\Helpers\SettingsHelper::get('site_currency', 'NGN') }} ({{ \App\Helpers\SettingsHelper::get('site_currency_symbol', '₦') }})</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Platform Fee Tiers</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ \App\Helpers\SettingsHelper::get('platform_fee_tier_1_rate', '7.5') }}% / {{ \App\Helpers\SettingsHelper::get('platform_fee_tier_2_rate', '5') }}% / {{ \App\Helpers\SettingsHelper::get('platform_fee_tier_3_rate', '3') }}% / {{ \App\Helpers\SettingsHelper::get('platform_fee_tier_4_rate', '2') }}%
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Maintenance Mode</dt>
                    <dd class="mt-1">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ \App\Helpers\SettingsHelper::get('maintenance_mode', false) ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                            {{ \App\Helpers\SettingsHelper::get('maintenance_mode', false) ? 'Enabled' : 'Disabled' }}
                        </span>
                    </dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500">Site Description</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ \App\Helpers\SettingsHelper::get('site_description', 'No description set') }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">About Team Members</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ count(\App\Helpers\SettingsHelper::aboutTeamMembers()) }}</dd>
                </div>
            </dl>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Show success message if settings were saved
    @if(session('success'))
        document.addEventListener('DOMContentLoaded', function() {
            showNotification('{{ session('success') }}', 'success');
        });
    @endif

    @if(session('error'))
        document.addEventListener('DOMContentLoaded', function() {
            showNotification('{{ session('error') }}', 'error');
        });
    @endif

    document.addEventListener('DOMContentLoaded', function() {
        const list = document.getElementById('team-members-list');
        const addButton = document.getElementById('add-team-member');
        let teamMemberIndex = list.querySelectorAll('.team-member').length;

        function teamMemberTemplate(index) {
            return `
                <div class="team-member border border-gray-200 rounded-lg p-4 bg-gray-50">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-sm font-semibold text-gray-900">Team Member</h4>
                        <button type="button" class="remove-team-member inline-flex items-center text-sm text-red-600 hover:text-red-800">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Remove
                        </button>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                            <input type="text" name="team_members[${index}][name]"
                                   class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                   placeholder="Full name">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                            <input type="text" name="team_members[${index}][role]"
                                   class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                   placeholder="Founder, Manager, Developer">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Upload Image</label>
                            <input type="file" name="team_members[${index}][photo]" accept="image/jpeg,image/png,image/gif,image/webp"
                                   class="block w-full text-sm text-gray-700 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100">
                            <p class="mt-1 text-xs text-gray-500">JPG, PNG, GIF, or WEBP up to 4MB.</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Current Image Path or URL</label>
                            <input type="text" name="team_members[${index}][image]"
                                   class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                   placeholder="images/member-photo.jpg or https://example.com/photo.jpg">
                            <p class="mt-1 text-xs text-gray-500">Uploading a new file replaces this value.</p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">About Them</label>
                            <textarea name="team_members[${index}][bio]" rows="3"
                                      class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                      placeholder="Short bio shown on the about page"></textarea>
                        </div>
                    </div>
                </div>
            `;
        }

        addButton.addEventListener('click', function() {
            list.insertAdjacentHTML('beforeend', teamMemberTemplate(teamMemberIndex));
            teamMemberIndex += 1;
        });

        list.addEventListener('click', function(event) {
            const removeButton = event.target.closest('.remove-team-member');

            if (removeButton) {
                removeButton.closest('.team-member').remove();
            }
        });
    });

    function showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm ${
            type === 'success' ? 'bg-green-50 border-l-4 border-green-500 text-green-800' :
            type === 'error' ? 'bg-red-50 border-l-4 border-red-500 text-red-800' :
            'bg-blue-50 border-l-4 border-blue-500 text-blue-800'
        }`;
        
        notification.innerHTML = `
            <div class="flex">
                <div class="flex-shrink-0">
                    ${type === 'success' ? 
                        '<svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>' :
                      type === 'error' ?
                        '<svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>' :
                        '<svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" /></svg>'
                    }
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium">${message}</p>
                </div>
                <div class="ml-auto pl-3">
                    <button type="button" onclick="this.parentElement.parentElement.remove()" class="inline-flex text-gray-400 hover:text-gray-500 focus:outline-none">
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 5000);
    }
</script>
@endpush
