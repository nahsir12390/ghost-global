@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-12 px-4">
    <div class="max-w-2xl mx-auto">
        <!-- Error Message -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
            <div class="bg-red-600 text-white p-8 text-center">
                <div class="mb-4">
                    <svg class="w-24 h-24 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4v2m0 4v2M6.172 6.172a4 4 0 015.656 0L12 7.07m0 0l.172-.172a4 4 0 115.656 5.656L12 18.03m0 0l-.172.172a4 4 0 11-5.656-5.656L12 7.07M12 3v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                </div>
                <h1 class="text-5xl font-bold mb-2">500</h1>
                <p class="text-xl text-red-100">Internal Server Error</p>
            </div>
            
            <!-- Error Details -->
            <div class="p-8">
                <!-- Main Message -->
                <div class="bg-red-50 border-l-4 border-red-600 rounded-lg p-6 mb-8">
                    <h2 class="text-2xl font-bold text-red-900 mb-3">Oops! Something went wrong</h2>
                    <p class="text-red-800 text-lg">
                        We encountered an unexpected error while processing your request. Our team has been notified and is working to fix the issue.
                    </p>
                </div>

                <!-- What Happened -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-8">
                    <h3 class="font-semibold text-yellow-900 mb-3 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        What Happened?
                    </h3>
                    <ul class="text-sm text-yellow-800 space-y-2">
                        <li>• A server error occurred while processing your request</li>
                        <li>• This could be due to a temporary system issue</li>
                        <li>• The error has been logged and reported to our team</li>
                        <li>• We're working to resolve this as quickly as possible</li>
                    </ul>
                </div>

                <!-- What to Do -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8">
                    <h3 class="font-semibold text-blue-900 mb-3 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zm-11-1a1 1 0 11-2 0 1 1 0 012 0zm3 0a1 1 0 11-2 0 1 1 0 012 0zm3 0a1 1 0 11-2 0 1 1 0 012 0z" clip-rule="evenodd"/>
                        </svg>
                        What Can You Do?
                    </h3>
                    <ul class="text-sm text-blue-800 space-y-2">
                        <li>✓ Try refreshing the page after a few moments</li>
                        <li>✓ Clear your browser cache and cookies</li>
                        <li>✓ Try accessing the site from a different browser</li>
                        <li>✓ Return to the home page and try again</li>
                        <li>✓ Contact our support team if the issue persists</li>
                    </ul>
                </div>

                <!-- Error Reference -->
                <div class="bg-gray-100 border border-gray-300 rounded-lg p-6 mb-8">
                    <h3 class="font-semibold text-gray-900 mb-2">Error Reference</h3>
                    <p class="text-sm text-gray-600">
                        Error ID: <span class="font-mono bg-gray-200 px-2 py-1 rounded">{{ env('APP_ENV') }}-{{ now()->timestamp }}</span>
                    </p>
                    <p class="text-xs text-gray-500 mt-2">
                        This error has been recorded and logged. Our team will review it shortly.
                    </p>
                </div>

                <!-- Action Buttons -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <a href="javascript:location.reload()" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg text-center transition flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Refresh Page
                    </a>
                    <a href="{{ auth()->check() ? route(auth()->user()->dashboardRouteName()) : route('home') }}" class="w-full bg-gray-600 hover:bg-gray-700 text-white font-bold py-3 px-4 rounded-lg text-center transition flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-3m0 0l7-4 7 4M5 9v10a1 1 0 001 1h12a1 1 0 001-1V9m-9 11l4-4m0 0l4 4m-4-4v4"/>
                        </svg>
                        Go to Dashboard
                    </a>
                </div>

                <!-- Support Contact -->
                <div class="mt-8 p-6 bg-gray-50 rounded-lg border border-gray-200 text-center">
                    <h3 class="font-semibold text-gray-900 mb-2 flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                        </svg>
                        Need Help?
                    </h3>
                    <p class="text-gray-600 mb-3">Contact our support team and we'll help you resolve this issue</p>
                    <a href="mailto:support@example.com" class="inline-block text-blue-600 hover:text-blue-700 font-semibold">
                        support@example.com
                    </a>
                </div>
            </div>
        </div>

        <!-- Additional Information -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <div class="text-3xl mb-2">⏱️</div>
                <h4 class="font-semibold text-gray-900 mb-1">Temporary Issue</h4>
                <p class="text-sm text-gray-600">This is usually a temporary problem that will be resolved shortly.</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <div class="text-3xl mb-2">🔧</div>
                <h4 class="font-semibold text-gray-900 mb-1">We're Investigating</h4>
                <p class="text-sm text-gray-600">Our team has been notified and is actively working on a fix.</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <div class="text-3xl mb-2">✅</div>
                <h4 class="font-semibold text-gray-900 mb-1">We'll Fix It</h4>
                <p class="text-sm text-gray-600">We'll update our status once the issue has been resolved.</p>
            </div>
        </div>
    </div>
</div>
@endsection
