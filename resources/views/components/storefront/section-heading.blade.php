@props(['eyebrow', 'title', 'description' => null, 'align' => 'left', 'theme' => 'light'])

@php
    $isCentered = $align === 'center';
    $isDark = $theme === 'dark';
@endphp

<div {{ $attributes->class([$isCentered ? 'mx-auto max-w-3xl text-center' : 'max-w-2xl']) }}>
    <div class="storefront-eyebrow {{ $isDark ? 'storefront-eyebrow--dark' : '' }}">
        <span class="storefront-eyebrow__dot"></span>{{ $eyebrow }}
    </div>
    <h2 class="mt-5 text-3xl font-semibold leading-[1.04] tracking-[-0.045em] sm:text-4xl lg:text-6xl {{ $isDark ? 'text-white' : 'text-slate-950' }}">{{ $title }}</h2>
    @if($description)
        <p class="mt-5 text-base leading-7 sm:text-lg {{ $isDark ? 'text-white/60' : 'text-slate-500' }}">{{ $description }}</p>
    @endif
</div>
