@extends('layouts.app')

@section('title', $product->name)
@section('meta_title', $product->name . ' - ' . \App\Helpers\SettingsHelper::get('site_name', config('app.name', 'E-Commerce')))
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags($product->description ?: 'View this product and place your order online.'), 155))
@section('canonical', route('product.show', $product->slug))
@section('og_type', 'product')
@php
    $productImages = is_array($product->images) ? $product->images : (is_string($product->images) ? json_decode($product->images, true) : []);
    $productShareImage = !empty($productImages) ? asset('storage/' . $productImages[0]) : asset('storage/logo.png');
@endphp
@section('share_image', $productShareImage)

@section('content')
    @livewire('product-details', ['product' => $product])
@endsection
