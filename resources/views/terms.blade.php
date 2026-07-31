@extends('layouts.app')

@section('title', 'Terms of Service')
@section('content')
<div class="max-w-4xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
    <div class="bg-white shadow-sm rounded-lg p-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Terms of Service</h1>
        
        <div class="prose prose-gray max-w-none">
            <p class="text-gray-600 mb-6">Last updated: January 2, 2026</p>
            
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">Acceptance of Terms</h2>
            <p class="text-gray-700 mb-6">
                By accessing and using our e-commerce platform, you accept and agree to be bound by the terms and 
                provision of this agreement.
            </p>
            
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">Use License</h2>
            <p class="text-gray-700 mb-6">
                Permission is granted to temporarily use our platform for personal, non-commercial transitory viewing only. 
                This is the grant of a license, not a transfer of title, and under this license you may not:
            </p>
            <ul class="list-disc list-inside text-gray-700 mb-6 ml-4">
                <li>modify or copy the materials</li>
                <li>use the materials for any commercial purpose or for any public display</li>
                <li>attempt to decompile or reverse engineer any software contained on our platform</li>
                <li>remove any copyright or other proprietary notations from the materials</li>
            </ul>
            
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">Products and Pricing</h2>
            <p class="text-gray-700 mb-6">
                All products are subject to availability. Prices are subject to change without notice. 
                We reserve the right to refuse or cancel any order for any reason.
            </p>
            
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">Shipping and Returns</h2>
            <p class="text-gray-700 mb-6">
                Shipping costs and delivery times vary by location and product. Returns are accepted within 30 days 
                of purchase with original packaging and proof of purchase.
            </p>
            
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">Contact Information</h2>
            <p class="text-gray-700">
                Questions about the Terms of Service should be sent to us at legal@example.com.
            </p>
        </div>
    </div>
</div>
@endsection