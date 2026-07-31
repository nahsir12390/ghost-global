@extends('layouts.app')

@section('title', 'Become a Vendor')

@section('content')
    <div class="max-w-4xl mx-auto px-4 py-10 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Become a Vendor</h1>
            <p class="mt-2 text-gray-600">Submit your store, verification, and payout details for admin review.</p>
        </div>

        @if ($errors->any())
            <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                <ul class="list-disc space-y-1 pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('vendor-upgrade.store') }}" method="POST" class="space-y-6 rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            @csrf

            <div class="grid gap-5 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label for="store_name" class="mb-1 block text-sm font-medium text-gray-700">Store Name</label>
                    <input id="store_name" name="store_name" value="{{ old('store_name', $user->store_name) }}" required class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                </div>

                <div>
                    <label for="phone" class="mb-1 block text-sm font-medium text-gray-700">Business Phone</label>
                    <input id="phone" name="phone" value="{{ old('phone', $user->phone) }}" required class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                </div>

                <div>
                    <label for="verification_email" class="mb-1 block text-sm font-medium text-gray-700">Verification Email</label>
                    <input id="verification_email" name="verification_email" type="email" value="{{ old('verification_email', $user->email) }}" required class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                </div>

                <div>
                    <label for="verification_phone" class="mb-1 block text-sm font-medium text-gray-700">Verification Phone</label>
                    <input id="verification_phone" name="verification_phone" value="{{ old('verification_phone', $user->phone) }}" required class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                </div>

                <div class="md:col-span-2">
                    <label for="address" class="mb-1 block text-sm font-medium text-gray-700">Business Address</label>
                    <textarea id="address" name="address" rows="3" required class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">{{ old('address', $user->address) }}</textarea>
                </div>

                <div class="md:col-span-2">
                    <label for="store_description" class="mb-1 block text-sm font-medium text-gray-700">Store Description</label>
                    <textarea id="store_description" name="store_description" rows="4" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">{{ old('store_description', $user->store_description) }}</textarea>
                    <p class="mt-1 text-xs text-gray-500">Optional. Tell customers what makes your store special.</p>
                </div>
            </div>

            <div class="border-t border-gray-200 pt-6">
                <h2 class="text-lg font-semibold text-gray-900">Store Links</h2>
                <div class="mt-4 grid gap-5 md:grid-cols-2">
                    <div>
                        <label for="store_whatsapp" class="mb-1 block text-sm font-medium text-gray-700">WhatsApp Number</label>
                        <input id="store_whatsapp" name="store_whatsapp" value="{{ old('store_whatsapp', $user->store_whatsapp) }}" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500" placeholder="2348012345678">
                    </div>
                    <div>
                        <label for="store_website" class="mb-1 block text-sm font-medium text-gray-700">Website</label>
                        <input id="store_website" name="store_website" value="{{ old('store_website', $user->store_website) }}" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500" placeholder="https://example.com">
                    </div>
                    <div>
                        <label for="store_instagram" class="mb-1 block text-sm font-medium text-gray-700">Instagram</label>
                        <input id="store_instagram" name="store_instagram" value="{{ old('store_instagram', $user->store_instagram) }}" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500" placeholder="@yourstore">
                    </div>
                    <div>
                        <label for="store_facebook" class="mb-1 block text-sm font-medium text-gray-700">Facebook</label>
                        <input id="store_facebook" name="store_facebook" value="{{ old('store_facebook', $user->store_facebook) }}" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500" placeholder="Store page or URL">
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-200 pt-6">
                <h2 class="text-lg font-semibold text-gray-900">Payout Details</h2>
                <div class="mt-4 grid gap-5 md:grid-cols-2">
                    <div>
                        <label for="bank_name" class="mb-1 block text-sm font-medium text-gray-700">Bank Name</label>
                        <input id="bank_name" name="bank_name" value="{{ old('bank_name') }}" required class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                    </div>
                    <div>
                        <label for="bank_account_name" class="mb-1 block text-sm font-medium text-gray-700">Account Name</label>
                        <input id="bank_account_name" name="bank_account_name" value="{{ old('bank_account_name', $user->name) }}" required class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                    </div>
                    <div class="md:col-span-2">
                        <label for="bank_account_number" class="mb-1 block text-sm font-medium text-gray-700">Account Number</label>
                        <input id="bank_account_number" name="bank_account_number" value="{{ old('bank_account_number') }}" required class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                    </div>
                </div>
            </div>

            <div class="flex flex-col gap-3 border-t border-gray-200 pt-6 sm:flex-row sm:justify-end">
                <a href="{{ route('user.dashboard') }}" class="rounded-lg border border-gray-300 px-5 py-2.5 text-center text-sm font-semibold text-gray-700 hover:bg-gray-50">Cancel</a>
                <button type="submit" class="rounded-lg bg-red-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-red-700">Submit Vendor Request</button>
            </div>
        </form>
    </div>
@endsection
