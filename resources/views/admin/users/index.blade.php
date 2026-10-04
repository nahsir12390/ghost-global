@extends('layouts.admin')

@section('title', 'Users Management')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="md:flex md:items-center md:justify-between mb-6">
            <div class="flex-1 min-w-0">
                <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                    Users Management
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Manage customer accounts and contact information
                </p>
            </div>
        </div>

        <!-- Livewire Component -->
        @livewire('admin.users.index')
    </div>
</div>
@endsection