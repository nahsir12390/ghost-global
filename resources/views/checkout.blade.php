@extends('layouts.app')

@section('title', 'Checkout - ' . config('app.name'))

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Checkout</h1>
            <p class="text-gray-600">Complete your order in just a few steps</p>
        </div>
        
        <!-- Checkout Content -->
        <div>
            @livewire('checkout')
        </div>
    </div>
</div>

<!-- JavaScript for checkout -->
@push('scripts')
<script>
    // Handle form validation and submission
    document.addEventListener('livewire:load', function() {
        // Auto-format phone number
        const phoneInput = document.getElementById('shipping_phone');
        if (phoneInput) {
            phoneInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length > 0) {
                    if (value.length <= 3) {
                        value = value;
                    } else if (value.length <= 6) {
                        value = value.slice(0, 3) + '-' + value.slice(3);
                    } else {
                        value = value.slice(0, 3) + '-' + value.slice(3, 6) + '-' + value.slice(6, 10);
                    }
                }
                e.target.value = value;
            });
        }
        
        // Auto-format postal code
        const postalInput = document.getElementById('shipping_postal_code');
        if (postalInput) {
            postalInput.addEventListener('input', function(e) {
                e.target.value = e.target.value.toUpperCase();
            });
        }
        
        // Prevent form submission on Enter key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && e.target.type !== 'textarea') {
                e.preventDefault();
            }
        });
    });
    
    // Address auto-complete (if you want to integrate Google Maps API)
    function initAddressAutocomplete() {
        // This is a placeholder for Google Maps Address Autocomplete
        // You'll need to add your Google Maps API key
        if (typeof google !== 'undefined') {
            const shippingAddress = document.getElementById('shipping_address');
            if (shippingAddress) {
                const autocomplete = new google.maps.places.Autocomplete(shippingAddress, {
                    types: ['address'],
                    componentRestrictions: { country: 'ng' } // Restrict to Nigeria
                });
                
                autocomplete.addListener('place_changed', function() {
                    const place = autocomplete.getPlace();
                    if (place.address_components) {
                        const addressComponents = {};
                        
                        for (const component of place.address_components) {
                            const type = component.types[0];
                            addressComponents[type] = component.long_name;
                        }
                        
                        // Auto-fill other fields
                        Livewire.emit('updateAddress', {
                            city: addressComponents.locality || '',
                            state: addressComponents.administrative_area_level_1 || '',
                            country: addressComponents.country || 'Nigeria',
                            postal_code: addressComponents.postal_code || ''
                        });
                    }
                });
            }
        }
    }
    
    // Load Google Maps API if needed
    @if(config('services.google.maps_api_key'))
    function loadGoogleMapsAPI() {
        const script = document.createElement('script');
        script.src = `https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&libraries=places&callback=initAddressAutocomplete`;
        script.async = true;
        script.defer = true;
        document.head.appendChild(script);
    }
    
    // Call loadGoogleMapsAPI when the page loads
    document.addEventListener('DOMContentLoaded', loadGoogleMapsAPI);
    @endif
</script>
@endpush
@endsection