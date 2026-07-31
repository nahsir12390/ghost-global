@extends('layouts.admin')

@section('title', 'Add New Product')
@section('breadcrumb', 'Products / Add New')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Add New Product</h1>
            <p class="text-gray-600">Add a new product to your store</p>
        </div>
        <a href="{{ route('admin.products.index') }}" 
           class="btn-secondary inline-flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Products
        </a>
    </div>

    <!-- Livewire Component -->
    <div class="bg-white shadow-sm rounded-lg p-6">
        <livewire:admin.products.create />
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('livewire:initialized', () => {
        // Image preview
        Livewire.on('imageAdded', () => {
            // You can add image preview functionality here
        });
    });
</script>
@endpush
