@extends('layouts.app')

@section('title', 'My Wishlist')

@section('content')
    <x-storefront.page-hero eyebrow="Saved for later" title="Your wishlist." description="A personal edit of everything that caught your eye." />
    <div class="bg-[#f5f3ee] py-8 sm:py-12">@livewire('wishlist-component')</div>
@endsection
