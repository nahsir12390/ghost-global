@extends('layouts.app')

@section('title', 'Profile')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Profile') }}
    </h2>
@endsection

@section('content')
    <x-storefront.page-hero eyebrow="Your account" title="Profile settings." description="Keep your identity, security and shopping preferences up to date." />
    <div class="bg-[#f5f3ee] py-8 sm:py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Sidebar -->
            <div class="lg:col-span-1">
                <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm lg:sticky lg:top-24">
                    <!-- Profile Picture Section -->
                    <div class="mb-6 text-center">
                        <div class="mb-4 flex justify-center">
                            @if(auth()->user()->profile_photo_path)
                                <img src="{{ asset('storage/' . auth()->user()->profile_photo_path) }}" 
                                     alt="{{ auth()->user()->name }}"
                                     class="w-20 h-20 rounded-full object-cover border-4 border-red-100">
                            @else
                                <div class="w-20 h-20 bg-red-100 text-red-600 rounded-full flex items-center justify-center text-3xl font-semibold border-4 border-red-50">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        <h2 class="text-lg font-semibold text-gray-900">{{ auth()->user()->name }}</h2>
                        <p class="text-sm text-gray-500">{{ auth()->user()->email }}</p>
                        <p class="text-xs text-gray-400 mt-1">
                            Member since {{ auth()->user()->created_at->format('M Y') }}
                        </p>
                    </div>

                    <!-- Stats -->
                    <div class="grid grid-cols-2 gap-3 mb-6 pb-6 border-b border-gray-200">
                        <div class="text-center">
                            <p class="text-2xl font-bold text-red-600">{{ auth()->user()->orders()->count() }}</p>
                            <p class="text-xs text-gray-600 mt-1">Orders</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-red-600">{{ auth()->user()->wishlists()->count() }}</p>
                            <p class="text-xs text-gray-600 mt-1">Wishlist</p>
                        </div>
                    </div>
                    
                    <div class="space-y-1">
                        <a href="{{ route(auth()->user()->dashboardRouteName()) }}" 
                           class="block px-4 py-2 text-gray-700 hover:bg-red-50 hover:text-red-600 rounded-md font-medium {{ auth()->user()->canAccessBackoffice() ? (request()->routeIs('admin.*') ? 'bg-red-50 text-red-600' : '') : (request()->routeIs('user.dashboard') ? 'bg-red-50 text-red-600' : '') }}">
                            {{ auth()->user()->isVendor() ? 'Vendor Dashboard' : (auth()->user()->isAdmin() ? 'Admin Dashboard' : 'Dashboard') }}
                        </a>
                        <a href="{{ route('my.orders') }}" 
                           class="block px-4 py-2 text-gray-700 hover:bg-red-50 hover:text-red-600 rounded-md font-medium {{ request()->routeIs('my.orders*') ? 'bg-red-50 text-red-600' : '' }}">
                            My Orders
                        </a>
                        <a href="{{ route('wallet.index') }}"
                           class="block px-4 py-2 text-gray-700 hover:bg-red-50 hover:text-red-600 rounded-md font-medium {{ request()->routeIs('wallet.*') ? 'bg-red-50 text-red-600' : '' }}">
                            My Wallet
                        </a>
                        <a href="{{ route('profile') }}" 
                           class="block px-4 py-2 bg-red-50 text-red-600 rounded-md font-medium">
                            Profile Settings
                        </a>
                        <div class="border-t border-gray-200 my-2"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" 
                                    class="block w-full text-left px-4 py-2 text-gray-700 hover:bg-red-50 hover:text-red-600 rounded-md font-medium">
                                Log Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Profile Picture Upload -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="mb-6">
                        <h2 class="text-lg font-semibold text-gray-900">Profile Picture</h2>
                        <p class="mt-1 text-sm text-gray-600">
                            Upload a profile picture to personalize your account.
                        </p>
                    </div>
                    
                    <livewire:profile.update-profile-photo />
                </div>

                <!-- Profile Information -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="mb-6">
                        <h2 class="text-lg font-semibold text-gray-900">Profile Information</h2>
                        <p class="mt-1 text-sm text-gray-600">
                            Update your account's profile information and email address.
                        </p>
                    </div>
                    
                    <livewire:profile.update-profile-information-form />
                </div>

                <!-- Update Password -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="mb-6">
                        <h2 class="text-lg font-semibold text-gray-900">Update Password</h2>
                        <p class="mt-1 text-sm text-gray-600">
                            Ensure your account is using a long, random password to stay secure.
                        </p>
                    </div>
                    
                    <livewire:profile.update-password-form />
                </div>

                <!-- Delete Account -->
                <div class="bg-white rounded-lg shadow-sm p-6">
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
