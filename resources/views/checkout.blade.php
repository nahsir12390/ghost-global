@extends('layouts.app')

@section('title', 'Checkout - ' . config('app.name'))

@section('content')
<x-storefront.page-hero eyebrow="Secure checkout" title="Almost yours." description="Confirm delivery details and choose how you would like to pay." step="Step 2 of 2" />
<div class="min-h-screen bg-[#f5f3ee] py-10 sm:py-14">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Checkout Content -->
        <div>
            @livewire('checkout')
        </div>
    </div>
</div>

@endsection
