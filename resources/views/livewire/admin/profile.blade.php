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



                            <!-- Name -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                                <input type="text" wire:model.defer="name" id="name"
                                       class="w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-red-500 focus:border-red-500 text-sm sm:text-base">
                                @error('name') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <input type="email" wire:model.defer="email" id="email"
                                       class="w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-red-500 focus:border-red-500 text-sm sm:text-base">
                                @error('email') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">{{ 'Phone' }}</label>
                                <input type="text" wire:model.defer="phone" id="phone"
                                       class="w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-red-500 focus:border-red-500 text-sm sm:text-base">
                                @error('phone') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div class="{{ '' }}">
                                <label for="address" class="block text-sm font-medium text-gray-700 mb-1">{{ 'Address' }}</label>
                                <textarea wire:model.defer="address" id="address" rows="3"
                                          class="w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-red-500 focus:border-red-500 text-sm sm:text-base"></textarea>
                                @error('address') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                            </div>



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
                        {{ 'Admin Information' }}
                    </h3>
                    <div class="space-y-3 sm:space-y-4 divide-y divide-gray-200">
                        <div class="pt-0">
                            <p class="text-xs sm:text-sm text-gray-500 uppercase tracking-wide font-semibold">Role</p>
                            <p class="text-sm sm:text-base font-medium text-gray-900 mt-1">
                                {{ 'Administrator' }}
                            </p>
                        </div>

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
