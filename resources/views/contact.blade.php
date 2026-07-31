@extends('layouts.app')

@section('title', 'Contact Us')

@section('content')
<div class="bg-gradient-to-br from-gray-50 to-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="text-center mb-16">
            <div class="inline-block mb-4">
                <span class="inline-block px-4 py-1 bg-red-100 text-red-700 rounded-full text-sm font-semibold">Get In Touch</span>
            </div>
            <h1 class="text-5xl font-bold text-gray-900 mb-4">Contact Our Support Team</h1>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">Have a question? We're here to help. Reach out to us through any of these channels.</p>
        </div>

        <!-- Contact Info Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-16">
            <!-- Email Card -->
            @php
                $siteEmail = \App\Helpers\SettingsHelper::get('site_email', config('mail.from.address'));
            @endphp
            <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition-shadow duration-300 p-8 border border-gray-100">
                <div class="flex items-center justify-center mb-4">
                    <div class="flex items-center justify-center w-14 h-14 bg-red-100 rounded-lg">
                        <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2 text-center">Email</h3>
                <p class="text-gray-600 text-center mb-3">Send us an email anytime</p>
                <a href="mailto:{{ $siteEmail }}" class="text-red-600 font-semibold text-center block hover:text-red-700 transition-colors">{{ $siteEmail }}</a>
            </div>

            <!-- Phone Card -->
            @php
                $sitePhone = \App\Helpers\SettingsHelper::get('site_phone', '+234 909 123 456');
            @endphp
            <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition-shadow duration-300 p-8 border border-gray-100">
                <div class="flex items-center justify-center mb-4">
                    <div class="flex items-center justify-center w-14 h-14 bg-red-100 rounded-lg">
                        <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 00.948.684l1.498 4.493a1 1 0 00.502.756l2.73 1.365a1 1 0 001.27-1.27l-1.365-2.73a1 1 0 00.756-.502l4.493-1.498a1 1 0 00.684-.949V5a2 2 0 00-2-2h-2.5a2 2 0 00-2 2v2m0 0H9m0 0a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2h14a2 2 0 002-2v-5"></path>
                        </svg>
                    </div>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2 text-center">Phone</h3>
                <p class="text-gray-600 text-center mb-3">Call us Mon-Fri 9am-5pm</p>
                <a href="tel:{{ preg_replace('/[^0-9+]/', '', $sitePhone) }}" class="text-red-600 font-semibold text-center block hover:text-red-700 transition-colors">{{ $sitePhone }}</a>
            </div>

            <!-- Location Card -->
            @php
                $siteAddress = \App\Helpers\SettingsHelper::get('site_address', 'Lagos, Nigeria');
            @endphp
            <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition-shadow duration-300 p-8 border border-gray-100">
                <div class="flex items-center justify-center mb-4">
                    <div class="flex items-center justify-center w-14 h-14 bg-red-100 rounded-lg">
                        <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2 text-center">Location</h3>
                <p class="text-gray-600 text-center mb-3">Visit our office</p>
                <p class="text-red-600 font-semibold text-center">{{ $siteAddress }}</p>
            </div>
        </div>

        <!-- Main Content Section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Contact Form -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-lg p-8 border border-gray-100">
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">Send us a Message</h2>
                    <p class="text-gray-600 mb-8">Fill out the form below and we'll get back to you as soon as possible.</p>

                    <form action="{{ route('contact.submit') }}" method="POST" class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Name -->
                            <div>
                                <label for="name" class="block text-sm font-semibold text-gray-900 mb-2">Full Name</label>
                                <input
                                    type="text"
                                    id="name"
                                    name="name"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all @error('name') border-red-500 @enderror"
                                    placeholder="John Doe"
                                    required
                                >
                                @error('name')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-semibold text-gray-900 mb-2">Email Address</label>
                                <input
                                    type="email"
                                    id="email"
                                    name="email"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all @error('email') border-red-500 @enderror"
                                    placeholder="john@example.com"
                                    required
                                >
                                @error('email')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Subject -->
                        <div>
                            <label for="subject" class="block text-sm font-semibold text-gray-900 mb-2">Subject</label>
                            <input
                                type="text"
                                id="subject"
                                name="subject"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all @error('subject') border-red-500 @enderror"
                                placeholder="How can we help?"
                                required
                            >
                            @error('subject')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Message -->
                        <div>
                            <label for="message" class="block text-sm font-semibold text-gray-900 mb-2">Message</label>
                            <textarea
                                id="message"
                                name="message"
                                rows="5"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all resize-none @error('message') border-red-500 @enderror"
                                placeholder="Tell us more about your inquiry..."
                                required
                            ></textarea>
                            @error('message')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <button
                            type="submit"
                            class="btn-primary w-full"
                        >
                            Send Message
                        </button>
                    </form>
                </div>
            </div>

            <!-- FAQ Section -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-lg p-8 border border-gray-100">
                    <h3 class="text-2xl font-bold text-gray-900 mb-6">Quick Help</h3>
                    <div class="space-y-4">
                        <details class="group cursor-pointer">
                            <summary class="flex items-center gap-3 font-semibold text-gray-900 hover:text-red-600 transition-colors">
                                <span class="flex-shrink-0 w-6 h-6 bg-red-100 rounded-full flex items-center justify-center group-open:bg-red-600 group-open:text-white transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                </span>
                                Response Time?
                            </summary>
                            <p class="text-gray-600 text-sm mt-3 ml-9">We respond within 24 hours during business days.</p>
                        </details>

                        <details class="group cursor-pointer">
                            <summary class="flex items-center gap-3 font-semibold text-gray-900 hover:text-red-600 transition-colors">
                                <span class="flex-shrink-0 w-6 h-6 bg-red-100 rounded-full flex items-center justify-center group-open:bg-red-600 group-open:text-white transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                </span>
                                Weekend Support?
                            </summary>
                            <p class="text-gray-600 text-sm mt-3 ml-9">Yes! Our team works 7 days a week for your convenience.</p>
                        </details>

                        <details class="group cursor-pointer">
                            <summary class="flex items-center gap-3 font-semibold text-gray-900 hover:text-red-600 transition-colors">
                                <span class="flex-shrink-0 w-6 h-6 bg-red-100 rounded-full flex items-center justify-center group-open:bg-red-600 group-open:text-white transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                </span>
                                Track My Order?
                            </summary>
                            <p class="text-gray-600 text-sm mt-3 ml-9">Visit our <a href="{{ route('tracking.index') }}" class="text-red-600 font-semibold hover:underline">tracking page</a> to check order status.</p>
                        </details>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom CTA Section -->
        @php
            $siteName = \App\Helpers\SettingsHelper::get('site_name', config('app.name', 'E-Commerce'));
            $sitePhone = \App\Helpers\SettingsHelper::get('site_phone', '+234 909 123 456');
            $phoneForCall = \App\Helpers\SettingsHelper::formatWhatsAppNumber($sitePhone);
            $phoneForWhatsApp = \App\Helpers\SettingsHelper::supportWhatsAppNumber();
        @endphp
        <div class="mt-16 bg-gradient-to-r from-red-600 to-red-700 rounded-xl px-8 py-12 text-center text-white shadow-lg">
            <h2 class="text-3xl font-bold mb-4">Ready to Get Help?</h2>
            <p class="text-red-100 mb-6 max-w-xl mx-auto">If you prefer immediate assistance, reach out to us on WhatsApp or call us directly. We're available round the clock!</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <button onclick="window.open('https://wa.me/{{ $phoneForWhatsApp }}?text=Hi%21%20I%20need%20help%20with%20{{ urlencode($siteName) }}', '_blank')" 
                        class="inline-block bg-white text-red-600 font-bold px-8 py-3 rounded-lg hover:bg-red-50 transition-colors">
                    💬 Chat on WhatsApp
                </button>
                <a href="tel:{{ $phoneForCall }}" class="inline-block bg-red-500 text-white font-bold px-8 py-3 rounded-lg hover:bg-red-800 transition-colors border-2 border-white">
                    📞 Call Us Now
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
