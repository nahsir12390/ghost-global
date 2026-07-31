@extends('layouts.app')

@section('title', 'Track Your Order')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-50">
    <!-- Modern Header -->
    <div class="relative overflow-hidden bg-gradient-to-r from-red-600 to-red-700 text-white py-16">
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute -top-40 -right-40 w-80 h-80 bg-red-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse"></div>
            <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-red-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse" style="animation-delay: 2s;"></div>
        </div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center">
                <h1 class="text-5xl font-bold mb-4">📦 Track Your Order</h1>
                <p class="text-red-100 text-lg max-w-2xl mx-auto">Enter your order number and email to get real-time updates on your delivery</p>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Error Alert -->
        @if($errors->any() || session('error'))
            <div class="mb-8 bg-red-50 border border-red-200 rounded-xl p-4 shadow-md">
                <div class="flex items-start">
                    <svg class="h-5 w-5 text-red-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="ml-3 flex-1">
                        @if(session('error'))
                            <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                        @else
                            @foreach($errors->all() as $error)
                                <p class="text-sm font-medium text-red-800">{{ $error }}</p>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- Search Card -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden mb-12 border border-gray-100">
            <div class="px-6 py-10 sm:px-10 sm:py-12 bg-gradient-to-br from-white to-gray-50">
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Find Your Order</h2>
                <p class="text-gray-600 mb-8">Quick and easy order tracking</p>

                <form action="{{ route('tracking.search') }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- Order Number Field -->
                    <div>
                        <label for="order_number" class="block text-sm font-semibold text-gray-900 mb-2">
                            Order Number
                        </label>
                        <input
                            type="text"
                            id="order_number"
                            name="order_number"
                            placeholder="e.g., ORD-ABC123XYZ"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all @error('order_number') border-red-500 @enderror"
                            value="{{ old('order_number') }}"
                            required
                        >
                        @error('order_number')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-sm text-gray-500">📧 Find it in your confirmation email</p>
                    </div>

                    <!-- Email or Phone Field -->
                    <div>
                        <label for="email_or_phone" class="block text-sm font-semibold text-gray-900 mb-2">
                            Email Address or Phone Number
                        </label>
                        <input
                            type="text"
                            id="email_or_phone"
                            name="email_or_phone"
                            placeholder="your@email.com or +234 800 000 0000"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all @error('email_or_phone') border-red-500 @enderror"
                            value="{{ old('email_or_phone') }}"
                            required
                        >
                        @error('email_or_phone')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-sm text-gray-500">Use the email or phone from your order</p>
                    </div>

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        class="w-full bg-gradient-to-r from-red-600 to-red-700 text-white py-3 px-6 rounded-lg font-semibold hover:shadow-lg transition-all duration-300 transform hover:scale-105 flex items-center justify-center space-x-2"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <span>Track Order</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Info Section -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
            <!-- Card 1 -->
            <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 p-6 border border-gray-100 hover:-translate-y-1">
                <div class="flex items-center mb-4">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center h-12 w-12 rounded-lg bg-red-100 text-red-600">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                    </div>
                    <h3 class="ml-3 text-lg font-semibold text-gray-900">Real-Time Tracking</h3>
                </div>
                <p class="text-gray-600 text-sm">Get instant updates as your order moves through every stage of our system.</p>
            </div>

            <!-- Card 2 -->
            <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 p-6 border border-gray-100 hover:-translate-y-1">
                <div class="flex items-center mb-4">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center h-12 w-12 rounded-lg bg-red-100 text-red-600">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                        </div>
                    </div>
                    <h3 class="ml-3 text-lg font-semibold text-gray-900">Notifications</h3>
                </div>
                <p class="text-gray-600 text-sm">Get email alerts whenever your order status changes or item ships.</p>
            </div>

            <!-- Card 3 -->
            <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 p-6 border border-gray-100 hover:-translate-y-1">
                <div class="flex items-center mb-4">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center h-12 w-12 rounded-lg bg-red-100 text-red-600">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <h3 class="ml-3 text-lg font-semibold text-gray-900">Secure & Private</h3>
                </div>
                <p class="text-gray-600 text-sm">Your information is encrypted and protected with industry-standard security.</p>
            </div>
        </div>

        <!-- FAQ Section -->
        <div class="bg-white rounded-2xl shadow-lg p-8 mb-8 border border-gray-100">
            <h3 class="text-2xl font-bold text-gray-900 mb-8">Frequently Asked Questions</h3>
            <div class="space-y-4">
                <details class="group cursor-pointer">
                    <summary class="flex items-center justify-between font-semibold text-gray-900 p-4 rounded-lg hover:bg-red-50 transition-colors">
                        <span class="flex items-center space-x-3">
                            <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2z" clip-rule="evenodd"/>
                            </svg>
                            <span>Where can I find my order number?</span>
                        </span>
                        <svg class="w-5 h-5 transform group-open:rotate-180 transition-transform text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                        </svg>
                    </summary>
                    <p class="text-gray-600 p-4 pl-12 bg-gray-50">Your order number starts with "ORD-" and was sent to you in your order confirmation email. You can also find it on your order receipt.</p>
                </details>

                <details class="group cursor-pointer">
                    <summary class="flex items-center justify-between font-semibold text-gray-900 p-4 rounded-lg hover:bg-red-50 transition-colors">
                        <span class="flex items-center space-x-3">
                            <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2z" clip-rule="evenodd"/>
                            </svg>
                            <span>What information do I need to track?</span>
                        </span>
                        <svg class="w-5 h-5 transform group-open:rotate-180 transition-transform text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                        </svg>
                    </summary>
                    <p class="text-gray-600 p-4 pl-12 bg-gray-50">You'll need your order number (e.g., ORD-ABC123XYZ) and either the email address or phone number associated with your order.</p>
                </details>

                <details class="group cursor-pointer">
                    <summary class="flex items-center justify-between font-semibold text-gray-900 p-4 rounded-lg hover:bg-red-50 transition-colors">
                        <span class="flex items-center space-x-3">
                            <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2z" clip-rule="evenodd"/>
                            </svg>
                            <span>How often is tracking information updated?</span>
                        </span>
                        <svg class="w-5 h-5 transform group-open:rotate-180 transition-transform text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                        </svg>
                    </summary>
                    <p class="text-gray-600 p-4 pl-12 bg-gray-50">Status updates are provided in real-time. You'll also receive email notifications each time your order status changes.</p>
                </details>

                <details class="group cursor-pointer">
                    <summary class="flex items-center justify-between font-semibold text-gray-900 p-4 rounded-lg hover:bg-red-50 transition-colors">
                        <span class="flex items-center space-x-3">
                            <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2z" clip-rule="evenodd"/>
                            </svg>
                            <span>What do the order statuses mean?</span>
                        </span>
                        <svg class="w-5 h-5 transform group-open:rotate-180 transition-transform text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                        </svg>
                    </summary>
                    <div class="text-gray-600 p-4 pl-12 bg-gray-50 space-y-2">
                        <div><strong class="text-gray-900">🔄 Processing:</strong> Your order has been received and is being prepared for shipment</div>
                        <div><strong class="text-gray-900">📦 Packed:</strong> Your items have been packed and are ready to be shipped</div>
                        <div><strong class="text-gray-900">🚚 Shipped:</strong> Your package has been picked up by the shipping carrier</div>
                        <div><strong class="text-gray-900">🛣️ On The Way:</strong> Your delivery is in transit and on its way to you</div>
                        <div><strong class="text-gray-900">✅ Delivered:</strong> Your order has been successfully delivered</div>
                        <div><strong class="text-gray-900">❌ Cancelled:</strong> Your order has been cancelled</div>
                    </div>
                </details>
            </div>
        </div>

        <!-- Support Section -->
        <div class="bg-gradient-to-r from-red-600 to-red-700 rounded-2xl p-10 text-center text-white shadow-lg">
            <h3 class="text-2xl font-bold mb-3">Need Immediate Help?</h3>
            <p class="mb-6 text-red-100">Can't find your order or need rapid assistance?</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('contact') }}" class="inline-flex items-center px-6 py-3 bg-white text-red-600 font-bold rounded-lg hover:bg-red-50 transition-all shadow-md">
                    📝 Contact Support
                </a>
                <a href="{{ route('cart') }}" class="inline-flex items-center px-6 py-3 border-2 border-white text-white font-bold rounded-lg hover:bg-white/10 transition-all">
                    🛍️ Continue Shopping
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
