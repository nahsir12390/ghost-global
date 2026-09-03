@extends('layouts.app')

@section('title', 'Profile')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Profile') }}
    </h2>
@endsection

@section('content')
    <x-storefront.page-hero eyebrow="Your account" title="Profile settings." description="Keep your identity, security and shopping preferences up to date." />
    <div class="min-h-screen bg-[#f5f3ee] py-8 sm:py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Sidebar -->
            <div class="lg:col-span-1">
                <div class="overflow-hidden rounded-[2rem] bg-slate-950 text-white shadow-[0_28px_70px_-34px_rgba(15,23,42,.8)] lg:sticky lg:top-24">
                    <div class="relative p-6">
                    <div class="absolute -right-12 -top-16 h-44 w-44 rounded-full bg-red-600/30 blur-3xl" aria-hidden="true"></div>
                    <!-- Profile Picture Section -->
                    <div class="mb-6 text-center">
                        <div class="mb-4 flex justify-center">
                            @if(auth()->user()->profile_photo_path)
                                <img src="{{ asset('storage/' . auth()->user()->profile_photo_path) }}" 
                                     alt="{{ auth()->user()->name }}"
                                     class="relative h-24 w-24 rounded-[1.75rem] border-4 border-white/15 object-cover shadow-xl">
                            @else
                                <div class="relative flex h-24 w-24 items-center justify-center rounded-[1.75rem] border-4 border-white/10 bg-gradient-to-br from-red-500 to-red-700 text-3xl font-bold text-white shadow-xl">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        <h2 class="relative text-xl font-semibold text-white">{{ auth()->user()->name }}</h2>
                        <p class="relative mt-1 truncate text-sm text-slate-300">{{ auth()->user()->email }}</p>
                        <p class="relative mt-2 text-xs text-slate-500">
                            Member since {{ auth()->user()->created_at->format('M Y') }}
                        </p>
                    </div>

                    <!-- Stats -->
                    <div class="relative mb-6 grid grid-cols-2 gap-3 border-b border-white/10 pb-6">
                        <div class="rounded-2xl bg-white/[.06] p-3 text-center">
                            <p class="text-2xl font-bold text-white">{{ auth()->user()->orders()->count() }}</p>
                            <p class="mt-1 text-xs text-slate-400">Orders</p>
                        </div>
                        <div class="rounded-2xl bg-white/[.06] p-3 text-center">
                            <p class="text-2xl font-bold text-white">{{ auth()->user()->wishlists()->count() }}</p>
                            <p class="mt-1 text-xs text-slate-400">Wishlist</p>
                        </div>
                    </div>
                    
                    <div class="relative space-y-1 text-sm">
                        <a href="{{ route(auth()->user()->dashboardRouteName()) }}" 
                           class="block rounded-xl px-4 py-3 font-medium text-slate-300 transition hover:bg-white/10 hover:text-white">
                            {{ auth()->user()->isVendor() ? 'Vendor Dashboard' : (auth()->user()->isAdmin() ? 'Admin Dashboard' : 'Dashboard') }}
                        </a>
                        <a href="{{ route('my.orders') }}" 
                           class="block rounded-xl px-4 py-3 font-medium text-slate-300 transition hover:bg-white/10 hover:text-white">
                            My Orders
                        </a>
                        <a href="{{ route('wallet.index') }}"
                           class="block rounded-xl px-4 py-3 font-medium text-slate-300 transition hover:bg-white/10 hover:text-white">
                            My Wallet
                        </a>
                        <a href="{{ route('profile') }}" 
                           class="block rounded-xl bg-red-600 px-4 py-3 font-semibold text-white shadow-lg shadow-red-950/30">
                            Profile Settings
                        </a>
                        <div class="my-2 border-t border-white/10"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" 
                                    class="block w-full rounded-xl px-4 py-3 text-left font-medium text-slate-400 transition hover:bg-white/10 hover:text-white">
                                Log Out
                            </button>
                        </form>
                    </div></div>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Profile Picture Upload -->
                <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                    <div class="mb-6">
                        <h2 class="text-lg font-semibold text-gray-900">Profile Picture</h2>
                        <p class="mt-1 text-sm text-gray-600">
                            Upload a profile picture to personalize your account.
                        </p>
                    </div>
                    
                    <livewire:profile.update-profile-photo />
                </div>

                <!-- Profile Information -->
                <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                    <div class="mb-6">
                        <h2 class="text-lg font-semibold text-gray-900">Profile Information</h2>
                        <p class="mt-1 text-sm text-gray-600">
                            Update your account's profile information and email address.
                        </p>
                    </div>
                    
                    <livewire:profile.update-profile-information-form />
                </div>

                <!-- Update Password -->
                <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                    <div class="mb-6">
                        <h2 class="text-lg font-semibold text-gray-900">Update Password</h2>
                        <p class="mt-1 text-sm text-gray-600">
                            Ensure your account is using a long, random password to stay secure.
                        </p>
                    </div>
                    
                    <livewire:profile.update-password-form />
                </div>

                <!-- Delete Account -->
                <div class="rounded-[2rem] border border-red-200 bg-red-50/60 p-6 shadow-sm sm:p-8">
                    <div class="mb-6">
                        <h2 class="text-lg font-semibold text-gray-900">Delete Account</h2>
                        <p class="mt-1 text-sm text-gray-600">
                            Once your account is deleted, all of its resources and data will be permanently deleted.
                        </p>
                    </div>
                    
                    <livewire:profile.delete-user-form />
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection
