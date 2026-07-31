<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $siteName = \App\Helpers\SettingsHelper::get('site_name', config('app.name', 'Laravel'));
    @endphp

    <title>@yield('title', 'Login') - {{ $siteName }}</title>
    <meta name="theme-color" content="#dc2626">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="{{ $siteName }}">
    <link rel="manifest" href="{{ route('pwa.manifest') }}">
    <link rel="apple-touch-icon" href="{{ route('pwa.icon', ['size' => 192]) }}">
    <link rel="icon" type="image/png" href="{{ route('pwa.icon', ['size' => 192]) }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Reset & Base */
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
            background:
                radial-gradient(circle at top left, rgba(220, 38, 38, 0.08), transparent 30%),
                radial-gradient(circle at bottom right, rgba(15, 23, 42, 0.06), transparent 35%),
                linear-gradient(135deg, #f8fafc 0%, #f3f4f6 100%);
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
        
        /* Button Styles - Modern */
        .btn-primary {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 0.75rem;
            font-weight: 600;
            font-size: 0.875rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
            cursor: pointer;
            width: 100%;
            position: relative;
            overflow: hidden;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -10px rgba(220, 38, 38, 0.5);
        }
        
        .btn-primary:active {
            transform: translateY(0);
        }
        
        .btn-primary:focus {
            outline: none;
            ring: 2px solid #dc2626;
            ring-offset: 2px;
        }
        
        .btn-primary:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }
        
        /* Form Styles */
        .form-group {
            margin-bottom: 1.25rem;
        }
        
        .form-label {
            display: block;
            color: #374151;
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .form-label .required {
            color: #dc2626;
            margin-left: 0.25rem;
        }
        
        .form-input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 0.75rem;
            font-size: 0.875rem;
            transition: all 0.2s ease;
            background-color: white;
        }
        
        .form-input:focus {
            outline: none;
            border-color: #dc2626;
            box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
        }
        
        .form-input-error {
            border-color: #dc2626;
            background-color: #fef2f2;
        }
        
        .form-input-error:focus {
            border-color: #dc2626;
            box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
        }
        
        .error-message {
            color: #dc2626;
            font-size: 0.75rem;
            margin-top: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }
        
        .error-message::before {
            content: "⚠";
            font-size: 0.75rem;
        }
        
        /* Checkbox Styles */
        .checkbox-wrapper {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
        }
        
        .form-checkbox {
            width: 1rem;
            height: 1rem;
            border: 2px solid #d1d5db;
            border-radius: 0.25rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .form-checkbox:checked {
            background-color: #dc2626;
            border-color: #dc2626;
        }
        
        .form-checkbox:focus {
            outline: none;
            ring: 2px solid #dc2626;
        }
        
        /* Auth Card Styles */
        .auth-card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(14px);
            border-radius: 1.75rem;
            box-shadow: 0 28px 60px -32px rgba(15, 23, 42, 0.28),
                        0 12px 24px -18px rgba(220, 38, 38, 0.2);
            padding: 1.25rem;
            width: 100%;
            max-width: 34rem;
            margin: 0 auto;
            border: 1px solid rgba(255, 255, 255, 0.55);
        }
        
        @media (min-width: 640px) {
            .auth-card {
                padding: 1.75rem;
            }
        }
        
        /* Logo Styles */
        .auth-logo {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 800;
            font-size: 1.875rem;
            line-height: 2.25rem;
        }
        
        /* Link Styles */
        .auth-link {
            color: #dc2626;
            font-size: 0.875rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s ease;
            position: relative;
        }
        
        .auth-link:hover {
            color: #b91c1c;
        }
        
        .auth-link::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            transition: width 0.3s ease;
        }
        
        .auth-link:hover::after {
            width: 100%;
        }
        
        /* Flash Message Styles */
        .flash-message {
            position: fixed;
            top: 1rem;
            right: 1rem;
            z-index: 50;
            max-width: 24rem;
            width: 100%;
            animation: slideInRight 0.3s ease-out;
        }
        
        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        @keyframes slideOutRight {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }
        
        .flash-message.hide {
            animation: slideOutRight 0.3s ease-out forwards;
        }
        
        /* Loading Spinner */
        .btn-loading {
            position: relative;
            color: transparent !important;
        }
        
        .btn-loading::after {
            content: '';
            position: absolute;
            width: 1rem;
            height: 1rem;
            top: 50%;
            left: 50%;
            margin-left: -0.5rem;
            margin-top: -0.5rem;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
        }
        
        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
        
        /* Social Login Buttons */
        .social-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e5e7eb;
            border-radius: 0.75rem;
            background: white;
            color: #374151;
            font-weight: 500;
            font-size: 0.875rem;
            transition: all 0.2s ease;
            cursor: pointer;
        }
        
        .social-btn:hover {
            border-color: #dc2626;
            background-color: #fef2f2;
            transform: translateY(-1px);
        }
        
        /* Header Styles */
        .auth-header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(229, 231, 235, 0.5);
            position: sticky;
            top: 0;
            z-index: 40;
        }
        
        /* Divider */
        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 1.5rem 0;
        }
        
        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .divider span {
            padding: 0 1rem;
            color: #6b7280;
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        /* Responsive Adjustments */
        @media (max-width: 640px) {
            .auth-card {
                margin: 0;
                padding: 1rem;
                border-radius: 1.5rem;
            }
            
            .btn-primary {
                padding: 0.625rem 1.25rem;
            }
        }
        
        /* Animation for card */
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
        
        .auth-card {
            animation: fadeInUp 0.5s ease-out;
        }
        
        /* Input group with icon */
        .input-group {
            position: relative;
        }
        
        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            pointer-events: none;
        }
        
        .input-icon + input {
            padding-left: 2.5rem;
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-gray-50 via-gray-100 to-gray-50 font-sans antialiased">
    <div class="min-h-screen flex flex-col">
        <!-- Modern Header -->
        <header class="auth-header">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex h-14 items-center justify-between sm:h-16">
                    <!-- Logo -->
                    <div class="flex items-center">
                        <a href="{{ route('home') }}" class="flex items-center space-x-2 group">
                            @if(file_exists(public_path('storage/logo.png')))
                                <img src="{{ asset('storage/logo.png') }}" 
                                     alt="{{ \App\Helpers\SettingsHelper::get('site_name', config('app.name', 'E-Commerce')) }}" 
                                     class="h-7 w-auto transition-transform group-hover:scale-105 sm:h-8">
                            @else
                                <span class="text-base font-bold bg-gradient-to-r from-red-600 to-red-700 bg-clip-text text-transparent sm:text-xl">
                                    {{ \App\Helpers\SettingsHelper::get('site_name', config('app.name', 'E-Commerce')) }}
                                </span>
                            @endif
                        </a>
                    </div>

                    <!-- Back to home with icon -->
                    <a href="{{ route('home') }}" 
                       class="inline-flex items-center gap-2 text-xs font-medium text-gray-600 transition-colors duration-200 group hover:text-red-600 sm:text-sm">
                        <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        <span class="hidden sm:inline">Back to Home</span>
                        <span class="sm:hidden">Home</span>
                    </a>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex flex-1 items-start justify-center p-3 pt-4 sm:items-center sm:p-6 lg:p-8">
            <!-- Modern Auth Card -->
            <div class="auth-card">
                <!-- Logo & Header -->
                <div class="mb-6 text-center sm:mb-8">
                    <a href="{{ route('home') }}" class="inline-block group">
                        <img src="{{ asset('images/keffi-cart-logo.svg') }}"
                             alt="{{ \App\Helpers\SettingsHelper::get('site_name', config('app.name', 'E-Commerce')) }}"
                             class="mx-auto h-12 w-12 transition-transform group-hover:scale-105 sm:h-16 sm:w-16">
                    </a>
                    <h1 class="mt-4 bg-gradient-to-r from-gray-900 to-gray-600 bg-clip-text text-xl font-bold text-transparent sm:mt-6 sm:text-2xl">
                        @yield('auth-title', 'Welcome Back')
                    </h1>
                    <p class="mt-2 text-xs text-gray-500 sm:text-sm">
                        @yield('auth-subtitle', 'Sign in to your account')
                    </p>
                </div>

                <!-- Main Content -->
                {{ $slot }}

                <!-- Footer Links -->
                <div class="mt-6 border-t border-gray-200 pt-5 text-center sm:mt-8 sm:pt-6">
                    @hasSection('auth-footer')
                        @yield('auth-footer')
                    @else
                        <p class="text-[11px] text-gray-500 sm:text-xs">
                            &copy; {{ date('Y') }} {{ \App\Helpers\SettingsHelper::get('site_name', config('app.name', 'E-Commerce')) }}. 
                            All rights reserved.
                        </p>
                        <div class="mt-3 flex items-center justify-center gap-3 sm:gap-4">
                            <a href="{{ route('privacy') }}" class="text-[11px] text-gray-500 transition-colors hover:text-red-600 sm:text-xs">
                                Privacy Policy
                            </a>
                            <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                            <a href="{{ route('terms') }}" class="text-[11px] text-gray-500 transition-colors hover:text-red-600 sm:text-xs">
                                Terms of Service
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </main>
    </div>

    @include('partials.app-launch-promo')
    @include('partials.pwa-install-prompt')
    @include('partials.toast-stack')

    <!-- Custom Scripts -->
    @stack('scripts')
    
    <script>
        // Prevent duplicate form submissions
        document.addEventListener('submit', function(e) {
            const form = e.target;
            if (form.classList.contains('js-prevent-double-submit')) {
                const submitButton = form.querySelector('button[type="submit"]');
                if (submitButton && !submitButton.disabled) {
                    submitButton.disabled = true;
                    submitButton.classList.add('btn-loading');
                }
            }
        });
        
        // Livewire event handling
        if (typeof Livewire !== 'undefined') {
            document.addEventListener('livewire:initialized', () => {
                Livewire.on('redirect', ({ url }) => {
                    window.location.href = url;
                });
            });
        }
        
        // Add floating label effect
        document.querySelectorAll('.form-input').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });
            
            input.addEventListener('blur', function() {
                if (!this.value) {
                    this.parentElement.classList.remove('focused');
                }
            });
        });
        
        // Password visibility toggle (optional)
        window.togglePasswordVisibility = function(inputId) {
            const input = document.getElementById(inputId);
            const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
            input.setAttribute('type', type);
        };
    </script>
</body>
</html>
