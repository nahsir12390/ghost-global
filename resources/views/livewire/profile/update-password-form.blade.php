<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Volt\Component;

new class extends Component
{
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Update the password for the currently authenticated user.
     */
    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                'current_password' => ['required', 'string', 'current_password'],
                'password' => ['required', 'string', Password::defaults(), 'confirmed'],
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');
            throw $e;
        }

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');

        $this->dispatch('password-updated');
    }
}; ?>

<div>
    <form wire:submit="updatePassword" class="space-y-6">
        <div x-data="{ showPassword: false }">
            <label for="update_password_current_password" class="block text-sm font-medium text-gray-700">Current Password</label>
            <div class="relative mt-1">
                <input wire:model.defer="current_password" 
                       id="update_password_current_password" 
                       name="current_password" 
                       x-bind:type="showPassword ? 'text' : 'password'" 
                       class="block w-full rounded-md border border-gray-300 py-2 px-3 pr-14 shadow-sm focus:border-red-500 focus:outline-none focus:ring-red-500 sm:text-sm @error('current_password') border-red-500 @enderror" 
                       autocomplete="current-password">
                <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 flex items-center pr-4 text-sm font-medium text-gray-400 transition hover:text-red-600">
                    <span x-text="showPassword ? 'Hide' : 'Show'"></span>
                </button>
            </div>
            @error('current_password')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div x-data="{ showPassword: false }">
            <label for="update_password_password" class="block text-sm font-medium text-gray-700">New Password</label>
            <div class="relative mt-1">
                <input wire:model.defer="password" 
                       id="update_password_password" 
                       name="password" 
                       x-bind:type="showPassword ? 'text' : 'password'" 
                       class="block w-full rounded-md border border-gray-300 py-2 px-3 pr-14 shadow-sm focus:border-red-500 focus:outline-none focus:ring-red-500 sm:text-sm @error('password') border-red-500 @enderror" 
                       autocomplete="new-password">
                <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 flex items-center pr-4 text-sm font-medium text-gray-400 transition hover:text-red-600">
                    <span x-text="showPassword ? 'Hide' : 'Show'"></span>
                </button>
            </div>
            @error('password')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div x-data="{ showPassword: false }">
            <label for="update_password_password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
            <div class="relative mt-1">
                <input wire:model.defer="password_confirmation" 
                       id="update_password_password_confirmation" 
                       name="password_confirmation" 
                       x-bind:type="showPassword ? 'text' : 'password'" 
                       class="block w-full rounded-md border border-gray-300 py-2 px-3 pr-14 shadow-sm focus:border-red-500 focus:outline-none focus:ring-red-500 sm:text-sm @error('password_confirmation') border-red-500 @enderror" 
                       autocomplete="new-password">
                <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 flex items-center pr-4 text-sm font-medium text-gray-400 transition hover:text-red-600">
                    <span x-text="showPassword ? 'Hide' : 'Show'"></span>
                </button>
            </div>
            @error('password_confirmation')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" 
                    class="btn-primary px-6 disabled:opacity-50 disabled:cursor-not-allowed"
                    wire:loading.attr="disabled"
                    wire:target="updatePassword">
                <span wire:loading.remove wire:target="updatePassword">Update Password</span>
                <span wire:loading wire:target="updatePassword" class="flex items-center">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Updating...
                </span>
            </button>

            <div x-data="{ show: false }" x-show="show" x-init="@this.on('password-updated', () => {
                show = true;
                setTimeout(() => show = false, 2000);
            })" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform translate-y-2"
                x-transition:enter-end="opacity-100 transform translate-y-0"
                x-transition:leave="transition ease-in duration-300"
                x-transition:leave-start="opacity-100 transform translate-y-0"
                x-transition:leave-end="opacity-0 transform translate-y-2"
                class="text-sm text-green-600" style="display: none;">
                {{ __('Password updated successfully.') }}
            </div>
        </div>
    </form>
</div>
