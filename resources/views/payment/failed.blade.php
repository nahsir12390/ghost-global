@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-12 px-4">
    <div class="max-w-2xl mx-auto">
        <!-- Error Message -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
            <div class="bg-red-500 text-white p-8 text-center">
                <svg class="w-20 h-20 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                <h1 class="text-4xl font-bold mb-2">Payment Failed</h1>
                <p class="text-red-100">Unfortunately, we couldn't process your payment. Please try again.</p>
            </div>
            
            <!-- Error Details -->
            <div class="p-8">
                @if($message ?? null)
                <div class="bg-red-50 border border-red-200 rounded-lg p-6 mb-8">
                    <h3 class="font-semibold text-red-900 mb-2">Error Details</h3>
                    <p class="text-red-800">{{ $message }}</p>
                </div>
                @endif

                <!-- What Went Wrong -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-8">
                    <h3 class="font-semibold text-yellow-900 mb-3">Why Did This Happen?</h3>
                    <ul class="text-sm text-yellow-800 space-y-2">
                        <li>• Insufficient funds on your card/account</li>
                        <li>• Card declined by your bank</li>
                        <li>• Incorrect payment details</li>
                        <li>• Transaction timeout</li>
                        <li>• You cancelled the payment</li>
                    </ul>
                </div>

                <!-- What to Do -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8">
                    <h3 class="font-semibold text-blue-900 mb-3">What Can You Do?</h3>
                    <ul class="text-sm text-blue-800 space-y-2">
                        <li>✓ Check that your card details are correct</li>
                        <li>✓ Ensure you have sufficient funds</li>
                        <li>✓ Try a different payment method</li>
                        <li>✓ Contact your bank if the issue persists</li>
                        <li>✓ Try again in a few moments</li>
                    </ul>
                </div>

                <!-- Action Buttons -->
                <div class="space-y-4">
                    <a href="{{ route('checkout') }}" class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg text-center transition">
                        Try Again
                    </a>
                    <a href="{{ route('shop') }}" class="block w-full bg-gray-200 hover:bg-gray-300 text-gray-900 font-bold py-3 px-4 rounded-lg text-center transition">
                        Continue Shopping
                    </a>
                </div>

                <!-- Support Contact -->
                <div class="mt-8 p-6 bg-gray-50 rounded-lg text-center">
                    <p class="text-gray-600 mb-2">Still having trouble?</p>
                    <p class="text-gray-900 font-semibold">Contact our support team</p>
                    <p class="text-blue-600 hover:text-blue-700">
                        <a href="mailto:support@example.com">support@example.com</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
