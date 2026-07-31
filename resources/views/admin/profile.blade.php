@extends('layouts.admin')

@section('title', 'Profile')
@section('breadcrumb', 'Profile')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    @livewire('admin.profile')
</div>
@endsection