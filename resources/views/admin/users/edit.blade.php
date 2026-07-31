@extends('layouts.admin')

@section('title', 'Edit ' . ($user->isVendor() ? 'Vendor' : 'Customer') . ': ' . $user->name)

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="md:flex md:items-center md:justify-between mb-6">
            <div class="flex-1 min-w-0">
                <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                    Edit {{ $user->isVendor() ? 'Vendor' : 'Customer' }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Update {{ $user->isVendor() ? 'vendor' : 'customer' }} information
                </p>
            </div>
            <div class="mt-4 flex md:mt-0 md:ml-4">
                <a href="{{ route('admin.users.show', $user) }}" 
                   class="ml-3 inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to {{ $user->isVendor() ? 'Vendor' : 'Customer' }}
                </a>
            </div>
        </div>

        <!-- Flash Messages -->
        @if ($errors->any())
            <div class="rounded-md bg-red-50 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.346 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">There were errors with your submission:</h3>
                        <div class="mt-2 text-sm text-red-700">
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Edit Form -->
        <div class="bg-white shadow rounded-lg">
            <form action="{{ route('admin.users.update', $user) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">{{ $user->isVendor() ? 'Vendor Information' : 'Customer Information' }}</h3>
                </div>
                
                <div class="p-6 space-y-6">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                            Full Name *
                        </label>
                        <input type="text"
                               name="name"
                               id="name"
                               value="{{ old('name', $user->name) }}"
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-red-500 focus:border-red-500"
                               placeholder="John Doe">
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                            Email Address *
                        </label>
                        <input type="email"
                               name="email"
                               id="email"
                               value="{{ old('email', $user->email) }}"
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-red-500 focus:border-red-500"
                               placeholder="john@example.com">
                    </div>

                    <!-- Phone -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
                            Phone Number
                        </label>
                        <input type="text"
                               name="phone"
                               id="phone"
                               value="{{ old('phone', $user->phone) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-red-500 focus:border-red-500"
                               placeholder="+234 800 000 0000">
                    </div>

                    <!-- Address -->
                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-1">
                            Address
                        </label>
                        <textarea name="address"
                                  id="address"
                                  rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                  placeholder="Enter customer's address">{{ old('address', $user->address) }}</textarea>
                    </div>

                    @if($user->isVendor())
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div class="md:col-span-2">
                                <label for="store_name" class="block text-sm font-medium text-gray-700 mb-1">
                                    Store Name
                                </label>
                                <input type="text"
                                       name="store_name"
                                       id="store_name"
                                       value="{{ old('store_name', $user->store_name) }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                       placeholder="Vendor store name">
                            </div>

                            <div class="md:col-span-2 rounded-lg border border-gray-200 bg-gray-50 p-4">
                                <div class="flex items-start gap-3">
                                    <input type="hidden" name="vendor_is_active" value="0">
                                    <input type="checkbox"
                                           name="vendor_is_active"
                                           id="vendor_is_active"
                                           value="1"
                                           @checked(old('vendor_is_active', $user->vendor_is_active ?? true))
                                           class="mt-1 h-4 w-4 rounded border-gray-300 text-red-600 focus:ring-red-500">
                                    <div>
                                        <label for="vendor_is_active" class="block text-sm font-semibold text-gray-900">
                                            Vendor is active and accepting orders
                                        </label>
                                        <p class="mt-1 text-sm text-gray-500">
                                            Turn this off when the vendor is unavailable. Customers will be blocked from ordering this vendor's products.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label for="verification_email" class="block text-sm font-medium text-gray-700 mb-1">
                                    Verification Email
                                </label>
                                <input type="email"
                                       name="verification_email"
                                       id="verification_email"
                                       value="{{ old('verification_email', $user->verification_email) }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                       placeholder="verification@example.com">
                            </div>

                            <div>
                                <label for="verification_phone" class="block text-sm font-medium text-gray-700 mb-1">
                                    Verification Phone
                                </label>
                                <input type="text"
                                       name="verification_phone"
                                       id="verification_phone"
                                       value="{{ old('verification_phone', $user->verification_phone) }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                       placeholder="+234...">
                            </div>

                            <div class="md:col-span-2">
                                <label for="verification_nin" class="block text-sm font-medium text-gray-700 mb-1">
                                    NIN
                                </label>
                                <input type="text"
                                       name="verification_nin"
                                       id="verification_nin"
                                       value="{{ old('verification_nin', $user->verification_nin) }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                       placeholder="Vendor NIN">
                            </div>

                            <div class="md:col-span-2">
                                <label for="verification_notes" class="block text-sm font-medium text-gray-700 mb-1">
                                    Verification Notes
                                </label>
                                <textarea name="verification_notes"
                                          id="verification_notes"
                                          rows="4"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                          placeholder="Admin notes about verification">{{ old('verification_notes', $user->verification_notes) }}</textarea>
                            </div>

                            <div>
                                <p class="block text-sm font-medium text-gray-700 mb-2">Front Document</p>
                                @if($user->verification_id_front_path)
                                    <a href="{{ asset('storage/'.$user->verification_id_front_path) }}" target="_blank" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                                        Open Front Document
                                    </a>
                                @else
                                    <p class="text-sm text-gray-500">No front document uploaded yet.</p>
                                @endif
                            </div>

                            <div>
                                <p class="block text-sm font-medium text-gray-700 mb-2">Back Document</p>
                                @if($user->verification_id_back_path)
                                    <a href="{{ asset('storage/'.$user->verification_id_back_path) }}" target="_blank" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                                        Open Back Document
                                    </a>
                                @else
                                    <p class="text-sm text-gray-500">No back document uploaded yet.</p>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Form Actions -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end space-x-3">
                    <a href="{{ route('admin.users.show', $user) }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Cancel
                    </a>
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                        </svg>
                        Update {{ $user->isVendor() ? 'Vendor' : 'Customer' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
