<div>
    <div class="w-full px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
            <!-- Profile Update -->
            <div class="lg:col-span-2 space-y-4 sm:space-y-6">
                <!-- Profile Information -->
                <div class="bg-white shadow rounded-lg p-4 sm:p-6">
                    <h3 class="text-lg sm:text-xl font-medium text-gray-900 mb-4 sm:mb-6">Profile Information</h3>
                    
                    @if(session('success'))
                        <div class="mb-4 p-3 sm:p-4 bg-green-50 border border-green-200 rounded-md">
                            <p class="text-sm sm:text-base text-green-600">{{ session('success') }}</p>
                        </div>
                    @endif

                    <form wire:submit.prevent="updateProfile">
                        <div class="space-y-4 sm:space-y-6">
                            <!-- Profile Photo -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Photo</label>
                                <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                                    <div class="flex-shrink-0">
                                        @if(Auth::user()->profile_photo_path)
                                            <img class="h-16 w-16 sm:h-20 sm:w-20 rounded-full object-cover" 
                                                 src="{{ asset('storage/' . Auth::user()->profile_photo_path) }}" 
                                                 alt="{{ Auth::user()->name }}">
                                        @else
                                            <div class="h-16 w-16 sm:h-20 sm:w-20 bg-red-100 text-red-600 rounded-full flex items-center justify-center font-semibold text-lg sm:text-2xl">
                                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <input type="file" wire:model="photo" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100">
                                        <p class="text-xs text-gray-500 mt-2">JPG, PNG or GIF (max 10MB)</p>
                                    </div>
                                </div>
                                @error('photo') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            @if(Auth::user()->isVendor())
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Store Banner</label>
                                    <div class="space-y-3">
                                        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-gradient-to-r from-slate-900 via-slate-800 to-red-900">
                                            @if(Auth::user()->store_banner_path)
                                                <img
                                                    src="{{ asset('storage/' . Auth::user()->store_banner_path) }}"
                                                    alt="{{ Auth::user()->store_name ?: Auth::user()->name }}"
                                                    class="h-40 w-full object-cover"
                                                >
                                            @else
                                                <div class="flex h-40 items-end bg-[radial-gradient(circle_at_top_left,_rgba(239,68,68,0.25),_transparent_35%),linear-gradient(135deg,_#0f172a,_#1e293b_55%,_#7f1d1d)] p-5 text-white">
                                                    <div>
                                                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-red-100">Storefront banner</p>
                                                        <p class="mt-2 text-lg font-semibold">{{ Auth::user()->store_name ?: 'Your vendor store' }}</p>
                                                        <p class="mt-1 text-sm text-slate-200">Upload a banner to make your store feel branded and more trustworthy.</p>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                        <input type="file" wire:model="store_banner" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100">
                                        <p class="text-xs text-gray-500">Recommended: wide image, up to 10MB.</p>
                                    </div>
                                    @error('store_banner') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            @endif

                            <!-- Name -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                                <input type="text" wire:model.blur="name" id="name" 
                                       class="w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-red-500 focus:border-red-500 text-sm sm:text-base">
                                @error('name') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <input type="email" wire:model.blur="email" id="email" 
                                       class="w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-red-500 focus:border-red-500 text-sm sm:text-base">
                                @error('email') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">{{ Auth::user()->isVendor() ? 'Business Phone' : 'Phone' }}</label>
                                <input type="text" wire:model.defer="phone" id="phone"
                                       class="w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-red-500 focus:border-red-500 text-sm sm:text-base">
                                @error('phone') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div class="{{ Auth::user()->isVendor() ? 'sm:col-span-2' : '' }}">
                                <label for="address" class="block text-sm font-medium text-gray-700 mb-1">{{ Auth::user()->isVendor() ? 'Business Address' : 'Address' }}</label>
                                <textarea wire:model.defer="address" id="address" rows="3"
                                          class="w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-red-500 focus:border-red-500 text-sm sm:text-base"></textarea>
                                @error('address') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            @if(Auth::user()->isVendor())
                                <div>
                                    <label for="store_name" class="block text-sm font-medium text-gray-700 mb-1">Store Name</label>
                                    <input type="text" wire:model.defer="store_name" id="store_name"
                                           class="w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-red-500 focus:border-red-500 text-sm sm:text-base">
                                    @error('store_name') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div class="sm:col-span-2">
                                    <label for="store_description" class="block text-sm font-medium text-gray-700 mb-1">Store Description</label>
                                    <textarea wire:model.defer="store_description" id="store_description" rows="4"
                                              class="w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-red-500 focus:border-red-500 text-sm sm:text-base"
                                              placeholder="Tell customers what your store is known for, what you sell, and why they should trust you."></textarea>
                                    @error('store_description') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div class="sm:col-span-2 rounded-xl border border-gray-200 bg-gray-50 p-4">
                                    <div class="mb-4">
                                        <p class="text-sm font-semibold text-gray-900">Store Links</p>
                                        <p class="mt-1 text-xs text-gray-500">These links appear on your public store page when provided.</p>
                                    </div>

                                    <div class="grid gap-4 sm:grid-cols-2">
                                        <div>
                                            <label for="store_whatsapp" class="block text-sm font-medium text-gray-700 mb-1">WhatsApp Number</label>
                                            <input type="text" wire:model.defer="store_whatsapp" id="store_whatsapp"
                                                   class="w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-red-500 focus:border-red-500 text-sm sm:text-base"
                                                   placeholder="2348012345678">
                                            @error('store_whatsapp') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                                        </div>

                                        <div>
                                            <label for="store_website" class="block text-sm font-medium text-gray-700 mb-1">Website</label>
                                            <input type="url" wire:model.defer="store_website" id="store_website"
                                                   class="w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-red-500 focus:border-red-500 text-sm sm:text-base"
                                                   placeholder="https://example.com">
                                            @error('store_website') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                                        </div>

                                        <div>
                                            <label for="store_instagram" class="block text-sm font-medium text-gray-700 mb-1">Instagram</label>
                                            <input type="text" wire:model.defer="store_instagram" id="store_instagram"
                                                   class="w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-red-500 focus:border-red-500 text-sm sm:text-base"
                                                   placeholder="@yourstore or full URL">
                                            @error('store_instagram') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                                        </div>

                                        <div>
                                            <label for="store_facebook" class="block text-sm font-medium text-gray-700 mb-1">Facebook</label>
                                            <input type="text" wire:model.defer="store_facebook" id="store_facebook"
                                                   class="w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-red-500 focus:border-red-500 text-sm sm:text-base"
                                                   placeholder="Page name or full URL">
                                            @error('store_facebook') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Submit Button -->
                            <div class="pt-2 sm:pt-4">
                                <button type="submit" 
                                        wire:loading.attr="disabled"
                                        wire:loading.class="opacity-50 cursor-not-allowed"
                                        class="w-full sm:w-auto inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition">
                                    <span wire:loading.remove>Save Changes</span>
                                    <span wire:loading class="flex items-center gap-2">
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

                <!-- Password Update -->
                <div class="bg-white shadow rounded-lg p-4 sm:p-6">
                    <h3 class="text-lg sm:text-xl font-medium text-gray-900 mb-4 sm:mb-6">Update Password</h3>
                    
                    <form wire:submit.prevent="updatePassword">
                        <div class="space-y-4 sm:space-y-6">
                            <!-- Current Password -->
                            <div x-data="{ showPassword: false }">
                                <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                                <div class="relative">
                                    <input wire:model.defer="current_password" id="current_password" 
                                           x-bind:type="showPassword ? 'text' : 'password'"
                                           class="w-full rounded-md border border-gray-300 py-2 px-3 pr-14 text-sm shadow-sm focus:border-red-500 focus:outline-none focus:ring-red-500 sm:text-base">
                                    <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 flex items-center pr-4 text-sm font-medium text-gray-400 transition hover:text-red-600">
                                        <span x-text="showPassword ? 'Hide' : 'Show'"></span>
                                    </button>
                                </div>
                                @error('current_password') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- New Password -->
                            <div x-data="{ showPassword: false }">
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                                <div class="relative">
                                    <input wire:model.defer="password" id="password" 
                                           x-bind:type="showPassword ? 'text' : 'password'"
                                           class="w-full rounded-md border border-gray-300 py-2 px-3 pr-14 text-sm shadow-sm focus:border-red-500 focus:outline-none focus:ring-red-500 sm:text-base">
                                    <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 flex items-center pr-4 text-sm font-medium text-gray-400 transition hover:text-red-600">
                                        <span x-text="showPassword ? 'Hide' : 'Show'"></span>
                                    </button>
                                </div>
                                @error('password') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Confirm Password -->
                            <div x-data="{ showPassword: false }">
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                                <div class="relative">
                                    <input wire:model.defer="password_confirmation" id="password_confirmation" 
                                           x-bind:type="showPassword ? 'text' : 'password'"
                                           class="w-full rounded-md border border-gray-300 py-2 px-3 pr-14 text-sm shadow-sm focus:border-red-500 focus:outline-none focus:ring-red-500 sm:text-base">
                                    <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 flex items-center pr-4 text-sm font-medium text-gray-400 transition hover:text-red-600">
                                        <span x-text="showPassword ? 'Hide' : 'Show'"></span>
                                    </button>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="pt-2 sm:pt-4">
                                <button type="submit" 
                                        wire:loading.attr="disabled"
                                        wire:loading.class="opacity-50 cursor-not-allowed"
                                        class="w-full sm:w-auto inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition">
                                    <span wire:loading.remove>Update Password</span>
                                    <span wire:loading class="flex items-center gap-2">
                                        <svg class="animate-spin h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                        Updating...
                                    </span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white shadow rounded-lg p-4 sm:p-6 sticky top-4">
                    <h3 class="text-lg sm:text-xl font-medium text-gray-900 mb-3 sm:mb-4">
                        {{ Auth::user()->isVendor() ? 'Vendor Information' : 'Admin Information' }}
                    </h3>
                    <div class="space-y-3 sm:space-y-4 divide-y divide-gray-200">
                        <div class="pt-0">
                            <p class="text-xs sm:text-sm text-gray-500 uppercase tracking-wide font-semibold">Role</p>
                            <p class="text-sm sm:text-base font-medium text-gray-900 mt-1">
                                {{ Auth::user()->isVendor() ? 'Vendor' : 'Administrator' }}
                            </p>
                        </div>
                        @if(Auth::user()->isVendor())
                            <div class="pt-3 sm:pt-4">
                                <p class="text-xs sm:text-sm text-gray-500 uppercase tracking-wide font-semibold">Store Name</p>
                                <p class="text-sm sm:text-base font-medium text-gray-900 mt-1">
                                    {{ Auth::user()->store_name ?: 'No store name added yet' }}
                                </p>
                            </div>
                            @if(Auth::user()->store_description)
                                <div class="pt-3 sm:pt-4">
                                    <p class="text-xs sm:text-sm text-gray-500 uppercase tracking-wide font-semibold">Store Description</p>
                                    <p class="text-sm sm:text-base text-gray-900 mt-1">
                                        {{ Auth::user()->store_description }}
                                    </p>
                                </div>
                            @endif
                            <div class="pt-3 sm:pt-4">
                                <p class="text-xs sm:text-sm text-gray-500 uppercase tracking-wide font-semibold">Verification Status</p>
                                @php($status = Auth::user()->verification_status ?? 'pending')
                                <p class="mt-1">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs sm:text-sm font-medium {{ $status === 'approved' ? 'bg-green-100 text-green-800' : ($status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800') }}">
                                        {{ $status === 'approved' ? 'Verified Vendor' : ucfirst($status) }}
                                    </span>
                                </p>
                            </div>
                            @if(Auth::user()->verification_phone || Auth::user()->phone)
                                <div class="pt-3 sm:pt-4">
                                    <p class="text-xs sm:text-sm text-gray-500 uppercase tracking-wide font-semibold">Phone Number</p>
                                    <p class="text-sm sm:text-base font-medium text-gray-900 mt-1">
                                        {{ Auth::user()->verification_phone ?: Auth::user()->phone }}
                                    </p>
                                </div>
                            @endif
                            @if(Auth::user()->storefrontUrl())
                                <div class="pt-3 sm:pt-4">
                                    <p class="text-xs sm:text-sm text-gray-500 uppercase tracking-wide font-semibold">Public Store</p>
                                    <a href="{{ Auth::user()->storefrontUrl() }}" target="_blank" rel="noopener" class="mt-1 inline-flex text-sm font-semibold text-red-600 transition hover:text-red-700">
                                        Open storefront
                                    </a>
                                </div>
                            @endif
                        @endif
                        <div class="pt-3 sm:pt-4">
                            <p class="text-xs sm:text-sm text-gray-500 uppercase tracking-wide font-semibold">Member Since</p>
                            <p class="text-sm sm:text-base font-medium text-gray-900 mt-1">
                                {{ Auth::user()->created_at->format('M j, Y') }}
                            </p>
                        </div>
                        <div class="pt-3 sm:pt-4">
                            <p class="text-xs sm:text-sm text-gray-500 uppercase tracking-wide font-semibold">Last Login</p>
                            <p class="text-sm sm:text-base font-medium text-gray-900 mt-1">
                                {{ Auth::user()->last_login_at ? Auth::user()->last_login_at->diffForHumans() : 'N/A' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
