@extends('layouts.admin')

@section('title', 'Shipping Settings')
@section('breadcrumb', 'Settings / Shipping')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Shipping Settings</h1>
            <p class="text-gray-600">Configure shipping methods and rates</p>
        </div>
        <a href="{{ route('admin.settings.index') }}" 
           class="btn-secondary inline-flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Settings
        </a>
    </div>

    <!-- Livewire Component -->
    <div class="bg-white shadow-sm rounded-lg">
        <livewire:admin.settings.shipping />
    </div>
</div>
@endsection