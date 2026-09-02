@props([
    'eyebrow',
    'title',
    'description' => null,
    'step' => null,
])

<section class="storefront-page-hero relative isolate overflow-hidden bg-[#0a0a0a] px-5 py-14 text-white sm:px-8 sm:py-20 lg:px-12">
    <div class="storefront-noise absolute inset-0 opacity-30" aria-hidden="true"></div>
    <div class="storefront-page-orb" aria-hidden="true"></div>
    <div class="relative z-10 mx-auto flex max-w-[90rem] flex-col gap-8 sm:flex-row sm:items-end sm:justify-between">
        <div class="max-w-3xl">
            <div class="storefront-eyebrow storefront-eyebrow--dark"><span class="storefront-eyebrow__dot"></span>{{ $eyebrow }}</div>
            <h1 class="mt-5 text-4xl font-semibold leading-none tracking-[-0.055em] sm:text-6xl lg:text-7xl">{{ $title }}</h1>
            @if($description)
                <p class="mt-5 max-w-2xl text-sm leading-6 text-white/55 sm:text-base">{{ $description }}</p>
            @endif
        </div>
        @if($step)
            <div class="flex items-center gap-3 rounded-full border border-white/10 bg-white/5 px-5 py-3 text-xs font-semibold uppercase tracking-[.18em] text-white/55 backdrop-blur-xl">
                <span class="h-2 w-2 rounded-full bg-red-500 shadow-[0_0_12px_rgba(239,68,68,.8)]"></span>{{ $step }}
            </div>
        @endif
    </div>
</section>
