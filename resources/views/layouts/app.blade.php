<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $siteName = \App\Helpers\SettingsHelper::siteName();
        $siteDescription = \App\Helpers\SettingsHelper::siteDescription();
        $siteLogo = \App\Helpers\SettingsHelper::logoUrl();
        $currencyCode = \App\Helpers\SettingsHelper::currencyCode();
        $pageTitle = trim($__env->yieldContent('title', 'Home'));
        $metaTitle = trim($__env->yieldContent('meta_title', $pageTitle . ' - ' . $siteName));
        $metaDescription = trim($__env->yieldContent('meta_description', $siteDescription));
        $canonicalUrl = trim($__env->yieldContent('canonical', url()->current()));
        $shareImage = trim($__env->yieldContent('share_image', asset('storage/logo.png')));
    @endphp

    <title>{{ $metaTitle }}</title>
    <meta name="description" content="{{ $metaDescription }}">
    <link rel="canonical" href="{{ $canonicalUrl }}">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:title" content="{{ $metaTitle }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:image" content="{{ $shareImage }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $metaTitle }}">
    <meta name="twitter:description" content="{{ $metaDescription }}">
    <meta name="twitter:image" content="{{ $shareImage }}">
    <meta name="theme-color" content="#dc2626">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="{{ $siteName }}">
    <link rel="manifest" href="{{ route('pwa.manifest') }}">
    <link rel="apple-touch-icon" href="{{ route('pwa.icon', ['size' => 192]) }}">
    <link rel="icon" href="{{ \App\Helpers\SettingsHelper::faviconUrl() }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    @livewireStyles

    <style>
        [x-cloak] { display: none !important; }

        /* Custom red theme */
        .bg-red-theme { background-color: #dc2626; }
        .text-red-theme { color: #dc2626; }
        .border-red-theme { border-color: #dc2626; }
        .hover\:bg-red-theme:hover { background-color: #dc2626; }
        .hover\:text-red-theme:hover { color: #dc2626; }

        /* Button styles - Fixed CSS */
        .btn-primary {
            background-color: #dc2626;
            color: white;
            padding: 0.75rem 1rem;
            border-radius: 0.375rem;
            font-weight: 500;
            transition: background-color 0.2s ease-in-out;
        }

        .btn-primary:hover {
            background-color: #b91c1c;
        }

        .btn-primary:focus {
            outline: 2px solid transparent;
            outline-offset: 2px;
            box-shadow: 0 0 0 2px rgba(220, 38, 38, 0.5);
        }

        .btn-secondary {
            background-color: white;
            color: #374151;
            border: 1px solid #d1d5db;
            padding: 0.75rem 1rem;
            border-radius: 0.375rem;
            font-weight: 500;
            transition: background-color 0.2s ease-in-out;
        }

        .btn-secondary:hover {
            background-color: #f9fafb;
        }

        .btn-secondary:focus {
            outline: 2px solid transparent;
            outline-offset: 2px;
            box-shadow: 0 0 0 2px rgba(209, 213, 219, 0.5);
        }

        .badge-red {
            background-color: #fee2e2;
            color: #991b1b;
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
        }

        .badge-green {
            background-color: #d1fae5;
            color: #065f46;
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
        }

        .badge-yellow {
            background-color: #fef3c7;
            color: #92400e;
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
        }

        .badge-blue {
            background-color: #dbeafe;
            color: #1e40af;
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
        }

        /* Cart badge */
        .cart-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: #dc2626;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
        }

        /* Wishlist badge */
        .wishlist-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: #dc2626;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
        }

        .notification-badge {
            position: absolute;
            top: -6px;
            right: -6px;
            min-width: 18px;
            height: 18px;
            padding: 0 4px;
            background: linear-gradient(135deg, #dc2626, #ef4444);
            color: white;
            border-radius: 9999px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 700;
            box-shadow: 0 0 0 2px white;
        }

        /* Quantity controls */
        .quantity-btn {
            width: 2.5rem;
            height: 2.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #d1d5db;
            background-color: white;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .quantity-btn:hover {
            background-color: #f9fafb;
        }

        .quantity-btn.decrement {
            border-radius: 0.375rem 0 0 0.375rem;
            border-right: none;
        }

        .quantity-btn.increment {
            border-radius: 0 0.375rem 0.375rem 0;
            border-left: none;
        }

        .quantity-input {
            width: 3rem;
            height: 2.5rem;
            text-align: center;
            border: 1px solid #d1d5db;
            border-left: none;
            border-right: none;
            outline: none;
        }

        /* Cart controls in product cards */
        .cart-controls {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .cart-quantity {
            font-weight: 600;
            font-size: 1.125rem;
            min-width: 2rem;
            text-align: center;
        }

        /* WhatsApp Button Pulse Animation */
        @keyframes pulse-ring {
            0% {
                transform: scale(0.8);
                opacity: 0.5;
            }
            100% {
                transform: scale(1.3);
                opacity: 0;
            }
        }

        .whatsapp-pulse {
            animation: pulse-ring 1.5s cubic-bezier(0.215, 0.61, 0.355, 1) infinite;
        }
    </style>

</head>
<body class="h-full overflow-x-hidden bg-gray-50 font-sans antialiased">
    <div class="min-h-screen flex flex-col">
        <!-- Top Navigation -->
        <header
            x-data="{
                mobileMenuOpen: false,
                scrolled: false,
                init() {
                    this.scrolled = window.scrollY > 12;
                    this.$watch('mobileMenuOpen', (value) => {
                        document.body.classList.toggle('overflow-hidden', value);
                    });
                }
            }"
            @scroll.window="scrolled = window.scrollY > 12"
            @keydown.escape.window="mobileMenuOpen = false"
            :class="scrolled ? 'border-white/70 bg-white/90 shadow-[0_14px_45px_-24px_rgba(15,23,42,.35)] backdrop-blur-xl' : 'border-slate-200/80 bg-white'"
            class="sticky top-0 z-50 border-b transition-all duration-300"
        >
            <div class="mx-auto max-w-[90rem] px-4 sm:px-6 lg:px-8">
                <div class="flex h-[4.5rem] items-center justify-between">
                    <!-- Logo -->
                    <div class="min-w-0 flex-1 md:flex-none">
                        <a href="{{ route('home') }}" class="group flex min-w-0 items-center">
                            <span class="flex min-w-0 items-center gap-2">
                                <img src="{{ $siteLogo }}" alt="{{ $siteName }} logo" class="h-10 w-10 shrink-0 object-contain transition-transform duration-300 group-hover:-rotate-6 group-hover:scale-105">
                                <span class="truncate text-base font-bold tracking-[-.025em] text-slate-950 sm:text-xl">{{ $siteName }}</span>
                            </span>
                        </a>
                    </div>

                    <!-- Desktop Navigation -->
                    <nav class="hidden items-center rounded-full bg-[#101010] p-1.5 shadow-xl shadow-slate-900/10 xl:flex">
                        <a href="{{ route('home') }}"
                           class="rounded-full px-4 py-2 text-sm font-semibold transition {{ request()->routeIs('home') ? 'bg-white text-slate-950 shadow-sm' : 'text-white/60 hover:bg-white/10 hover:text-white' }}">
                            Home
                        </a>
                        <a href="{{ route('shop') }}"
                           class="rounded-full px-4 py-2 text-sm font-semibold transition {{ request()->routeIs('shop') ? 'bg-white text-slate-950 shadow-sm' : 'text-white/60 hover:bg-white/10 hover:text-white' }}">
                            Shop
                        </a>
                        <a href="{{ route('tracking.index') }}"
                           class="rounded-full px-4 py-2 text-sm font-semibold transition {{ request()->routeIs('tracking.*') ? 'bg-white text-slate-950 shadow-sm' : 'text-white/60 hover:bg-white/10 hover:text-white' }}">
                            Track Order
                        </a>
                        <a href="{{ route('about') }}"
                           class="rounded-full px-4 py-2 text-sm font-semibold transition {{ request()->routeIs('about') ? 'bg-white text-slate-950 shadow-sm' : 'text-white/60 hover:bg-white/10 hover:text-white' }}">
                            About
                        </a>
                        <a href="{{ route('contact') }}"
                           class="rounded-full px-4 py-2 text-sm font-semibold transition {{ request()->routeIs('contact') ? 'bg-white text-slate-950 shadow-sm' : 'text-white/60 hover:bg-white/10 hover:text-white' }}">
                            Contact
                        </a>
                    </nav>

                    <!-- Right side icons -->
                    <div class="ml-3 flex items-center gap-2 md:gap-4">
                        <!-- Cart (visible for all users) -->
                        <a href="{{ route('cart') }}" aria-label="Shopping cart" class="relative inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-700 transition hover:-translate-y-0.5 hover:border-red-200 hover:text-red-600 hover:shadow-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span data-cart-count class="cart-badge">{{ collect(session('cart', []))->sum('quantity') }}</span>
                        </a>

                        <!-- Wishlist -->
                            <a href="{{ route('wishlist') }}" aria-label="Wishlist" class="relative hidden h-10 w-10 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-700 transition hover:-translate-y-0.5 hover:border-red-200 hover:text-red-600 hover:shadow-lg md:inline-flex">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
                                @livewire('wishlist-counter')
                            </a>
                        @auth
                            @php
                                $navUser = Auth::user();
                                $activeOrderStatuses = ['pending', 'processing', 'packed', 'shipped', 'on_the_way'];
                                $activeOrdersCount = $navUser->orders()->whereIn('status', $activeOrderStatuses)->count();
                                $recentUserOrders = $navUser->orders()->latest()->take(3)->get();
                                $notificationCount = $activeOrdersCount;
                            @endphp

                            <div class="hidden items-center gap-2 xl:flex">
                                <a href="{{ route('wallet.index') }}"
                                   class="rounded-full border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 transition hover:border-red-200 hover:text-red-600">
                                    Wallet
                                </a>
                                <a href="{{ route('my.orders') }}"
                                   class="rounded-full border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 transition hover:border-red-200 hover:text-red-600">
                                    Orders
                                </a>
                                @if($navUser->canAccessBackoffice())
                                    <a href="{{ route($navUser->dashboardRouteName()) }}"
                                       class="rounded-full bg-gray-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-black">
                                        {{ $navUser->isStaff() ? 'Staff' : 'Dashboard' }}
                                    </a>
                                @else
                                    <a href="{{ route('dashboard') }}"
                                       class="rounded-full bg-gray-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-black">
                                        Dashboard
                                    </a>
                                @endif
                            </div>

                            <div x-data="{ notificationsOpen: false }" class="relative hidden sm:block">
                                <button @click="notificationsOpen = !notificationsOpen"
                                        class="relative rounded-full p-2 text-gray-700 transition hover:bg-red-50 hover:text-red-600 focus:outline-none">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                    </svg>
                                    @if($notificationCount > 0)
                                        <span class="notification-badge">{{ $notificationCount > 9 ? '9+' : $notificationCount }}</span>
                                    @endif
                                </button>

                                <div x-show="notificationsOpen"
                                     @click.away="notificationsOpen = false"
                                     x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="transform opacity-0 scale-95"
                                     x-transition:enter-end="transform opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="transform opacity-100 scale-100"
                                     x-transition:leave-end="transform opacity-0 scale-95"
                                     class="absolute right-0 z-20 mt-3 w-80 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-xl"
                                     style="display: none;">
                                    <div class="border-b border-gray-100 bg-gradient-to-r from-red-50 to-white px-4 py-4">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="text-sm font-semibold text-gray-900">Notifications</p>
                                                <p class="text-xs text-gray-500">Recent updates for your account</p>
                                            </div>
                                            @if($notificationCount > 0)
                                                <span class="rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-700">{{ $notificationCount }} active</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="max-h-96 overflow-y-auto px-4 py-3 space-y-3">


                                        @forelse($recentUserOrders as $order)
                                            <a href="{{ route('my.orders') }}" class="block rounded-xl border border-gray-200 p-3 transition hover:border-red-200 hover:bg-red-50/60">
                                                <div class="flex items-start justify-between gap-3">
                                                    <div>
                                                        <p class="text-sm font-semibold text-gray-900">{{ $order->order_number }}</p>
                                                        <p class="mt-1 text-xs text-gray-500">{{ $order->created_at->format('M d, Y') }}</p>
                                                    </div>
                                                    <span class="rounded-full px-2.5 py-1 text-[11px] font-semibold {{ in_array($order->status, $activeOrderStatuses) ? 'bg-blue-100 text-blue-700' : ($order->status === 'completed' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700') }}">
                                                        {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                                    </span>
                                                </div>
                                                <p class="mt-2 text-xs text-gray-600">
                                                    {{ in_array($order->status, $activeOrderStatuses) ? 'Your order is currently being processed.' : 'Order update available in your order history.' }}
                                                </p>
                                            </a>
                                        @empty

                                                <div class="rounded-xl border border-dashed border-gray-200 px-4 py-8 text-center">
                                                    <p class="text-sm font-medium text-gray-700">No new notifications</p>
                                                    <p class="mt-1 text-xs text-gray-500">Your account updates will appear here.</p>
                                                </div>

                                        @endforelse
                                    </div>

                                    <div class="border-t border-gray-100 px-4 py-3">
                                        <a href="{{ route('my.orders') }}" class="block rounded-xl bg-gray-900 px-4 py-2.5 text-center text-sm font-semibold text-white transition hover:bg-black">
                                            View My Orders
                                        </a>
                                    </div>
                                </div>
                            </div>

                        <!-- User Dropdown -->
                            <div x-data="{ open: false }" class="relative hidden sm:block">
                                <button @click="open = !open"
                                        class="flex items-center space-x-2 p-1 rounded-md hover:bg-gray-100 focus:outline-none">
                                    @if(Auth::user()->profile_photo_path)
                                        <img src="{{ asset('storage/' . Auth::user()->profile_photo_path) }}"
                                             alt="{{ Auth::user()->name }}"
                                             class="w-8 h-8 rounded-full object-cover border border-red-200">
                                    @else
                                        <div class="w-8 h-8 bg-red-100 text-red-600 rounded-full flex items-center justify-center font-semibold text-sm">
                                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                <div x-show="open" @click.away="open = false"
                                     x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="transform opacity-0 scale-95"
                                     x-transition:enter-end="transform opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="transform opacity-100 scale-100"
                                     x-transition:leave-end="transform opacity-0 scale-95"
                                     class="origin-top-right absolute right-0 mt-2 z-10 w-72 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-xl ring-1 ring-black ring-opacity-5"
                                     style="display: none;">
                                    <div class="max-h-[min(80vh,36rem)] overflow-y-auto">
                                        <div class="border-b border-gray-200 px-4 py-4">
                                            <p class="text-sm font-semibold text-gray-900">{{ Auth::user()->name }}</p>
                                            <p class="text-xs text-gray-600 mt-1">{{ Auth::user()->email }}</p>
                                            <div class="grid grid-cols-2 gap-2 mt-3">
                                                <a href="{{ route('my.orders') }}" class="text-center px-2 py-1 bg-red-50 rounded hover:bg-red-100 transition">
                                                    <p class="text-lg font-bold text-red-600">{{ Auth::user()->orders()->count() }}</p>
                                                    <p class="text-xs text-gray-600">Orders</p>
                                                </a>
                                                <a href="{{ route('wishlist') }}" class="text-center px-2 py-1 bg-red-50 rounded hover:bg-red-100 transition">
                                                    <p class="text-lg font-bold text-red-600">{{ Auth::user()->wishlists()->count() }}</p>
                                                    <p class="text-xs text-gray-600">Wishlist</p>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="px-2 py-2">
                                        <a href="{{ route('profile') }}"
                                           class="block rounded-xl px-3 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            Profile Settings
                                        </a>
                                        <a href="{{ route('my.orders') }}"
                                           class="block rounded-xl px-3 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            My Orders
                                        </a>
                                        <a href="{{ route('wallet.index') }}"
                                           class="block rounded-xl px-3 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            My Wallet
                                        </a>
                                        <a href="{{ route('my.downloads') }}"
                                           class="block rounded-xl px-3 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            My Downloads
                                        </a>
                                        <a href="{{ route('my.courses') }}"
                                           class="block rounded-xl px-3 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            My Courses
                                        </a>
                                        <a href="{{ route('wishlist') }}"
                                           class="block rounded-xl px-3 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            My Wishlist
                                        </a>


                                        @if(auth()->user()->canAccessBackoffice())
                                            <a href="{{ route(auth()->user()->dashboardRouteName()) }}"
                                               class="block rounded-xl px-3 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                {{ auth()->user()->isStaff() ? 'Staff Dashboard' : 'Admin Dashboard' }}
                                            </a>
                                        @endif
                                        </div>
                                        <div class="border-t border-gray-100 bg-white px-2 py-2">
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit"
                                                    class="block w-full rounded-xl px-3 py-2 text-left text-sm font-medium text-red-600 hover:bg-red-50">
                                                Log Out
                                            </button>
                                        </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <a href="{{ route('login') }}"
                               class="hidden text-sm font-semibold text-gray-700 hover:text-red-600 md:inline-flex">
                                Login
                            </a>
                            <a href="{{ route('register') }}"
                               class="hidden btn-primary text-sm md:inline-flex">
                                Register
                            </a>
                        @endauth

                        <!-- Mobile menu button -->
                       <div class="xl:hidden">
    <button @click="mobileMenuOpen = !mobileMenuOpen"
            :aria-label="mobileMenuOpen ? 'Close navigation' : 'Open navigation'"
            class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-[#101010] text-white transition hover:bg-red-600">
        <svg x-show="!mobileMenuOpen" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
        <svg x-show="mobileMenuOpen" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>
                       </div>
                    </div>
                </div>
            </div>

    <div x-show="mobileMenuOpen"
         x-cloak
         @click="mobileMenuOpen = false"
         class="fixed inset-0 top-[4.5rem] z-40 bg-slate-950/45 backdrop-blur-sm xl:hidden"
         style="display: none;"></div>

    <!-- Mobile menu panel -->
    <div x-show="mobileMenuOpen"
         x-cloak
          x-transition:enter="transition ease-out duration-200"
          x-transition:enter-start="opacity-0 scale-95"
          x-transition:enter-end="opacity-100 scale-100"
          x-transition:leave="transition ease-in duration-150"
          x-transition:leave-start="opacity-100 scale-100"
          x-transition:leave-end="opacity-0 scale-95"
          @click.away="mobileMenuOpen = false"
         class="fixed bottom-3 left-3 right-3 top-[5.25rem] z-50 overflow-hidden rounded-[2rem] border border-white/70 bg-white/95 shadow-2xl backdrop-blur-xl xl:hidden"
          style="display: none;">
        <div class="h-full space-y-2 overflow-y-auto overscroll-contain px-4 pb-8 pt-5">
            <a href="{{ route('home') }}"
               class="block rounded-xl px-4 py-3 text-gray-700 hover:text-red-600 hover:bg-gray-50 font-medium {{ request()->routeIs('home') ? 'text-red-600 bg-red-50' : '' }}"
               @click="mobileMenuOpen = false">
                Home
            </a>
            <a href="{{ route('shop') }}"
               class="block rounded-xl px-4 py-3 text-gray-700 hover:text-red-600 hover:bg-gray-50 font-medium {{ request()->routeIs('shop') ? 'text-red-600 bg-red-50' : '' }}"
               @click="mobileMenuOpen = false">
                Shop
            </a>
            <a href="{{ route('tracking.index') }}"
               class="block rounded-xl px-4 py-3 text-gray-700 hover:text-red-600 hover:bg-gray-50 font-medium {{ request()->routeIs('tracking.*') ? 'text-red-600 bg-red-50' : '' }}"
               @click="mobileMenuOpen = false">
                Track Order
            </a>
                <a href="{{ route('about') }}"
                   class="block rounded-xl px-4 py-3 text-gray-700 hover:text-red-600 hover:bg-gray-50 font-medium {{ request()->routeIs('about') ? 'text-red-600 bg-red-50' : '' }}"
                   @click="mobileMenuOpen = false">
                     About
                 </a>
                <a href="{{ route('contact') }}"
                   class="block rounded-xl px-4 py-3 text-gray-700 hover:text-red-600 hover:bg-gray-50 font-medium {{ request()->routeIs('contact') ? 'text-red-600 bg-red-50' : '' }}"
                   @click="mobileMenuOpen = false">
                     Contact
                 </a>


            <a href="{{ route('cart') }}"
               class="block rounded-xl px-4 py-3 text-gray-700 hover:text-red-600 hover:bg-gray-50 font-medium {{ request()->routeIs('cart') ? 'text-red-600 bg-red-50' : '' }}"
               @click="mobileMenuOpen = false">
                 Cart
             </a>
            @auth
                <div class="rounded-2xl border border-gray-200 bg-gradient-to-r from-gray-900 to-slate-800 px-4 py-4 text-white shadow-sm">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-sm font-semibold">{{ Auth::user()->name }}</p>
                            <p class="mt-1 text-xs text-slate-300">{{ Auth::user()->email }}</p>
                        </div>
                        <div class="h-10 w-10 rounded-2xl bg-white/10 flex items-center justify-center text-sm font-bold">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                    </div>

                    <div class="mt-4 grid grid-cols-1 gap-2 text-center min-[430px]:grid-cols-3">
                        <div class="rounded-xl bg-white/10 px-2 py-2">
                            <p class="text-lg font-bold">{{ Auth::user()->orders()->count() }}</p>
                            <p class="text-[11px] text-slate-300">Orders</p>
                        </div>
                        <div class="rounded-xl bg-white/10 px-2 py-2">
                            <p class="text-lg font-bold">{{ Auth::user()->wishlists()->count() }}</p>
                            <p class="text-[11px] text-slate-300">Wishlist</p>
                        </div>
                        <div class="rounded-xl bg-white/10 px-2 py-2">
                            <p class="text-sm font-bold">{{ \App\Helpers\SettingsHelper::currency(optional(Auth::user()->wallet)->balance ?? 0) }}</p>
                            <p class="text-[11px] text-slate-300">Wallet</p>
                        </div>
                    </div>
                </div>

                <a href="{{ route('wishlist') }}"
                   class="block rounded-xl px-4 py-3 text-gray-700 hover:text-red-600 hover:bg-gray-50 font-medium {{ request()->routeIs('wishlist') ? 'text-red-600 bg-red-50' : '' }}"
                   @click="mobileMenuOpen = false">
                     Wishlist
                 </a>

                <a href="{{ route('my.orders') }}"
                   class="block rounded-xl px-4 py-3 text-gray-700 hover:text-red-600 hover:bg-gray-50 font-medium {{ request()->routeIs('my.orders*') ? 'text-red-600 bg-red-50' : '' }}"
                   @click="mobileMenuOpen = false">
                     My Orders
                 </a>
                <a href="{{ route('wallet.index') }}"
                   class="block rounded-xl px-4 py-3 text-gray-700 hover:text-red-600 hover:bg-gray-50 font-medium {{ request()->routeIs('wallet.*') ? 'text-red-600 bg-red-50' : '' }}"
                   @click="mobileMenuOpen = false">
                     My Wallet
                 </a>
                <a href="{{ route('dashboard') }}"
                   class="block rounded-xl px-4 py-3 text-gray-700 hover:text-red-600 hover:bg-gray-50 font-medium {{ request()->routeIs('dashboard') ? 'text-red-600 bg-red-50' : '' }}"
                   @click="mobileMenuOpen = false">
                     Referral Dashboard
                 </a>
                <a href="{{ route('my.downloads') }}"
                   class="block rounded-xl px-4 py-3 text-gray-700 hover:text-red-600 hover:bg-gray-50 font-medium {{ request()->routeIs('my.downloads*') ? 'text-red-600 bg-red-50' : '' }}"
                   @click="mobileMenuOpen = false">
                     My Downloads
                 </a>
                <a href="{{ route('my.courses') }}"
                   class="block rounded-xl px-4 py-3 text-gray-700 hover:text-red-600 hover:bg-gray-50 font-medium {{ request()->routeIs('my.courses*') ? 'text-red-600 bg-red-50' : '' }}"
                   @click="mobileMenuOpen = false">
                     My Courses
                 </a>
                <div class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold text-gray-900">Notifications</p>
                            <p class="text-xs text-gray-500">Quick account updates</p>
                        </div>
                        @if($notificationCount > 0)
                            <span class="rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-700">{{ $notificationCount }}</span>
                        @endif
                    </div>

                    <div class="mt-3 space-y-2">


                        @forelse($recentUserOrders as $order)
                            <a href="{{ route('my.orders') }}"
                               class="block rounded-lg bg-white px-3 py-2 text-sm text-gray-700"
                               @click="mobileMenuOpen = false">
                                {{ $order->order_number }}: {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                            </a>
                        @empty

                                <p class="rounded-lg bg-white px-3 py-2 text-sm text-gray-500">No new notifications right now.</p>

                        @endforelse
                    </div>
                </div>
                @if(auth()->user()->canAccessBackoffice())
                    <a href="{{ route(auth()->user()->dashboardRouteName()) }}"
                       class="block rounded-xl px-4 py-3 text-gray-700 hover:text-red-600 hover:bg-gray-50 font-medium {{ request()->routeIs('admin.*') ? 'text-red-600 bg-red-50' : '' }}"
                       @click="mobileMenuOpen = false">
                        {{ auth()->user()->isStaff() ? 'Staff Dashboard' : 'Admin Dashboard' }}
                    </a>
                @endif

                <div class="sticky bottom-0 border-t border-gray-200 bg-white pt-3">
                    <a href="{{ route('profile') }}"
                       class="block rounded-xl px-4 py-3 text-gray-700 hover:text-red-600 hover:bg-gray-50 font-medium"
                       @click="mobileMenuOpen = false">
                        Profile
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="block w-full rounded-xl px-4 py-3 text-left text-gray-700 hover:text-red-600 hover:bg-gray-50 font-medium"
                                @click="mobileMenuOpen = false">
                            Log Out
                        </button>
                    </form>
                </div>
            @else
                <div class="border-t border-gray-200 pt-3">
                    <a href="{{ route('login') }}"
                       class="block rounded-xl px-4 py-3 text-gray-700 hover:text-red-600 hover:bg-gray-50 font-medium"
                       @click="mobileMenuOpen = false">
                        Login
                    </a>
                    <a href="{{ route('register') }}"
                       class="mt-2 block rounded-xl bg-red-600 px-4 py-3 text-center text-sm font-medium text-white transition hover:bg-red-700"
                       @click="mobileMenuOpen = false">
                        Register
                    </a>
                </div>
            @endauth
        </div>
    </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1">
            <!-- Page Heading -->
            @hasSection('header')
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        @yield('header')
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <div>
                @yield('content')
            </div>
        </main>

        <!-- Footer -->
        <footer class="mt-16 overflow-hidden bg-slate-950 text-white">
            @php
                $footerSiteName = \App\Helpers\SettingsHelper::siteName();
                $footerDescription = \App\Helpers\SettingsHelper::siteDescription();
                $footerAddress = \App\Helpers\SettingsHelper::get('site_address', 'Available online');
                $footerPhone = \App\Helpers\SettingsHelper::get('site_phone', '');
                $footerEmail = \App\Helpers\SettingsHelper::get('site_email', config('mail.from.address'));
                $storeShareUrl = route('home');
                $storeShareText = 'Shop with ' . $footerSiteName;
            @endphp

            <div class="border-b border-white/10 bg-[radial-gradient(circle_at_top_left,_rgba(239,68,68,0.25),_transparent_32%),radial-gradient(circle_at_top_right,_rgba(56,189,248,0.12),_transparent_24%)]">
                <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
                    <div class="flex flex-col gap-6 rounded-[2rem] border border-white/10 bg-white/5 p-6 shadow-2xl shadow-black/20 backdrop-blur sm:p-8 lg:flex-row lg:items-center lg:justify-between">
                        <div class="max-w-2xl">
                            <span class="inline-flex items-center gap-2 rounded-full border border-emerald-400/25 bg-emerald-500/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.24em] text-emerald-200"><span class="h-1.5 w-1.5 rounded-full bg-emerald-300"></span>Install ready</span>
                            <h2 class="mt-4 text-2xl font-semibold tracking-tight text-white sm:text-3xl">Your favourite store, now one tap away.</h2>
                            <p class="mt-3 text-sm leading-6 text-slate-300 sm:text-base">{{ $footerDescription }}</p>
                        </div>

                        <div class="grid gap-3 sm:grid-cols-2">
                            <a href="{{ route('shop') }}" class="inline-flex items-center justify-center rounded-2xl bg-white px-5 py-3 text-sm font-semibold text-slate-900 transition hover:bg-slate-100">
                                Explore Store
                            </a>
                            <button type="button" data-pwa-footer-install onclick="window.triggerStoreInstallPrompt?.()" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-red-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-red-950/30 transition hover:-translate-y-0.5 hover:bg-red-500">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v12m0 0 4-4m-4 4-4-4M5 19h14" /></svg>
                                <span data-pwa-install-label>Install App</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
                <div class="grid gap-10 lg:grid-cols-[1.25fr_0.8fr_0.8fr_1fr]">
                    <div>
                        <div class="flex items-center gap-3">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-red-500 to-red-700 text-lg font-bold shadow-lg shadow-red-950/40">
                                {{ strtoupper(substr($footerSiteName, 0, 1)) }}
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-white">{{ $footerSiteName }}</h3>
                                <p class="text-sm text-slate-400">Built for modern commerce.</p>
                            </div>
                        </div>

                        <p class="mt-5 max-w-md text-sm leading-7 text-slate-400">
                            Discover products faster, track orders more easily, and enjoy a storefront designed to feel smoother on desktop and mobile.
                        </p>

                        <div class="mt-6 flex flex-wrap gap-3">
                            <span class="inline-flex items-center rounded-full border border-emerald-400/20 bg-emerald-500/10 px-3 py-1 text-xs font-medium text-emerald-200">Secure checkout</span>
                            <span class="inline-flex items-center rounded-full border border-sky-400/20 bg-sky-500/10 px-3 py-1 text-xs font-medium text-sky-200">Responsive design</span>
                            <span class="inline-flex items-center rounded-full border border-amber-400/20 bg-amber-500/10 px-3 py-1 text-xs font-medium text-amber-200">Installable app</span>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-sm font-semibold uppercase tracking-[0.24em] text-slate-300">Navigate</h3>
                        <ul class="mt-5 space-y-3 text-sm text-slate-400">
                            <li><a href="{{ route('home') }}" class="transition hover:text-white">Home</a></li>
                            <li><a href="{{ route('shop') }}" class="transition hover:text-white">Shop</a></li>
                            <li><a href="{{ route('about') }}" class="transition hover:text-white">About Us</a></li>
                            <li><a href="{{ route('contact') }}" class="transition hover:text-white">Contact</a></li>
                            <li><a href="{{ route('tracking.index') }}" class="transition hover:text-white">Track Order</a></li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="text-sm font-semibold uppercase tracking-[0.24em] text-slate-300">Company</h3>
                        <ul class="mt-5 space-y-3 text-sm text-slate-400">
                            <li><a href="{{ route('privacy') }}" class="transition hover:text-white">Privacy Policy</a></li>
                            <li><a href="{{ route('terms') }}" class="transition hover:text-white">Terms of Service</a></li>
                            <li><a href="{{ route('wishlist') }}" class="transition hover:text-white">Wishlist</a></li>
                            <li><a href="{{ route('my.orders') }}" class="transition hover:text-white">My Orders</a></li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="text-sm font-semibold uppercase tracking-[0.24em] text-slate-300">Connect</h3>

                        <div class="mt-5 space-y-4 text-sm text-slate-400">
                            <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                <p class="font-medium text-white">Address</p>
                                <p class="mt-2 leading-6">{{ $footerAddress }}</p>
                            </div>

                            <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                <p class="font-medium text-white">Contact</p>
                                @if($footerPhone)<a href="tel:{{ preg_replace('/\s+/', '', $footerPhone) }}" class="mt-2 block transition hover:text-white">{{ $footerPhone }}</a>@endif
                                <a href="mailto:{{ $footerEmail }}" class="mt-1 block transition hover:text-white">{{ $footerEmail }}</a>
                            </div>

                            <div>
                                <p class="mb-3 font-medium text-white">Share the store</p>
                                <div class="flex flex-wrap gap-3">
                                    <a href="https://wa.me/?text={{ rawurlencode($storeShareText . ' ' . $storeShareUrl) }}" target="_blank" rel="noopener" class="inline-flex items-center justify-center rounded-2xl bg-emerald-500 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-400">
                                        WhatsApp
                                    </a>
                                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ rawurlencode($storeShareUrl) }}" target="_blank" rel="noopener" class="inline-flex items-center justify-center rounded-2xl bg-sky-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-sky-500">
                                        Facebook
                                    </a>
                                    <button type="button" onclick="navigator.clipboard.writeText('{{ $storeShareUrl }}').then(() => window.KeffiCart?.notify('Store link copied.', 'success'))" class="inline-flex items-center justify-center rounded-2xl border border-white/15 bg-white/5 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/10">
                                        Copy Link
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="relative mt-10 overflow-hidden rounded-[2rem] border border-white/10 bg-gradient-to-br from-red-600/20 via-white/[0.06] to-emerald-500/10 p-5 sm:p-7">
                    <div class="absolute -right-16 -top-20 h-56 w-56 rounded-full bg-red-500/15 blur-3xl" aria-hidden="true"></div>
                    <div class="relative grid gap-6 lg:grid-cols-[1fr_auto] lg:items-center">
                        <div class="flex items-start gap-4 sm:gap-5">
                            <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-red-500 to-red-700 shadow-xl shadow-red-950/40 sm:h-16 sm:w-16">
                                <svg class="h-7 w-7 text-white sm:h-8 sm:w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><rect x="6" y="2" width="12" height="20" rx="3" stroke-width="1.8"/><path stroke-linecap="round" stroke-width="1.8" d="M10 18h4" /></svg>
                            </div>
                            <div class="max-w-2xl">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="text-[10px] font-bold uppercase tracking-[0.22em] text-red-300">{{ $footerSiteName }} App</span>
                                    <span data-pwa-install-status class="rounded-full border border-emerald-400/20 bg-emerald-400/10 px-2.5 py-1 text-[10px] font-semibold text-emerald-200">Ready for your device</span>
                                </div>
                                <h3 class="mt-2 text-xl font-semibold tracking-tight text-white sm:text-2xl">Shop faster from your home screen.</h3>
                                <p class="mt-2 text-sm leading-6 text-slate-300">Install the secure web app for quick access, a focused full-screen experience and easier order tracking—without visiting an app store.</p>
                                <div class="mt-4 flex flex-wrap gap-x-5 gap-y-2 text-xs font-medium text-slate-300">
                                    <span class="inline-flex items-center gap-1.5"><span class="text-emerald-400">✓</span> Quick launch</span>
                                    <span class="inline-flex items-center gap-1.5"><span class="text-emerald-400">✓</span> Lightweight</span>
                                    <span class="inline-flex items-center gap-1.5"><span class="text-emerald-400">✓</span> No app store</span>
                                </div>
                            </div>
                        </div>

                        <button type="button" data-pwa-footer-install onclick="window.triggerStoreInstallPrompt?.()" class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-white px-6 py-3.5 text-sm font-bold text-slate-950 shadow-xl transition hover:-translate-y-0.5 hover:bg-red-50 disabled:cursor-default disabled:opacity-60 lg:w-auto">
                            <svg class="h-5 w-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v12m0 0 4-4m-4 4-4-4M5 19h14" /></svg>
                            <span data-pwa-install-label>Install {{ $footerSiteName }}</span>
                        </button>
                    </div>
                </div>

                <div class="mt-8 flex flex-col gap-3 border-t border-white/10 pt-6 text-sm text-slate-400 sm:flex-row sm:items-center sm:justify-between">
                    <p>&copy; {{ date('Y') }} {{ $footerSiteName }}. All rights reserved.</p>
                    <p>Optimized for responsive shopping, simpler discovery, and easier order tracking.</p>
                </div>
            </div>
        </footer>
    </div>

    @livewireScripts

    {{-- Livewire 3 ships with Alpine. Loading the CDN copy as well reinitializes Alpine
         and prevents Livewire controls such as pagination from receiving clicks. --}}

    <script>
        window.KeffiCart = {
            routes: {
                add: @js(route('cart.add')),
                update: @js(route('cart.update')),
                remove: @js(route('cart.remove')),
                clear: @js(route('cart.clear')),
                summary: @js(route('cart.summary')),
            },
            csrf: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '',
            money(amount) {
                const value = Number(amount || 0);
                return new Intl.NumberFormat('en-NG', {
                    style: 'currency',
                    currency: @js($currencyCode),
                    minimumFractionDigits: 2,
                }).format(value);
            },
            notify(message, type = 'success') {
                if (window.AppNotify) {
                    window.AppNotify(message, type);
                    return;
                }

                if (message) {
                    const toast = document.createElement('div');
                    toast.className = `fixed right-4 top-4 z-[9999] max-w-sm rounded-xl border px-4 py-3 text-sm shadow-lg ${
                        type === 'error'
                            ? 'border-red-200 bg-red-50 text-red-700'
                            : 'border-green-200 bg-green-50 text-green-700'
                    }`;
                    toast.textContent = message;
                    document.body.appendChild(toast);
                    setTimeout(() => toast.remove(), 3000);
                }
            },
            async request(url, payload = {}) {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrf,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify(payload),
                });

                const data = await response.json();

                if (!response.ok || data.success === false) {
                    throw new Error(data.message || 'Something went wrong.');
                }

                this.sync(data);
                return data;
            },
            async share(url, title = 'Product') {
                const absoluteUrl = new URL(url, window.location.origin).toString();

                if (navigator.share) {
                    await navigator.share({
                        title,
                        text: title,
                        url: absoluteUrl,
                    });
                    return;
                }

                await navigator.clipboard.writeText(absoluteUrl);
                this.notify('Product link copied.', 'success');
            },
            sync(data) {
                const cart = data.cart ?? { items: {}, summary: data.summary ?? {} };
                const summary = cart.summary ?? data.summary ?? {};
                const count = Number(summary.count || 0);

                document.querySelectorAll('[data-cart-count]').forEach((element) => {
                    element.textContent = count;
                    element.style.display = count > 0 ? 'flex' : 'none';
                });

                window.dispatchEvent(new CustomEvent('cart:changed', {
                    detail: {
                        productId: data.product_id ?? null,
                        item: data.item ?? null,
                        items: cart.items ?? {},
                        summary,
                    },
                }));
            },
            async add(productId, quantity = 1) {
                return this.request(this.routes.add, { product_id: productId, quantity });
            },
            async update(productId, quantity) {
                return this.request(this.routes.update, { product_id: productId, quantity });
            },
            async remove(productId) {
                return this.request(this.routes.remove, { product_id: productId });
            },
            async clear() {
                return this.request(this.routes.clear);
            },
        };
        window.KeffiCart.refreshSummary = async function () {
            try {
                const response = await fetch(this.routes.summary, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    this.sync(data);
                }
            } catch (error) {
                console.error('Failed to refresh cart summary.', error);
            }
        };

        document.addEventListener('alpine:init', () => {
            Alpine.data('cartCard', (config) => ({
                productId: Number(config.productId),
                stock: Number(config.stock || 0),
                quantity: Number(config.initialQuantity || 0),
                selectedQuantity: Number(config.initialSelectedQuantity || 1),
                busy: false,
                init() {
                    window.addEventListener('cart:changed', (event) => {
                        const items = event.detail.items || {};
                        const item = items[String(this.productId)] || null;
                        this.quantity = item ? Number(item.quantity || 0) : 0;
                    });
                },
                async run(callback) {
                    if (this.busy) {
                        return;
                    }

                    this.busy = true;

                    try {
                        const response = await callback();
                        window.KeffiCart.notify(response.message, 'success');
                    } catch (error) {
                        window.KeffiCart.notify(error.message, 'error');
                    } finally {
                        this.busy = false;
                    }
                },
                add(quantity = this.selectedQuantity) {
                    this.run(() => window.KeffiCart.add(this.productId, quantity));
                    this.selectedQuantity = 1;
                },
                increment() {
                    if (this.quantity >= this.stock) {
                        return;
                    }

                    this.run(() => window.KeffiCart.update(this.productId, this.quantity + 1));
                },
                decrement() {
                    if (this.quantity <= 1) {
                        this.remove();
                        return;
                    }

                    this.run(() => window.KeffiCart.update(this.productId, this.quantity - 1));
                },
                remove() {
                    this.run(() => window.KeffiCart.remove(this.productId));
                },
                increaseSelected() {
                    if (this.selectedQuantity < this.stock) {
                        this.selectedQuantity++;
                    }
                },
                decreaseSelected() {
                    if (this.selectedQuantity > 1) {
                        this.selectedQuantity--;
                    }
                },
            }));

            Alpine.data('cartPage', (config) => ({
                items: config.cart.items || {},
                summary: config.cart.summary || {},
                checkoutUrl: config.checkoutUrl,
                isAuthenticated: config.isAuthenticated,
                busy: {},
                clearing: false,
                init() {
                    window.addEventListener('cart:changed', (event) => {
                        this.items = event.detail.items || {};
                        this.summary = event.detail.summary || {};
                    });
                },
                get itemsArray() {
                    return Object.values(this.items || {});
                },
                money(amount) {
                    return window.KeffiCart.money(amount);
                },
                productUrl(slug) {
                    return `{{ url('/product') }}/${slug}`;
                },
                async mutate(productId, action) {
                    this.busy[productId] = true;

                    try {
                        const response = await action();
                        window.KeffiCart.notify(response.message, 'success');
                    } catch (error) {
                        window.KeffiCart.notify(error.message, 'error');
                    } finally {
                        this.busy[productId] = false;
                    }
                },
                increment(productId) {
                    const item = this.items[String(productId)];
                    if (!item || item.quantity >= item.stock) {
                        return;
                    }

                    this.mutate(productId, () => window.KeffiCart.update(productId, Number(item.quantity) + 1));
                },
                decrement(productId) {
                    const item = this.items[String(productId)];
                    if (!item) {
                        return;
                    }

                    const quantity = Number(item.quantity) - 1;
                    this.mutate(productId, () => window.KeffiCart.update(productId, Math.max(quantity, 0)));
                },
                remove(productId) {
                    this.mutate(productId, () => window.KeffiCart.remove(productId));
                },
                async clearCart() {
                    this.clearing = true;

                    try {
                        const response = await window.KeffiCart.clear();
                        window.KeffiCart.notify(response.message, 'success');
                    } catch (error) {
                        window.KeffiCart.notify(error.message, 'error');
                    } finally {
                        this.clearing = false;
                    }
                },
            }));
        });
    </script>
    <!-- Cart functionality script -->
    <script>
        document.addEventListener('livewire:initialized', () => {
            // Listen for cart events from related products
            window.addEventListener('cart-add', (event) => {
                const { productId } = event.detail;
                Livewire.dispatch('add-to-cart', { productId });
            });

            window.addEventListener('cart-increment', (event) => {
                const { productId } = event.detail;
                Livewire.dispatch('increment-quantity', { productId });
            });

            window.addEventListener('cart-decrement', (event) => {
                const { productId } = event.detail;
                Livewire.dispatch('decrement-quantity', { productId });
            });

            window.addEventListener('cart-remove', (event) => {
                const { productId } = event.detail;
                Livewire.dispatch('remove-from-cart', { productId });
            });
        });
    </script>

    <script>
    document.addEventListener('livewire:initialized', () => {
        // Listen for cart events from related products
        Livewire.on('cart-add', (event) => {
            const { productId } = event.detail;
            Livewire.dispatch('cart-add', { productId });
        });

        Livewire.on('cart-increment', (event) => {
            const { productId } = event.detail;
            Livewire.dispatch('cart-increment', { productId });
        });

        Livewire.on('cart-decrement', (event) => {
            const { productId } = event.detail;
            Livewire.dispatch('cart-decrement', { productId });
        });

        Livewire.on('cart-remove', (event) => {
            const { productId } = event.detail;
            Livewire.dispatch('cart-remove', { productId });
        });

        // Handle cart updated events
        Livewire.on('cartUpdated', () => {
            // Dispatch event to update cart counter
            Livewire.dispatch('cart-updated');
        });
    });
    </script>

    <script>
    document.addEventListener('livewire:initialized', () => {
        // Listen for wishlist events from product cards
        window.addEventListener('toggleWishlist', (event) => {
            const { productId } = event.detail;
            Livewire.dispatch('toggleWishlist', { productId });
        });

        // Listen for wishlist updated events
        Livewire.on('wishlist-updated', () => {
            // This will trigger the wishlist-counter to update
            Livewire.dispatch('refresh-wishlist');
        });
    });
    </script>

    @php
        $supportWhatsAppNumber = \App\Helpers\SettingsHelper::supportWhatsAppNumber();
        $supportSiteName = \App\Helpers\SettingsHelper::siteName();
        $supportContext = match (true) {
            request()->routeIs('product.show') => [
                'label' => 'Product assistance',
                'message' => 'Hi! I have a question about the product I am viewing: '.request()->fullUrl(),
            ],
            request()->routeIs('cart') => [
                'label' => 'Cart assistance',
                'message' => 'Hi! I need help with the items in my cart.',
            ],
            request()->routeIs('checkout*', 'payment.*') => [
                'label' => 'Checkout assistance',
                'message' => 'Hi! I need help completing my checkout or payment.',
            ],
            request()->routeIs('tracking.*') => [
                'label' => 'Order tracking',
                'message' => 'Hi! I need help tracking my order.',
            ],
            request()->routeIs('my.orders*') => [
                'label' => 'Order assistance',
                'message' => 'Hi! I need help with one of my orders.',
            ],
            request()->routeIs('contact*') => [
                'label' => 'Contact support',
                'message' => 'Hi! I visited the contact page and would like some help.',
            ],
            default => [
                'label' => 'Customer support',
                'message' => 'Hi! I need help shopping on '.$supportSiteName.'.',
            ],
        };
        $supportFaqs = [
            [
                'question' => 'How do I place an order?',
                'answer' => 'Open the product you want, tap "Add to Cart", then go to your cart and continue to checkout. Fill in your delivery details and choose your payment method.',
                'message' => 'Hi! I need help placing an order.',
            ],
            [
                'question' => 'How do I track my order?',
                'answer' => 'Tap "Track Order" in the menu. Enter your order number and the email you used when placing the order. You will then see the latest update.',
                'message' => 'Hi! I need help tracking my order.',
            ],
            [
                'question' => 'What payment methods can I use?',
                'answer' => 'Available payment methods depend on the current settings. You may see Wallet, Paystack, or Cash on Delivery if they are enabled for checkout.',
                'message' => 'Hi! I have a question about payment methods.',
            ],
            [
                'question' => 'What should I do if something is wrong?',
                'answer' => 'If your order, payment, account, or product has a problem, tap the WhatsApp button below and tell us what happened in simple words. We will guide you step by step.',
                'message' => 'Hi! I want to report an issue.',
            ],
        ];
    @endphp

    <div
        x-data='{
            isOpen: false,
            activeFaq: null,
            faqs: @json($supportFaqs),
            draftMessage: "",
            openFaq(index) {
                this.activeFaq = this.activeFaq === index ? null : index;
            },
            resetPanelScroll() {
                this.$nextTick(() => {
                    if (this.$refs.supportScroller) {
                        this.$refs.supportScroller.scrollTop = 0;
                    }
                });
            },
            contactSupport(message) {
                const outgoingMessage = (message || this.draftMessage || "Hi! I need help.").trim();
                window.open("https://wa.me/{{ $supportWhatsAppNumber }}?text=" + encodeURIComponent(outgoingMessage), "_blank", "noopener,noreferrer");
            },
            toggle() {
                this.isOpen = !this.isOpen;
                document.body.classList.toggle("overflow-hidden", this.isOpen && window.innerWidth < 640);
                if (this.isOpen) {
                    this.activeFaq = null;
                    this.resetPanelScroll();
                }
            },
            close() {
                this.isOpen = false;
                this.activeFaq = null;
                document.body.classList.remove("overflow-hidden");
                this.resetPanelScroll();
            }
        }'
        x-init="draftMessage = $el.dataset.contextMessage"
        data-context-message="{{ $supportContext['message'] }}"
        x-cloak
        @keydown.escape.window="close()"
        class="fixed bottom-4 right-4 z-[9999] sm:bottom-6 sm:right-6"
    >
        <div
            x-show="isOpen"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[9998] bg-slate-950/40 backdrop-blur-[2px] sm:hidden"
            @click="close()"
            style="display: none;"
        ></div>

        <div
            x-show="isOpen"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="translate-y-10 opacity-0 sm:translate-y-4 sm:scale-95"
            x-transition:enter-end="translate-y-0 opacity-100 sm:scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="translate-y-0 opacity-100 sm:scale-100"
            x-transition:leave-end="translate-y-8 opacity-0 sm:scale-95"
            @click.away="close()"
            class="fixed inset-x-0 bottom-0 z-[9999] flex max-h-[calc(100svh-0.75rem)] flex-col overflow-hidden rounded-t-[1.75rem] border border-gray-200 bg-white shadow-[0_-24px_60px_-24px_rgba(15,23,42,0.45)] sm:absolute sm:bottom-16 sm:right-0 sm:left-auto sm:inset-x-auto sm:max-h-[min(42rem,calc(100svh-2rem))] sm:w-[24rem] sm:max-w-[calc(100vw-2rem)] sm:rounded-[1.75rem]"
            style="display: none;"
        >
            <div class="relative overflow-hidden bg-gradient-to-br from-emerald-500 via-green-500 to-slate-900 px-4 pb-5 pt-4 text-white sm:px-5 sm:pb-6 sm:pt-5">
                <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,0.22) 1px, transparent 1px); background-size: 18px 18px;"></div>
                <div class="absolute -right-10 -top-10 h-28 w-28 rounded-full bg-white/10 blur-2xl"></div>
                <div class="absolute -left-8 bottom-0 h-24 w-24 rounded-full bg-emerald-300/15 blur-2xl"></div>

                <div class="relative">
                    <div class="mx-auto mb-3 h-1.5 w-14 rounded-full bg-white/25 sm:hidden"></div>

                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-white/14 backdrop-blur-sm">
                                <svg class="h-6 w-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12.032 2.001c-5.514 0-10 4.486-10 10 0 1.767.463 3.428 1.263 4.873L2 22l5.218-1.299c1.391.763 2.981 1.2 4.677 1.2 5.513 0 9.999-4.486 9.999-10 0-5.514-4.486-10-9.999-10zm0 18.6c-1.513 0-2.983-.405-4.239-1.167l-.304-.18-3.097.772.827-3.011-.197-.315a7.992 7.992 0 01-1.262-4.299c0-4.416 3.593-8.009 8.009-8.009 4.416 0 8.009 3.593 8.009 8.009 0 4.416-3.593 8.009-8.009 8.009z"/>
                                    <path d="M16.034 13.852c-.173-.086-1.02-.503-1.178-.56-.158-.058-.273-.086-.388.086s-.446.56-.547.675c-.101.115-.202.13-.375.043-.173-.086-.73-.269-1.39-.858-.514-.458-.861-1.023-.962-1.195-.101-.173-.011-.267.076-.353.078-.078.173-.202.26-.303.086-.101.115-.173.173-.288.058-.115.029-.216-.014-.303-.043-.086-.388-.936-.532-1.281-.14-.34-.282-.287-.388-.293-.101-.005-.216-.005-.332-.005-.115 0-.302.043-.46.216-.158.173-.605.591-.605 1.442 0 .85.619 1.672.706 1.788.086.115 1.218 1.86 2.952 2.608.412.178.734.285.985.365.414.13.79.112 1.087.068.332-.05 1.02-.417 1.164-.82.144-.403.144-.748.101-.82-.043-.072-.158-.115-.331-.201z"/>
                                </svg>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="h-2 w-2 rounded-full bg-emerald-200 shadow-[0_0_0_4px_rgba(167,243,208,0.16)]"></span>
                                    <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-white/80">Online support</p>
                                </div>
                                <h3 class="mt-1 text-lg font-semibold">{{ $supportSiteName }} Support</h3>
                                <p class="text-xs text-white/75">Usually replies as soon as possible</p>
                            </div>
                        </div>

                        <button type="button" @click="close()" aria-label="Close customer support" class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-white/10 text-white transition hover:bg-white/20">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="mt-4 rounded-2xl border border-white/10 bg-white/10 p-3.5 backdrop-blur-sm">
                        <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-emerald-100">Help for this page</p>
                        <p class="mt-1 text-sm font-semibold leading-6 text-white">{{ $supportContext['label'] }}</p>
                    </div>
                </div>
            </div>

            <div x-ref="supportScroller" class="min-h-0 flex-1 overflow-y-auto bg-slate-50 px-4 py-4 sm:px-5">
                <div class="mb-4 rounded-[1.4rem] border border-emerald-100 bg-white p-4 shadow-sm">
                    <label for="support-message" class="text-sm font-semibold text-slate-900">How can we help?</label>
                    <p class="mt-1 text-xs leading-5 text-slate-500">Edit the message if needed, then continue securely in WhatsApp.</p>
                    <textarea
                        id="support-message"
                        x-model="draftMessage"
                        rows="3"
                        maxlength="500"
                        class="mt-3 w-full resize-none rounded-2xl border border-slate-200 bg-slate-50 px-3.5 py-3 text-sm leading-6 text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-emerald-400 focus:bg-white focus:ring-4 focus:ring-emerald-100"
                        placeholder="Write your support message..."
                    ></textarea>
                    <button
                        type="button"
                        @click="contactSupport()"
                        :disabled="!draftMessage.trim()"
                        class="mt-3 inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-emerald-500 to-green-500 px-4 py-3.5 text-sm font-bold text-white shadow-[0_12px_24px_-12px_rgba(16,185,129,0.8)] transition hover:-translate-y-0.5 hover:from-emerald-600 hover:to-green-600 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M12.032 2.001c-5.514 0-10 4.486-10 10 0 1.767.463 3.428 1.263 4.873L2 22l5.218-1.299c1.391.763 2.981 1.2 4.677 1.2 5.513 0 9.999-4.486 9.999-10 0-5.514-4.486-10-9.999-10zm0 18.6c-1.513 0-2.983-.405-4.239-1.167l-.304-.18-3.097.772.827-3.011-.197-.315a7.992 7.992 0 01-1.262-4.299c0-4.416 3.593-8.009 8.009-8.009 4.416 0 8.009 3.593 8.009 8.009 0 4.416-3.593 8.009-8.009 8.009z"/>
                        </svg>
                        Start WhatsApp chat
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                    </button>
                    <p class="mt-2.5 text-center text-[11px] text-slate-400">WhatsApp will open in a new tab. No message is sent automatically.</p>
                </div>

                <div class="mb-2 flex items-center justify-between gap-3 px-1">
                    <p class="text-xs font-bold uppercase tracking-[0.16em] text-slate-500">Quick answers</p>
                    <span class="text-[11px] text-slate-400">Tap to expand</span>
                </div>
                <div class="space-y-2.5">
                    <template x-for="(faq, index) in faqs" :key="index">
                        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                            <button
                                type="button"
                                @click="openFaq(index)"
                                :aria-expanded="activeFaq === index"
                                class="flex w-full items-center justify-between gap-3 px-4 py-3.5 text-left transition hover:bg-slate-50"
                            >
                                <span class="pr-2 text-sm font-semibold leading-6 text-slate-800" x-text="faq.question"></span>
                                <svg class="h-4 w-4 shrink-0 text-slate-400 transition-transform" :class="{ 'rotate-180': activeFaq === index }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <div x-show="activeFaq === index" class="border-t border-slate-100 px-4 py-3.5">
                                <p class="text-sm leading-6 text-slate-600" x-text="faq.answer"></p>
                                <button
                                    type="button"
                                    @click="contactSupport(faq.message)"
                                    class="mt-3 inline-flex items-center justify-center rounded-full bg-emerald-50 px-3.5 py-2 text-xs font-semibold text-emerald-700 transition hover:bg-emerald-100"
                                >
                                    Continue on WhatsApp
                                </button>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="mt-4 rounded-[1.4rem] border border-slate-200 bg-white p-4 shadow-sm">
                    <p class="text-sm font-semibold text-slate-900">Popular support topics</p>

                    <div class="mt-4 grid gap-2.5">
                        <button
                            type="button"
                            @click="contactSupport('Hi! I need help with my order.')"
                            class="inline-flex items-center justify-center rounded-2xl bg-gradient-to-r from-emerald-500 to-green-500 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:from-emerald-600 hover:to-green-600"
                        >
                            Order Help
                        </button>
                        <button
                            type="button"
                            @click="contactSupport('Hi! I have a question about a product.')"
                            class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-100"
                        >
                            Product Question
                        </button>
                        <button
                            type="button"
                            @click="contactSupport('Hi! I have a general question.')"
                            class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                        >
                            General Support
                        </button>
                    </div>
                </div>
            </div>

            <div class="border-t border-slate-100 bg-white px-4 py-3 sm:px-5">
                <p class="text-[11px] text-slate-500 sm:text-xs">
                    Tip: keep your message short and clear so support can help you faster.
                </p>
            </div>
        </div>

        <button
            @click="toggle()"
            class="group relative flex h-12 w-12 items-center justify-center rounded-full bg-gradient-to-r from-emerald-500 to-green-500 shadow-lg transition-all duration-300 hover:scale-110 hover:shadow-2xl active:scale-95 sm:h-14 sm:w-14"
            aria-label="Open customer support"
            :aria-expanded="isOpen"
        >
            <span class="absolute inset-0 rounded-full bg-emerald-500 opacity-75 whatsapp-pulse"></span>
            <svg class="relative z-10 h-7 w-7 text-white sm:h-8 sm:w-8" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12.032 2.001c-5.514 0-10 4.486-10 10 0 1.767.463 3.428 1.263 4.873L2 22l5.218-1.299c1.391.763 2.981 1.2 4.677 1.2 5.513 0 9.999-4.486 9.999-10 0-5.514-4.486-10-9.999-10zm0 18.6c-1.513 0-2.983-.405-4.239-1.167l-.304-.18-3.097.772.827-3.011-.197-.315a7.992 7.992 0 01-1.262-4.299c0-4.416 3.593-8.009 8.009-8.009 4.416 0 8.009 3.593 8.009 8.009 0 4.416-3.593 8.009-8.009 8.009z"/>
                <path d="M16.034 13.852c-.173-.086-1.02-.503-1.178-.56-.158-.058-.273-.086-.388.086s-.446.56-.547.675c-.101.115-.202.13-.375.043-.173-.086-.73-.269-1.39-.858-.514-.458-.861-1.023-.962-1.195-.101-.173-.011-.267.076-.353.078-.078.173-.202.26-.303.086-.101.115-.173.173-.288.058-.115.029-.216-.014-.303-.043-.086-.388-.936-.532-1.281-.14-.34-.282-.287-.388-.293-.101-.005-.216-.005-.332-.005-.115 0-.302.043-.46.216-.158.173-.605.591-.605 1.442 0 .85.619 1.672.706 1.788.086.115 1.218 1.86 2.952 2.608.412.178.734.285.985.365.414.13.79.112 1.087.068.332-.05 1.02-.417 1.164-.82.144-.403.144-.748.101-.82-.043-.072-.158-.115-.331-.201z"/>
            </svg>
            <div class="pointer-events-none absolute right-full mr-3 hidden whitespace-nowrap opacity-0 transition-opacity duration-300 group-hover:opacity-100 sm:block">
                <div class="rounded-lg bg-slate-900 px-3 py-1.5 text-sm text-white shadow-lg">
                    Need help? Open support
                    <div class="absolute left-full top-1/2 -translate-y-1/2 transform border-4 border-transparent border-l-slate-900"></div>
                </div>
            </div>
        </button>
    </div>

    @guest
        @php
            $guestPromptSiteName = \App\Helpers\SettingsHelper::siteName();
            $guestPromptBenefits = [
                ['title' => 'Faster checkout', 'text' => 'Save your details for next time.'],
                ['title' => 'Track orders', 'text' => 'Check order updates anytime.'],
            ];
        @endphp

        <div
            x-data='{
                isOpen: false,
                storageKey: "guest-auth-prompt-dismissed-v1",
                init() {
                    try {
                        const dismissedAt = localStorage.getItem(this.storageKey);
                        const twelveHours = 1000 * 60 * 60 * 12;

                        if (dismissedAt && (Date.now() - Number(dismissedAt)) < twelveHours) {
                            return;
                        }

                        setTimeout(() => {
                            this.isOpen = true;
                            document.body.classList.add("overflow-hidden");
                        }, 1800);
                    } catch (error) {
                        setTimeout(() => {
                            this.isOpen = true;
                            document.body.classList.add("overflow-hidden");
                        }, 1800);
                    }
                },
                close(remember = true) {
                    this.isOpen = false;
                    document.body.classList.remove("overflow-hidden");

                    if (remember) {
                        try {
                            localStorage.setItem(this.storageKey, String(Date.now()));
                        } catch (error) {}
                    }
                }
            }'
            x-cloak
            @keydown.escape.window="close()"
        >
            <div
                x-show="isOpen"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 z-[9997] bg-slate-950/50 backdrop-blur-sm"
                style="display: none;"
                @click="close()"
            ></div>

            <div
                x-show="isOpen"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="translate-y-8 opacity-0 sm:translate-y-4 sm:scale-95"
                x-transition:enter-end="translate-y-0 opacity-100 sm:scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="translate-y-0 opacity-100 sm:scale-100"
                x-transition:leave-end="translate-y-6 opacity-0 sm:scale-95"
                class="fixed inset-x-4 bottom-4 z-[9998] sm:inset-x-0 sm:bottom-auto sm:left-1/2 sm:top-1/2 sm:w-full sm:max-w-md sm:-translate-x-1/2 sm:-translate-y-1/2 sm:px-4"
                style="display: none;"
            >
                <div @click.stop class="overflow-hidden rounded-[1.75rem] border border-white/70 bg-white shadow-[0_30px_80px_-30px_rgba(15,23,42,0.55)]">
                    <div class="relative overflow-hidden bg-gradient-to-br from-slate-950 via-slate-900 to-red-700 px-4 pb-4 pt-4 text-white sm:px-6 sm:pb-5 sm:pt-5">
                        <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,0.18) 1px, transparent 1px); background-size: 18px 18px;"></div>
                        <div class="absolute -right-10 top-0 h-28 w-28 rounded-full bg-red-400/25 blur-3xl"></div>
                        <div class="absolute -left-8 bottom-0 h-24 w-24 rounded-full bg-white/10 blur-2xl"></div>

                        <div class="relative">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-[11px] font-semibold uppercase tracking-[0.28em] text-red-100/80">Welcome In</p>
                                    <h3 class="mt-2 max-w-sm text-xl font-semibold tracking-tight sm:text-[1.7rem]">
                                        Shop faster with {{ $guestPromptSiteName }}.
                                    </h3>
                                    <p class="mt-2 max-w-md text-sm leading-5 text-white/80">
                                        Log in or create an account to save time next time you shop.
                                    </p>
                                </div>

                                <button
                                    type="button"
                                    @click="close()"
                                    class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-white/10 text-white transition hover:bg-white/20"
                                >
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white px-4 py-4 sm:px-6 sm:py-5">
                        <div class="grid gap-2 sm:grid-cols-2">
                            @foreach ($guestPromptBenefits as $benefit)
                                <div class="rounded-2xl border border-slate-200 bg-slate-50 px-3 py-2.5">
                                    <p class="text-sm font-semibold text-slate-900">{{ $benefit['title'] }}</p>
                                    <p class="mt-1 text-xs leading-4 text-slate-600">{{ $benefit['text'] }}</p>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-4 grid gap-2.5 sm:grid-cols-2">
                            <button
                                type="button"
                                @click="close(false); window.location.href = @js(route('register'))"
                                class="inline-flex items-center justify-center rounded-2xl bg-gradient-to-r from-red-600 to-rose-600 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:from-red-700 hover:to-rose-700"
                            >
                                Create Account
                            </button>
                            <button
                                type="button"
                                @click="close(false); window.location.href = @js(route('login'))"
                                class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-800 transition hover:bg-slate-50"
                            >
                                Log In
                            </button>
                        </div>

                        <button
                            type="button"
                            @click="close()"
                            class="mt-3 inline-flex text-sm font-medium text-slate-500 transition hover:text-slate-800"
                        >
                            Maybe later
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endguest

    @include('partials.pwa-install-prompt')
    @include('partials.toast-stack')

    @auth
        @if(filled(config('webpush.vapid.public_key')))
            <div
                x-data="{ visible: false, init() { this.visible = Notification.permission === 'default' && !localStorage.getItem('push-alert-prompt-dismissed') }, dismiss() { this.visible = false; localStorage.setItem('push-alert-prompt-dismissed', '1') } }"
                x-show="visible"
                x-cloak
                class="fixed bottom-4 left-4 z-[60] max-w-[calc(100vw-2rem)] sm:bottom-6 sm:left-6 sm:w-96"
            >
                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-2xl">
                    <p class="text-sm font-bold text-slate-900">Get order updates</p>
                    <p class="mt-1 text-xs leading-5 text-slate-500">Enable alerts for delivery and order-status changes on this device.</p>
                    <div class="mt-3 flex items-center gap-2">
                        <button type="button" @click="window.dispatchEvent(new Event('store:enable-push')); visible = false" class="rounded-xl bg-red-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-red-700">Enable alerts</button>
                        <button type="button" @click="dismiss()" class="px-2 py-2 text-xs font-semibold text-slate-500 hover:text-slate-800">Not now</button>
                    </div>
                </div>
            </div>

            <script>
                (() => {
                    const config = {
                        publicKey: @js(config('webpush.vapid.public_key')),
                        subscribeUrl: @js(route('push-subscriptions.store')),
                        csrf: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                    };

                    const decodeKey = (value) => {
                        const base64 = (value + '='.repeat((4 - value.length % 4) % 4)).replace(/-/g, '+').replace(/_/g, '/');
                        return Uint8Array.from(atob(base64), (character) => character.charCodeAt(0));
                    };

                    window.addEventListener('store:enable-push', async () => {
                        try {
                            if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
                                throw new Error('Push alerts are not supported by this browser.');
                            }

                            if (await Notification.requestPermission() !== 'granted') {
                                throw new Error('Notifications were not enabled. You can allow them later in browser settings.');
                            }

                            const registration = await navigator.serviceWorker.ready;
                            const subscription = await registration.pushManager.getSubscription() || await registration.pushManager.subscribe({
                                userVisibleOnly: true,
                                applicationServerKey: decodeKey(config.publicKey),
                            });
                            const payload = subscription.toJSON();
                            payload.endpoint = subscription.endpoint;

                            const response = await fetch(config.subscribeUrl, {
                                method: 'POST',
                                headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': config.csrf },
                                body: JSON.stringify(payload),
                            });
                            const result = await response.json();

                            if (!response.ok) {
                                throw new Error(result.message || 'Could not enable alerts.');
                            }

                            localStorage.removeItem('push-alert-prompt-dismissed');
                            window.KeffiCart?.notify(result.message, 'success');
                        } catch (error) {
                            window.KeffiCart?.notify(error.message || 'Could not enable alerts.', 'error');
                        }
                    });
                })();
            </script>
        @endif
    @endauth

    <!-- Custom Scripts -->
    @stack('scripts')
</body>
</html>
