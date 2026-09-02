<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    public function login(): void
    {
        $this->form->authenticate();

        Session::regenerate();

        $this->redirect(route(Auth::user()->dashboardRouteName()), navigate: true);
    }
}; ?>

@php
    $siteName = \App\Helpers\SettingsHelper::get('site_name', config('app.name', 'E-Commerce'));
@endphp

<div class="space-y-5">
    @if(session('status'))
        <div class="rounded-2xl border border-blue-200 bg-blue-50 p-3.5 sm:p-4">
            <div class="flex items-start gap-3">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-blue-100">
                    <svg class="h-5 w-5 text-blue-500" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-blue-900">Notice</p>
                    <p class="mt-1 text-xs leading-6 text-blue-800 sm:text-sm">{{ session('status') }}</p>
                </div>
            </div>
        </div>
    @endif

    <div class="overflow-hidden rounded-[1.6rem] border border-red-100 bg-white shadow-sm">
        <div class="bg-gradient-to-br from-red-600 via-red-700 to-slate-950 px-4 py-5 text-white sm:px-6 sm:py-6">
            <div class="flex items-center justify-between gap-4">
                <span class="rounded-full bg-white/15 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.22em]">Login</span>
                <span class="rounded-full bg-white/10 px-3 py-1 text-[11px] font-medium">Fast access</span>
            </div>
            <h2 class="mt-4 text-2xl font-bold tracking-tight sm:text-3xl">Welcome back</h2>
            <p class="mt-2 max-w-xl text-xs leading-6 text-red-50 sm:text-sm">
                Log in to continue shopping, track your orders, and access your account faster.
            </p>
        </div>

        <div class="space-y-5 p-4 sm:p-6">
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

            <form wire:submit="login" class="space-y-4">
                <div>
                    <label for="email" class="form-label">Email Address</label>
                    <div class="input-group">
                        <svg class="input-icon h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12H8m8 0l-4 4m4-4l-4-4m8-2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6a2 2 0 012-2h12a2 2 0 012 2z" />
                        </svg>
                        <input wire:model.defer="form.email" id="email" class="form-input @error('form.email') form-input-error @enderror" type="email" name="email" required autofocus autocomplete="username" placeholder="you@example.com">
                    </div>
                    @error('form.email')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div x-data="{ showPassword: false }">
                    <div class="mb-2 flex items-center justify-between">
                        <label for="password" class="form-label mb-0">Password</label>
                        @if (Route::has('password.request'))
                            <a class="auth-link" href="{{ route('password.request') }}" wire:navigate>Forgot password?</a>
                        @endif
                    </div>
                    <div class="input-group">
                        <svg class="input-icon h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        <input wire:model.defer="form.password" id="password" class="form-input pr-12 @error('form.password') form-input-error @enderror" x-bind:type="showPassword ? 'text' : 'password'" name="password" required autocomplete="current-password" placeholder="Enter your password">
                        <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 flex items-center pr-4 text-sm font-medium text-gray-400 transition hover:text-red-600">
                            <span x-text="showPassword ? 'Hide' : 'Show'"></span>
                        </button>
                    </div>
                    @error('form.password')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-col gap-3 rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 sm:flex-row sm:items-center sm:justify-between">
                    <label for="remember" class="flex items-center">
                        <input wire:model.defer="form.remember" id="remember" type="checkbox" class="form-checkbox">
                        <span class="ml-2 text-sm text-gray-600">Remember me</span>
                    </label>
                    <span class="text-[11px] font-medium uppercase tracking-[0.2em] text-gray-400">Protected</span>
                </div>

                <button type="submit" class="btn-primary" wire:loading.attr="disabled" wire:loading.class="opacity-75">
                    <span wire:loading.remove wire:target="login">Log in</span>
                    <span wire:loading wire:target="login" class="flex items-center justify-center">
                        <svg class="-ml-1 mr-3 h-5 w-5 animate-spin text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Logging in...
                    </span>
                </button>
            </form>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white px-4 py-4 text-center shadow-sm sm:px-5">
        <p class="text-sm text-gray-600">
            New to {{ $siteName }}?
            <a href="{{ route('register') }}" class="auth-link ml-1" wire:navigate>Create an account</a>
        </p>
    </div>
</div>
