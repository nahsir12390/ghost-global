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
    public string $account_type = 'customer';
    public string $store_name = '';
    public string $phone = '';
    public string $address = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $referralCode = '';

    public function mount(): void
    {
        $this->referralCode = (string) session(ReferralService::SESSION_KEY, '');
    }

    protected function makeStoreSlug(string $storeName): string
    {
        $baseSlug = Str::slug($storeName);
        $slug = $baseSlug;
        $counter = 1;

        while (User::where('store_slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    public function register(): void
    {
        $this->referralCode = strtoupper(trim($this->referralCode));

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'account_type' => ['required', 'in:customer,vendor'],
            'store_name' => ['nullable', 'string', 'max:255', 'required_if:account_type,vendor'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:1000'],
            'referralCode' => ['nullable', 'string', 'max:32', 'exists:users,referral_code'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['role'] = $validated['account_type'];
        unset($validated['account_type']);

        if ($validated['role'] === 'vendor') {
            $validated['store_slug'] = $this->makeStoreSlug($validated['store_name']);
            $validated['verification_status'] = 'pending';
        } else {
            $validated['store_name'] = null;
            $validated['phone'] = null;
            $validated['address'] = null;
        }

        $validated['password'] = Hash::make($validated['password']);
        $validated['referral_code'] = User::generateUniqueReferralCode();

        event(new Registered($user = User::create($validated)));

        app(ReferralService::class)->assignReferrer($user, $this->referralCode);

        Auth::login($user);

        $destination = $user->isVendor() || $user->isAdmin()
            ? route('admin.dashboard', absolute: false)
            : route('dashboard', absolute: false);

        $this->redirect($destination, navigate: true);
    }
}; ?>

@php
    $siteName = \App\Helpers\SettingsHelper::get('site_name', config('app.name', 'E-Commerce'));
@endphp

<div class="space-y-5" x-data="{ accountType: @entangle('account_type'), showReferral: @js((bool) $referralCode) }">
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

            <div class="rounded-2xl border border-gray-200 bg-gray-50 p-3.5 sm:p-4">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-red-500">Account type</p>
                        <p class="mt-1 text-sm text-gray-500">Choose how you want to start.</p>
                    </div>
                    <button type="button" @click="showReferral = !showReferral" class="text-xs font-semibold text-gray-500 transition hover:text-red-600">
                        Referral code
                    </button>
                </div>

                <div class="mt-4 grid grid-cols-2 gap-3">
                    <label class="cursor-pointer rounded-2xl border p-3 transition"
                           :class="accountType === 'customer' ? 'border-red-500 bg-white shadow-sm' : 'border-gray-200 bg-white hover:border-red-200'">
                        <input type="radio" wire:model.change="account_type" x-model="accountType" value="customer" class="sr-only">
                        <div class="flex items-start gap-3">
                            <span class="mt-1 flex h-5 w-5 items-center justify-center rounded-full border"
                                  :class="accountType === 'customer' ? 'border-red-500' : 'border-gray-300'">
                                <span x-cloak x-show="accountType === 'customer'" class="h-2.5 w-2.5 rounded-full bg-red-500"></span>
                            </span>
                            <span>
                                <span class="block text-sm font-semibold text-gray-900">Customer</span>
                                <span class="mt-1 block text-xs leading-5 text-gray-500">Shop and track orders.</span>
                            </span>
                        </div>
                    </label>

                    <label class="cursor-pointer rounded-2xl border p-3 transition"
                           :class="accountType === 'vendor' ? 'border-red-500 bg-white shadow-sm' : 'border-gray-200 bg-white hover:border-red-200'">
                        <input type="radio" wire:model.change="account_type" x-model="accountType" value="vendor" class="sr-only">
                        <div class="flex items-start gap-3">
                            <span class="mt-1 flex h-5 w-5 items-center justify-center rounded-full border"
                                  :class="accountType === 'vendor' ? 'border-red-500' : 'border-gray-300'">
                                <span x-cloak x-show="accountType === 'vendor'" class="h-2.5 w-2.5 rounded-full bg-red-500"></span>
                            </span>
                            <span>
                                <span class="block text-sm font-semibold text-gray-900">Vendor</span>
                                <span class="mt-1 block text-xs leading-5 text-gray-500">Open a store profile.</span>
                            </span>
                        </div>
                    </label>
                </div>

                <div x-cloak x-show="showReferral" x-transition.opacity.duration.150ms class="mt-4 rounded-2xl border border-emerald-100 bg-white p-3.5">
                    <label for="referralCode" class="form-label mb-2 text-emerald-900">Referral Code</label>
                    <input wire:model.blur="referralCode"
                           id="referralCode"
                           class="form-input bg-white @error('referralCode') form-input-error @enderror"
                           type="text"
                           name="referralCode"
                           placeholder="Enter referral code if you have one">
                    @error('referralCode')
                        <p class="error-message">{{ $message }}</p>
                    @else
                        <p class="mt-2 text-xs leading-5 text-emerald-700">Optional. Leave empty if nobody referred you.</p>
                    @enderror
                </div>
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
                    <input wire:model.blur="email" id="email" class="form-input @error('email') form-input-error @enderror" type="email" name="email" required autocomplete="username" placeholder="you@example.com">
                    @error('email')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div x-cloak
                 x-show="accountType === 'vendor'"
                 x-transition.opacity.duration.150ms
                 class="grid gap-4 rounded-2xl border border-red-100 bg-red-50/50 p-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-red-500">Vendor setup</p>
                    <p class="mt-1 text-sm text-gray-500">Only the essentials for now.</p>
                </div>

                <div class="sm:col-span-2">
                    <label for="store_name" class="form-label">Store Name</label>
                    <input wire:model.defer="store_name" id="store_name" class="form-input @error('store_name') form-input-error @enderror" type="text" name="store_name" placeholder="e.g. Your Fashion Hub">
                    @error('store_name')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phone" class="form-label">Phone Number</label>
                    <input wire:model.defer="phone" id="phone" class="form-input @error('phone') form-input-error @enderror" type="text" name="phone" placeholder="+234...">
                    @error('phone')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="address" class="form-label">Business Address</label>
                    <input wire:model.defer="address" id="address" class="form-input @error('address') form-input-error @enderror" type="text" name="address" placeholder="Store or pickup address">
                    @error('address')
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
                <span x-show="accountType === 'vendor'">
                    Vendor accounts can enter the dashboard after signup, but store approval still happens from admin review.
                </span>
                <span x-show="accountType !== 'vendor'">
                    Customer accounts are ready immediately, and vendor access can be requested later from your account.
                </span>
            </div>

            <div class="flex flex-col gap-4 pt-1">
                <button type="submit" class="btn-primary" wire:loading.attr="disabled" wire:loading.class="opacity-75">
                    <span wire:loading.remove wire:target="register">
                        <span x-show="accountType === 'vendor'">Create Vendor Account</span>
                        <span x-show="accountType !== 'vendor'">Create Account</span>
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
