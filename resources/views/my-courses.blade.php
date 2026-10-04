@extends('layouts.app')

@section('title', 'My Courses')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-10 sm:px-6 lg:px-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">My Courses</h1>
        <p class="mt-2 text-sm text-gray-600">All your purchased course enrollments in one place.</p>
    </div>

    @if($products->isEmpty())
        <div class="rounded-2xl border border-dashed border-gray-300 bg-white px-6 py-14 text-center">
            <h2 class="text-xl font-semibold text-gray-900">No courses yet</h2>
            <p class="mt-2 text-sm text-gray-600">When you buy a course, it will appear here automatically.</p>
            <a href="{{ route('shop') }}" class="mt-6 inline-flex rounded-xl bg-red-600 px-5 py-3 text-sm font-semibold text-white hover:bg-red-700">
                Explore Courses
            </a>
        </div>
    @else
        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
            @foreach($products as $product)
                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                    <div class="mb-4 flex items-start justify-between gap-3">
                        <div>
                            <span class="inline-flex rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">Online Course</span>
                            <h2 class="mt-3 text-lg font-semibold text-gray-900">{{ $product->name }}</h2>
                        </div>
                        <span class="text-sm font-semibold text-gray-500">{{ \App\Helpers\SettingsHelper::currency($product->price) }}</span>
                    </div>

                    @if($product->description)
                        <p class="text-sm text-gray-600">{{ \Illuminate\Support\Str::limit($product->description, 120) }}</p>
                    @endif

                    @if($product->access_instructions)
                        <div class="mt-4 rounded-xl bg-gray-50 px-4 py-3 text-sm text-gray-700">
                            {{ $product->access_instructions }}
                        </div>
                    @endif

                    <div class="mt-5 flex flex-wrap gap-3">
                        @if($product->course_access_url)
                            <a href="{{ $product->course_access_url }}"
                               target="_blank"
                               rel="noopener"
                               class="inline-flex items-center rounded-xl bg-red-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-red-700">
                                Open Course
                            </a>
                        @endif
                        <a href="{{ route('product.show', $product) }}"
                           class="inline-flex items-center rounded-xl border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                            View Product
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
