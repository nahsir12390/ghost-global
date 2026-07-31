@extends('layouts.admin')

@section('title', 'Create Category')
@section('breadcrumb', 'Categories / Create')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Create Category</h1>
                <p class="mt-1 text-sm text-gray-500">Create a product category and optionally add a cover image.</p>
            </div>
            <a href="{{ route('admin.categories.index') }}"
               class="inline-flex items-center rounded-xl border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                Back to Categories
            </a>
        </div>

        <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @include('admin.categories._form')
        </form>
    </div>
</div>
@endsection
