<?php

use App\Models\User;
use App\Services\ReferralService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $referralCode = '';

    public function mount(): void
    {
        $this->referralCode = (string) session(ReferralService::SESSION_KEY, '');
    }



    public function register(): void
    {
        $this->referralCode = strtoupper(trim($this->referralCode));

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'referralCode' => ['nullable', 'string', 'max:32', 'exists:users,referral_code'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['role'] = 'customer';

        $validated['password'] = Hash::make($validated['password']);
        $validated['referral_code'] = User::generateUniqueReferralCode();

        event(new Registered($user = User::create($validated)));

        app(ReferralService::class)->assignReferrer($user, $this->referralCode);

        Auth::login($user);

        $destination = $user->isAdmin()
            ? route('admin.dashboard', absolute: false)
            : route('dashboard', absolute: false);

        $this->redirect($destination, navigate: true);
    }
}; ?>

@php
    $siteName = \App\Helpers\SettingsHelper::get('site_name', config('app.name', 'E-Commerce'));
@endphp

<div class="space-y-5" x-data="{ showReferral: @js((bool) $referralCode) }">
    <div class="overflow-hidden rounded-[1.6rem] border border-red-100 bg-white shadow-sm">
        <div class="bg-gradient-to-br from-red-600 via-red-700 to-slate-950 px-4 py-5 text-white sm:px-6 sm:py-6">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <span class="rounded-full bg-white/15 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.22em]">Create account</span>
                <span class="rounded-full bg-white/10 px-3 py-1 text-[11px] font-medium">Premium signup</span>
            </div>
            <h2 class="mt-4 text-2xl font-bold tracking-tight sm:text-3xl">Join {{ $siteName }}</h2>
            <p class="mt-2 max-w-xl text-xs leading-6 text-red-50 sm:text-sm">
                Create your account in a few steps and start shopping right away.
            </p>
        </div>

        <form wire:submit="register" class="space-y-5 p-4 sm:p-6">
            <a href="{{ route('social.redirect', 'google') }}"
               class="flex min-h-11 w-full items-center justify-center gap-3 rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm font-semibold text-gray-700 shadow-sm transition hover:border-red-200 hover:bg-red-50">
                <span class="flex h-8 w-8 items-center justify-center rounded-full border border-gray-200 bg-white text-base font-bold text-red-500">G</span>
                <span>Continue with Google</span>
            </a>

            <div class="flex items-center gap-4">
                <div class="h-px flex-1 bg-gray-200"></div>
                <span class="text-[11px] font-semibold uppercase tracking-[0.2em] text-gray-400">or use email</span>
                <div class="h-px flex-1 bg-gray-200"></div>
            </div>

            <div>
                <label for="referralCode" class="form-label">Referral code (optional)</label>
                <input wire:model.defer="referralCode" id="referralCode" class="form-input" type="text" autocomplete="off">
                @error('referralCode')<p class="error-message">{{ $message }}</p>@enderror
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="name" class="form-label">Full Name</label>
                    <input wire:model.defer="name" id="name" class="form-input @error('name') form-input-error @enderror" type="text" name="name" required autofocus autocomplete="name" placeholder="Your full name">
                    @error('name')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="form-label">Email Address</label>
                    <input wire:model.defer="email" id="email" class="form-input @error('email') form-input-error @enderror" type="email" name="email" required autocomplete="username" placeholder="you@example.com">
                    @error('email')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div x-data="{ showPassword: false }">
                    <label for="password" class="form-label">Password</label>
                    <div class="relative">
                        <input wire:model.defer="password" id="password" class="form-input pr-12 @error('password') form-input-error @enderror" x-bind:type="showPassword ? 'text' : 'password'" name="password" required autocomplete="new-password" placeholder="Create a password">
                        <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 flex items-center pr-4 text-sm font-medium text-gray-400 transition hover:text-red-600">
                            <span x-text="showPassword ? 'Hide' : 'Show'"></span>
                        </button>
                    </div>
                    @error('password')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div x-data="{ showPassword: false }">
                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                    <div class="relative">
                        <input wire:model.defer="password_confirmation" id="password_confirmation" class="form-input pr-12 @error('password_confirmation') form-input-error @enderror" x-bind:type="showPassword ? 'text' : 'password'" name="password_confirmation" required autocomplete="new-password" placeholder="Repeat password">
                        <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 flex items-center pr-4 text-sm font-medium text-gray-400 transition hover:text-red-600">
                            <span x-text="showPassword ? 'Hide' : 'Show'"></span>
                        </button>
                    </div>
                    @error('password_confirmation')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-xs leading-6 text-gray-600 sm:text-sm">
                Your account lets you shop, save favourites, and track orders.
            </div>

            <div class="flex flex-col gap-4 pt-1">
                <button type="submit" class="btn-primary" wire:loading.attr="disabled" wire:loading.class="opacity-75">
                    <span wire:loading.remove wire:target="register">
                        Create Account
                    </span>
                    <span wire:loading wire:target="register" class="flex items-center justify-center">
                        <svg class="-ml-1 mr-3 h-5 w-5 animate-spin text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Creating account...
                    </span>
                </button>

                <p class="text-center text-sm text-gray-600">
                    Already have an account?
                    <a class="auth-link ml-1" href="{{ route('login') }}" wire:navigate>Log in</a>
                </p>
            </div>
        </form>
    </div>
</div>
