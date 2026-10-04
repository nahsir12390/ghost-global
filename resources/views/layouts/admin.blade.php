<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') - {{ \App\Helpers\SettingsHelper::get('site_name', config('app.name', 'Laravel')) }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Styles -->
    @vite(['resources/css/app.css'])

    @livewireStyles

    <style>
        /* Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        [x-cloak] {
            display: none !important;
        }

        body {
            font-family: 'Figtree', sans-serif;
            background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: #dc2626;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #b91c1c;
        }

        /* Sidebar Transitions */
        .sidebar-enter-active,
        .sidebar-leave-active {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar-enter-from,
        .sidebar-leave-to {
            transform: translateX(-100%);
            opacity: 0;
        }

        .sidebar-enter-to,
        .sidebar-leave-from {
            transform: translateX(0);
            opacity: 1;
        }

        /* Modern Card Styles */
        .card-modern {
            @apply bg-white rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300;
        }

        /* Button Styles - Modern */
        .btn-primary {
            @apply bg-gradient-to-r from-red-600 to-red-700 text-white px-5 py-2.5 rounded-xl font-medium
                   hover:from-red-700 hover:to-red-800 focus:outline-none focus:ring-2 focus:ring-red-500
                   focus:ring-offset-2 transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98];
        }

        .btn-secondary {
            @apply bg-white text-gray-700 border border-gray-300 px-5 py-2.5 rounded-xl font-medium
                   hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500
                   focus:ring-offset-2 transition-all duration-200;
        }

        .btn-danger {
            @apply bg-gradient-to-r from-red-600 to-red-700 text-white px-5 py-2.5 rounded-xl font-medium
                   hover:from-red-700 hover:to-red-800 focus:outline-none focus:ring-2 focus:ring-red-500
                   transition-all duration-200;
        }

        /* Badge Styles */
        .badge-red {
            @apply bg-red-100 text-red-800 text-xs font-semibold px-2.5 py-1 rounded-full;
        }

        .badge-green {
            @apply bg-green-100 text-green-800 text-xs font-semibold px-2.5 py-1 rounded-full;
        }

        .badge-yellow {
            @apply bg-yellow-100 text-yellow-800 text-xs font-semibold px-2.5 py-1 rounded-full;
        }

        .badge-blue {
            @apply bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-1 rounded-full;
        }

        /* Form Input Styles */
        .form-input-modern {
            @apply w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-red-500
                   focus:ring-2 focus:ring-red-200 transition-all duration-200 outline-none;
        }

        /* Glassmorphism Effects */
        .glass-effect {
            @apply bg-white/80 backdrop-blur-sm border border-white/20;
        }

        /* Loading Animation */
        @keyframes pulse-ring {
            0% {
                transform: scale(0.8);
                opacity: 0.5;
            }
            100% {
                transform: scale(1.2);
                opacity: 0;
            }
        }

        .notification-dot {
            position: relative;
        }

        .notification-dot::after {
            content: '';
            position: absolute;
            top: -2px;
            right: -2px;
            width: 8px;
            height: 8px;
            background-color: #ef4444;
            border-radius: 50%;
            animation: pulse-ring 1.5s cubic-bezier(0.215, 0.61, 0.355, 1) infinite;
        }

        /* Responsive Table */
        .responsive-table {
            @apply w-full overflow-x-auto;
        }

        @media (max-width: 768px) {
            .responsive-table table {
                @apply min-w-[600px];
            }
        }

        /* Custom Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in-up {
            animation: fadeInUp 0.5s ease-out;
        }
    </style>

</head>
<body class="font-sans antialiased bg-gradient-to-br from-gray-50 to-gray-100 h-full">
    <div x-data="{
        sidebarOpen: false,
        isMobile: window.innerWidth < 1024,
        init() {
            // Set initial sidebar state based on screen size
            this.sidebarOpen = !this.isMobile;
            document.body.classList.toggle('overflow-hidden', this.sidebarOpen && this.isMobile);

            // Watch for window resize
            window.addEventListener('resize', () => {
                this.isMobile = window.innerWidth < 1024;
                if (!this.isMobile) {
                    this.sidebarOpen = true;
                }
                document.body.classList.toggle('overflow-hidden', this.sidebarOpen && this.isMobile);
            });

            this.$watch('sidebarOpen', (value) => {
                document.body.classList.toggle('overflow-hidden', value && this.isMobile);
            });
        }
    }"
    x-init="init()"
    class="min-h-screen lg:flex relative">

        <!-- Sidebar Backdrop (Mobile) -->
        <div x-show="sidebarOpen && isMobile"
             x-cloak
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="sidebarOpen = false"
             class="fixed inset-0 z-20 bg-black/45 lg:hidden"></div>

        <!-- Sidebar -->
        <aside x-show="sidebarOpen"
               x-cloak
               x-transition:enter="transform transition-all duration-300 ease-out"
               x-transition:enter-start="-translate-x-full"
               x-transition:enter-end="translate-x-0"
               x-transition:leave="transform transition-all duration-300 ease-in"
               x-transition:leave-start="translate-x-0"
               x-transition:leave-end="-translate-x-full"
               class="fixed inset-y-0 left-0 z-30 w-80 max-w-[85vw] bg-gradient-to-b from-gray-900 to-gray-800 shadow-2xl flex flex-col lg:sticky lg:top-0 lg:h-screen lg:w-72 lg:max-w-none lg:flex-shrink-0"
                :class="{'shadow-2xl': sidebarOpen}">

            <!-- Sidebar Header -->
            <div class="flex items-center justify-between h-20 px-6 bg-gradient-to-r from-gray-900 to-gray-800 border-b border-gray-700/50">
                <div class="flex items-center space-x-3">
                    <img src="{{ \App\Helpers\SettingsHelper::logoUrl() }}" alt="{{ \App\Helpers\SettingsHelper::siteName() }} logo" class="h-10 w-10 object-contain shadow-lg">
                    <a href="{{ route(Auth::user()->dashboardRouteName()) }}" class="flex items-center">
                        @if(file_exists(public_path('storage/logo.png')))
                            <img src="{{ asset('storage/logo.png') }}" alt="Logo" class="h-8 w-auto">
                        @else
                            <span class="text-xl font-bold bg-gradient-to-r from-white to-gray-300 bg-clip-text text-transparent">
                                {{ \App\Helpers\SettingsHelper::get('site_name', config('app.name', 'Admin')) }}
                            </span>
                        @endif
                    </a>
                </div>
                <button @click="sidebarOpen = false"
                        class="lg:hidden p-2 rounded-lg text-gray-400 hover:text-white hover:bg-gray-700/50 transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Navigation -->
            @if(Auth::user()->isAdmin())<a href="{{ route('admin.settings.delivery') }}" class="mx-4 mt-4 block rounded-lg bg-white/10 px-4 py-3 text-sm font-semibold text-white">Worldwide delivery</a>@endif
            <nav class="flex-1 px-4 py-6 overflow-y-auto overscroll-contain">
                @php
                    $sidebarUser = Auth::user();
                    $sidebarPendingOrders = \App\Models\Order::visibleTo($sidebarUser)
                        ->whereIn('status', ['ordered', 'pending', 'processing'])
                        ->count();
                    $sidebarNotificationTotal = $sidebarPendingOrders;
                @endphp

                <div class="space-y-1">
                    @if($sidebarNotificationTotal > 0)
                        <div class="mb-5 rounded-xl border border-red-500/20 bg-red-500/10 p-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="relative flex h-2.5 w-2.5">
                                        <span class="absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75 animate-ping"></span>
                                        <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-red-500"></span>
                                    </span>
                                    <p class="text-xs font-semibold uppercase tracking-wide text-red-100">Notifications</p>
                                </div>
                                <span class="rounded-full bg-red-600 px-2 py-0.5 text-xs font-bold text-white">{{ $sidebarNotificationTotal }}</span>
                            </div>
                            <div class="mt-3 space-y-2 text-xs text-red-50">
                                @if($sidebarPendingOrders > 0)
                                    <a href="{{ $sidebarUser->isStaff() && $sidebarUser->canManageOrders() ? route('admin.staff.orders.index') : route('admin.orders.index') }}" class="flex items-center justify-between rounded-lg bg-white/10 px-3 py-2 hover:bg-white/15">
                                        <span>Orders need attention</span>
                                        <span class="font-bold">{{ $sidebarPendingOrders }}</span>
                                    </a>
                                @endif

                            </div>
                        </div>
                    @endif

                    <div class="mb-4 rounded-2xl border border-white/10 bg-white/5 px-4 py-4 text-white shadow-lg shadow-black/10">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-gray-400">
                            {{ $sidebarUser->isAdmin() ? 'Admin Workspace' : 'Staff Workspace' }}
                        </p>
                        <p class="mt-2 text-sm font-semibold">{{ $sidebarUser->name }}</p>
                        <p class="mt-1 text-xs text-gray-400">
                            {{ $sidebarUser->isAdmin() ? 'Manage the store from one place.' : 'Handle the tasks assigned to your staff role.' }}
                        </p>
                        <div class="mt-4 grid grid-cols-2 gap-2">
                            <a href="{{ route($sidebarUser->dashboardRouteName()) }}" class="inline-flex items-center justify-center rounded-xl bg-white/10 px-3 py-2 text-xs font-semibold text-white transition hover:bg-white/15">
                                Dashboard
                            </a>
                            <a href="{{ $sidebarUser->storefrontUrl() ?: route('home') }}" class="inline-flex items-center justify-center rounded-xl border border-white/10 bg-black/10 px-3 py-2 text-xs font-semibold text-gray-100 transition hover:bg-white/10">
                                {{ $sidebarUser->storefrontUrl() ? 'My Store' : 'View Site' }}
                            </a>
                        </div>
                    </div>

                    @if(!Auth::user()->isStaff())
                        <p class="px-4 pt-2 text-[11px] font-semibold uppercase tracking-[0.24em] text-gray-500">Overview</p>
                    @endif

                    <!-- Dashboard (Only for Admin/Staff) -->
                    @if(!Auth::user()->isStaff())
                    <a href="{{ route(Auth::user()->dashboardRouteName()) }}"
                       class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group
                              {{ request()->routeIs('admin.dashboard')
                                  ? 'bg-gradient-to-r from-red-600 to-red-700 text-white shadow-lg'
                                  : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3 transition-transform duration-200 group-hover:scale-110"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Dashboard
                    </a>
                    @endif

                    <!-- Staff Orders Link (Only for Staff with Order Manager) -->
                    @if(Auth::user()->isStaff() && Auth::user()->isOrderManager())
                    <a href="{{ route('admin.orders.index') }}"
                       class="flex items-center justify-between px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group
                              {{ request()->routeIs('admin.orders.*')
                                  ? 'bg-gradient-to-r from-red-600 to-red-700 text-white shadow-lg'
                                  : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3 transition-transform duration-200 group-hover:scale-110"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            Orders Management
                        </div>
                        @if($sidebarPendingOrders)
                            <span class="bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                                {{ $sidebarPendingOrders }}
                            </span>
                        @endif
                    </a>
                    @endif

                    <!-- Staff Products Link (Only for Staff with Product Manager) -->
                    @if(Auth::user()->isStaff() && Auth::user()->isProductManager())
                    <a href="{{ route('admin.products.index') }}"
                       class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group
                              {{ request()->routeIs('admin.products.*')
                                  ? 'bg-gradient-to-r from-red-600 to-red-700 text-white shadow-lg'
                                  : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3 transition-transform duration-200 group-hover:scale-110"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                        </svg>
                        Products Management
                    </a>
                    @endif

                    @if(!Auth::user()->isStaff())
                        <p class="px-4 pt-4 text-[11px] font-semibold uppercase tracking-[0.24em] text-gray-500">Commerce</p>
                    @endif

                    <!-- Products Dropdown (Only for Admin/Staff) -->
                    @if(!Auth::user()->isStaff())
                    <div x-data="{ open: {{ request()->routeIs('admin.products.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open"
                                class="w-full flex items-center justify-between px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 text-gray-300 hover:bg-gray-700/50 hover:text-white group">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3 transition-transform duration-200 group-hover:scale-110"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                </svg>
                                Products
                            </div>
                            <svg :class="{'rotate-180': open}" class="w-4 h-4 transition-transform duration-200"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="open"
                             x-transition:enter="transition-all duration-200 ease-out"
                             x-transition:enter-start="opacity-0 transform -translate-y-2"
                             x-transition:enter-end="opacity-100 transform translate-y-0"
                             x-transition:leave="transition-all duration-150 ease-in"
                             x-transition:leave-start="opacity-100 transform translate-y-0"
                             x-transition:leave-end="opacity-0 transform -translate-y-2"
                             class="ml-8 mt-1 space-y-1">
                            <a href="{{ route('admin.products.index') }}"
                               class="block px-4 py-2 text-sm rounded-lg transition-all duration-200
                                      {{ request()->routeIs('admin.products.index')
                                          ? 'text-red-400 bg-gray-700/30'
                                          : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">
                                All Products
                            </a>
                            <a href="{{ route('admin.products.create') }}"
                               class="block px-4 py-2 text-sm rounded-lg transition-all duration-200
                                      {{ request()->routeIs('admin.products.create')
                                          ? 'text-red-400 bg-gray-700/30'
                                          : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">
                                Add New
                            </a>
                            @if(Auth::user()->isAdmin())
                                <a href="{{ route('admin.categories.index') }}"
                                   class="block px-4 py-2 text-sm rounded-lg transition-all duration-200
                                          {{ request()->routeIs('admin.categories.*')
                                              ? 'text-red-400 bg-gray-700/30'
                                              : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">
                                    Categories
                                </a>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Orders (Only for Admin/Staff) -->
                    @if(!Auth::user()->isStaff())
                    <a href="{{ route('admin.orders.index') }}"
                       class="flex items-center justify-between px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group
                              {{ request()->routeIs('admin.orders.*')
                                  ? 'bg-gradient-to-r from-red-600 to-red-700 text-white shadow-lg'
                                  : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3 transition-transform duration-200 group-hover:scale-110"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            Orders
                        </div>
                        @if($sidebarPendingOrders)
                            <span class="bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                                {{ $sidebarPendingOrders }}
                            </span>
                        @endif
                    </a>

                    <!-- Customers (Only for Admin) -->
                    @if(Auth::user()->isAdmin())
                        <a href="{{ route('admin.users.index') }}"
                           class="flex items-center justify-between px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group
                                  {{ request()->routeIs('admin.users.*')
                                      ? 'bg-gradient-to-r from-red-600 to-red-700 text-white shadow-lg'
                                      : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3 transition-transform duration-200 group-hover:scale-110"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                Customers
                            </div>

                        </a>
                    @endif
                    @endif

                    @if(Auth::user()->isAdmin())
                        <p class="px-4 pt-4 text-[11px] font-semibold uppercase tracking-[0.24em] text-gray-500">Operations</p>
                    @endif

                    <!-- Staff Management (Only for Admin) -->
                    @if(Auth::user()->isAdmin())
                    <div x-data="{ open: {{ request()->routeIs('admin.staff.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open"
                                class="w-full flex items-center justify-between px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 text-gray-300 hover:bg-gray-700/50 hover:text-white group">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3 transition-transform duration-200 group-hover:scale-110"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Staff Management
                            </div>
                            <svg :class="{'rotate-180': open}" class="w-4 h-4 transition-transform duration-200"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="open"
                             x-transition:enter="transition-all duration-200 ease-out"
                             x-transition:enter-start="opacity-0 transform -translate-y-2"
                             x-transition:enter-end="opacity-100 transform translate-y-0"
                             x-transition:leave="transition-all duration-150 ease-in"
                             x-transition:leave-start="opacity-100 transform translate-y-0"
                             x-transition:leave-end="opacity-0 transform -translate-y-2"
                             class="ml-8 mt-1 space-y-1">
                            <a href="{{ route('admin.staff.index') }}"
                               class="block px-4 py-2 text-sm rounded-lg transition-all duration-200
                                      {{ request()->routeIs('admin.staff.index')
                                          ? 'text-red-400 bg-gray-700/30'
                                          : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">
                                All Staff
                            </a>
                            <a href="{{ route('admin.staff.assign') }}"
                               class="block px-4 py-2 text-sm rounded-lg transition-all duration-200
                                      {{ request()->routeIs('admin.staff.assign')
                                          ? 'text-red-400 bg-gray-700/30'
                                          : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">
                                Assign New Staff
                            </a>
                        </div>
                    </div>
                    @endif

                    <!-- Reports Dropdown (Only for Admin) -->
                    @if(Auth::user()->isAdmin())
                    <div x-data="{ open: {{ request()->routeIs('admin.reports.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open"
                                class="w-full flex items-center justify-between px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 text-gray-300 hover:bg-gray-700/50 hover:text-white group">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3 transition-transform duration-200 group-hover:scale-110"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                                Reports
                            </div>
                            <svg :class="{'rotate-180': open}" class="w-4 h-4 transition-transform duration-200"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="open"
                             x-transition:enter="transition-all duration-200 ease-out"
                             x-transition:enter-start="opacity-0 transform -translate-y-2"
                             x-transition:enter-end="opacity-100 transform translate-y-0"
                             x-transition:leave="transition-all duration-150 ease-in"
                             x-transition:leave-start="opacity-100 transform translate-y-0"
                             x-transition:leave-end="opacity-0 transform -translate-y-2"
                             class="ml-8 mt-1 space-y-1">
                            <a href="{{ route('admin.reports.sales') }}"
                               class="block px-4 py-2 text-sm rounded-lg transition-all duration-200
                                      {{ request()->routeIs('admin.reports.sales')
                                          ? 'text-red-400 bg-gray-700/30'
                                          : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">
                                Sales Report
                            </a>
                            <a href="{{ route('admin.reports.products') }}"
                               class="block px-4 py-2 text-sm rounded-lg transition-all duration-200
                                      {{ request()->routeIs('admin.reports.products')
                                          ? 'text-red-400 bg-gray-700/30'
                                          : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">
                                Products Report
                            </a>
                        </div>
                    </div>
                    @endif

                    @if(Auth::user()->isAdmin())
                        <p class="px-4 pt-4 text-[11px] font-semibold uppercase tracking-[0.24em] text-gray-500">Marketing & System</p>
                    @endif

                    <!-- Settings (Only for Admin) -->
                    @if(Auth::user()->isAdmin())
                    <a href="{{ route('admin.settings.index') }}"
                       class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group
                              {{ request()->routeIs('admin.settings.*')
                                  ? 'bg-gradient-to-r from-red-600 to-red-700 text-white shadow-lg'
                                  : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3 transition-transform duration-200 group-hover:scale-110"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Settings
                    </a>

                    <!-- Newsletter Subscribers (Only for Admin) -->
                    <a href="{{ route('admin.newsletter-subscribers.index') }}"
                       class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group
                              {{ request()->routeIs('admin.newsletter-subscribers.*')
                                  ? 'bg-gradient-to-r from-red-600 to-red-700 text-white shadow-lg'
                                  : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3 transition-transform duration-200 group-hover:scale-110"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        Newsletter
                    </a>
                    <a href="{{ route('admin.dashboard.database-backup') }}"
                       class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group text-gray-300 hover:bg-gray-700/50 hover:text-white">
                        <svg class="w-5 h-5 mr-3 transition-transform duration-200 group-hover:scale-110"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1M7 10l5 5 5-5M12 15V3" />
                        </svg>
                        Backup Database
                    </a>
                    @endif

                    <!-- Staff Navigation (Only visible to staff members) -->
                    @if(Auth::user()->isStaff())
                        <div class="pt-4 border-t border-gray-700/50 mt-4">
                            <p class="px-4 py-2 text-xs font-semibold uppercase text-gray-400 tracking-wider">Staff Panel</p>

                            <!-- Staff Dashboard -->
                            <a href="{{ route('admin.staff.dashboard') }}"
                               class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group
                                      {{ request()->routeIs('admin.staff.dashboard')
                                          ? 'bg-gradient-to-r from-red-600 to-red-700 text-white shadow-lg'
                                          : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
                                <svg class="w-5 h-5 mr-3 transition-transform duration-200 group-hover:scale-110"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                                Staff Dashboard
                            </a>

                            <!-- Orders (if order manager) -->
                            @if(Auth::user()->canManageOrders())
                                <a href="{{ route('admin.staff.orders.index') }}"
                                   class="flex items-center justify-between px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group
                                          {{ request()->routeIs('admin.staff.orders.*')
                                              ? 'bg-gradient-to-r from-red-600 to-red-700 text-white shadow-lg'
                                              : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 mr-3 transition-transform duration-200 group-hover:scale-110"
                                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                        </svg>
                                        Orders
                                    </div>
                                    @if($sidebarPendingOrders)
                                        <span class="bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                                            {{ $sidebarPendingOrders }}
                                        </span>
                                    @endif
                                </a>
                            @endif

                            <!-- Products (if product manager) -->
                            @if(Auth::user()->canManageProducts())
                                <a href="{{ route('admin.staff.products.index') }}"
                                   class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group
                                          {{ request()->routeIs('admin.staff.products.*')
                                              ? 'bg-gradient-to-r from-red-600 to-red-700 text-white shadow-lg'
                                              : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
                                    <svg class="w-5 h-5 mr-3 transition-transform duration-200 group-hover:scale-110"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                    Products
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </nav>

            <!-- User Info -->
            <div class="flex-shrink-0 border-t border-gray-700/50 bg-black/10 p-6">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center shadow-lg">
                        <span class="text-white font-bold text-lg">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-white truncate">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-400 truncate">{{ Auth::user()->email }}</p>
                    </div>
                </div>
                <div class="mt-4 grid grid-cols-2 gap-2">
                    <a href="{{ route('admin.profile') }}" class="inline-flex items-center justify-center rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-xs font-semibold text-gray-200 transition hover:bg-white/10">
                        Profile
                    </a>
                    <a href="{{ Auth::user()->storefrontUrl() ?: route('home') }}" class="inline-flex items-center justify-center rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-xs font-semibold text-gray-200 transition hover:bg-white/10">
                        {{ Auth::user()->storefrontUrl() ? 'View Store' : 'Visit Site' }}
                    </a>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="mt-3">
                    @csrf
                    <button type="submit" class="inline-flex w-full items-center justify-center rounded-xl bg-red-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-red-700">
                        Log Out
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Top Navbar -->
            <header class="glass-effect sticky top-0 z-10 border-b border-gray-200/50">
                <div class="flex items-center justify-between px-4 sm:px-6 lg:px-8 h-16">
                    <!-- Left: Menu button -->
                    <div class="flex items-center space-x-4">
                        <button @click="sidebarOpen = !sidebarOpen"
                                class="p-2 rounded-xl text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition-all duration-200 lg:hidden">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>

                        <!-- Breadcrumb -->
                        <div class="hidden sm:block">
                            <h1 class="text-xl font-bold bg-gradient-to-r from-gray-900 to-gray-600 bg-clip-text text-transparent">
                                @yield('title', 'Dashboard')
                            </h1>
                            <nav class="flex text-sm text-gray-500">
                                <a href="{{ route(Auth::user()->dashboardRouteName()) }}" class="hover:text-red-600 transition-colors">Dashboard</a>
                                @if(View::hasSection('breadcrumb'))
                                    <span class="mx-2">/</span>
                                    @yield('breadcrumb')
                                @endif
                            </nav>
                        </div>
                    </div>

                    <!-- Right: User Menu -->
                    <div class="flex items-center space-x-3">
                        <!-- Notifications -->
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open"
                                    class="relative p-2 rounded-xl text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition-all duration-200">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                                @php
                                    $notificationUser = Auth::user();
                                    $pendingOrders = \App\Models\Order::visibleTo($notificationUser)
                                        ->where('status', 'ordered')
                                        ->count();
                                    $notificationCount = $pendingOrders;
                                @endphp
                                @if($notificationCount > 0)
                                    <span class="notification-dot absolute top-1 right-1"></span>
                                @endif
                            </button>

                            <!-- Notification Dropdown -->
                            <div x-show="open"
                                 @click.away="open = false"
                                 x-transition:enter="transition-all duration-200 ease-out"
                                 x-transition:enter-start="opacity-0 transform scale-95 translate-y-2"
                                 x-transition:enter-end="opacity-100 transform scale-100 translate-y-0"
                                 x-transition:leave="transition-all duration-150 ease-in"
                                 x-transition:leave-start="opacity-100 transform scale-100 translate-y-0"
                                 x-transition:leave-end="opacity-0 transform scale-95 translate-y-2"
                                 class="absolute right-0 mt-2 w-80 sm:w-96 rounded-2xl shadow-2xl bg-white ring-1 ring-black/5 z-50 overflow-hidden"
                                 style="display: none;">
                                <div class="p-4 border-b border-gray-100">
                                    <p class="text-sm font-semibold text-gray-900">Notifications</p>
                                </div>
                                <div class="max-h-96 overflow-y-auto">




                                    @if($pendingOrders)
                                        <a href="{{ route('admin.orders.index') }}"
                                           class="block p-4 hover:bg-gray-50 transition-colors border-b border-gray-100">
                                            <div class="flex items-start space-x-3">
                                                <div class="flex-shrink-0">
                                                    <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center">
                                                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                                        </svg>
                                                    </div>
                                                </div>
                                                <div class="flex-1">
                                                    <p class="text-sm font-medium text-gray-900">{{ $pendingOrders }} pending order{{ $pendingOrders > 1 ? 's' : '' }}</p>
                                                    <p class="text-xs text-gray-500 mt-1">Require attention</p>
                                                </div>
                                            </div>
                                        </a>
                                    @endif

                                    @if(!$notificationCount)
                                        <div class="p-8 text-center">
                                            <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                            </svg>
                                            <p class="text-sm text-gray-500">No notifications</p>
                                        </div>
                                    @endif
                                </div>
                                @if($notificationCount)
                                    <div class="p-3 bg-gray-50 border-t border-gray-100">
                                        <a href="{{ route($notificationUser->dashboardRouteName()) }}"
                                           class="block text-center text-sm text-red-600 hover:text-red-700 font-medium">
                                            View all notifications
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- User Dropdown -->
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open"
                                    class="flex items-center space-x-2 p-2 rounded-xl hover:bg-gray-100 transition-all duration-200">
                                <div class="w-8 h-8 bg-gradient-to-br from-red-500 to-red-600 rounded-lg flex items-center justify-center shadow-md">
                                    <span class="text-white font-semibold text-sm">
                                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                    </span>
                                </div>
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <div x-show="open"
                                 @click.away="open = false"
                                 x-transition:enter="transition-all duration-200 ease-out"
                                 x-transition:enter-start="opacity-0 transform scale-95 translate-y-2"
                                 x-transition:enter-end="opacity-100 transform scale-100 translate-y-0"
                                 x-transition:leave="transition-all duration-150 ease-in"
                                 x-transition:leave-start="opacity-100 transform scale-100 translate-y-0"
                                 x-transition:leave-end="opacity-0 transform scale-95 translate-y-2"
                                 class="absolute right-0 mt-2 w-56 rounded-xl shadow-2xl bg-white ring-1 ring-black/5 z-50 overflow-hidden"
                                 style="display: none;">
                                <div class="py-2">
                                    <a href="{{ route('admin.profile') }}"
                                       class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        Your Profile
                                    </a>
                                    @php
                                        $adminDropdownStoreUrl = Auth::user()->storefrontUrl() ?: route('home');
                                    @endphp
                                    <a href="{{ $adminDropdownStoreUrl }}"
                                       class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                        </svg>
                                        {{ Auth::user()->storefrontUrl() ? 'View My Store' : 'View Store' }}
                                    </a>
                                    <div class="border-t border-gray-100 my-1"></div>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit"
                                                class="w-full flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                            </svg>
                                            Log Out
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="flex-1 overflow-y-auto">
                <div class="p-4 sm:p-6 lg:p-8 fade-in-up">
                    <!-- Page Content -->
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <!-- Scripts -->
    @vite(['resources/js/app.js'])

    <!-- Livewire Scripts -->
    @livewireScripts

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    @include('partials.toast-stack')

    <!-- Custom Scripts -->
    @stack('scripts')

</body>
</html>
