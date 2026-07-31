<div class="relative">
    <span class="cart-badge">
        @if($loading)
            <div class="inline-block h-3 w-3 animate-spin rounded-full border-2 border-solid border-white border-r-transparent"></div>
        @else
            {{ $cartCount > 99 ? '99+' : $cartCount }}
        @endif
    </span>
</div>