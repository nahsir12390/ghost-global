@extends('layouts.admin')

@section('title', 'Products')
@section('breadcrumb', 'Products')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Products</h1>
            <p class="text-gray-600">Manage your store products</p>
        </div>
        @if(!auth()->user()->isVendor() || auth()->user()->isVendorVerified())
            <a href="{{ route('admin.products.create') }}" 
               class="btn-primary inline-flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add Product
            </a>
        @else
            <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                Your vendor account is not verified yet. Product posting is disabled until approval.
            </div>
        @endif
    </div>

    <!-- Livewire Component -->
    <div class="bg-white shadow-sm rounded-lg">
        <livewire:admin.products.index />
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('livewire:initialized', () => {
        // Confirm delete modal
        Livewire.on('confirmDelete', (event) => {
            if (confirm('Are you sure you want to delete this product?')) {
                Livewire.dispatch('deleteProduct', { id: event.detail.id });
            }
        });

        // Show success notification
        Livewire.on('showNotification', (event) => {
            if (window.KeffiCart?.notify) {
                window.KeffiCart.notify(event.detail.message, 'success');
            }
        });
    });
</script>
@endpush
