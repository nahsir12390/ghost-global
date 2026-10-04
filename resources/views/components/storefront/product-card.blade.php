@props(['product', 'tag' => null])

@php
    $symbol = \App\Helpers\SettingsHelper::currencySymbol();
    $discount = $product->discount_percentage;
@endphp

<article {{ $attributes->merge(['class' => 'group min-w-0']) }} data-tilt-card data-product-id="{{ $product->id }}">
    <a href="{{ route('product.show', $product->slug) }}" class="block">
        <div class="relative aspect-[4/5] overflow-hidden rounded-[1.4rem] bg-[#ebe8e1]">
            @if($product->main_image)
                <img src="{{ asset('storage/' . $product->main_image) }}" alt="{{ $product->name }}" class="h-full w-full object-cover transition duration-700 ease-out group-hover:scale-[1.045]" loading="lazy">
            @else
                <div class="flex h-full items-center justify-center bg-gradient-to-br from-slate-100 to-stone-200 text-5xl text-slate-300">✦</div>
            @endif

            <div class="absolute inset-x-0 top-0 flex items-start justify-between p-3">
                <div class="flex flex-wrap gap-2">
                    @if($tag)<span class="rounded-full bg-slate-950/90 px-3 py-1.5 text-[9px] font-bold uppercase tracking-[.16em] text-white backdrop-blur">{{ $tag }}</span>@endif
                    @if($discount > 0)<span class="rounded-full bg-red-500 px-3 py-1.5 text-[9px] font-bold uppercase tracking-[.14em] text-white">-{{ $discount }}%</span>@endif
                </div>
                <span class="grid h-9 w-9 place-items-center rounded-full border border-white/50 bg-white/85 text-sm text-slate-900 shadow-sm backdrop-blur transition group-hover:-translate-y-0.5 group-hover:rotate-12">↗</span>
            </div>

            <div class="absolute inset-x-3 bottom-3 translate-y-3 rounded-2xl border border-white/40 bg-white/80 px-4 py-3 opacity-0 shadow-lg backdrop-blur-xl transition duration-300 group-hover:translate-y-0 group-hover:opacity-100">
                <span class="text-[10px] font-bold uppercase tracking-[.16em] text-slate-500">View product</span>
            </div>
        </div>

        <div class="px-1 pt-4">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-[10px] font-bold uppercase tracking-[.16em] text-slate-400">{{ $product->category?->name ?? ucfirst($product->product_type) }}</p>
                    <h3 class="mt-1 truncate text-sm font-semibold tracking-tight text-slate-950 sm:text-base">{{ $product->name }}</h3>
                </div>
                @if($product->isPhysical() && is_array($product->delivery_countries) && count($product->delivery_countries))
                    <span class="shrink-0 rounded-full bg-slate-100 px-2 py-1 text-[9px] font-bold uppercase tracking-wide text-slate-500">Global</span>
                @endif
            </div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-sm font-bold text-slate-950 sm:text-base">{{ $symbol }}{{ number_format((float) $product->price, 2) }}</span>
                @if($product->compare_price && $product->compare_price > $product->price)
                    <span class="text-xs text-slate-400 line-through">{{ $symbol }}{{ number_format((float) $product->compare_price, 2) }}</span>
                @endif
            </div>
        </div>
    </a>
</article>
