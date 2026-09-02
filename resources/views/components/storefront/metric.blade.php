@props(['value', 'label', 'dark' => false])

<div {{ $attributes->class(['border-l pl-4 sm:pl-6', $dark ? 'border-white/15' : 'border-slate-200']) }}>
    <strong class="block text-xl font-semibold tracking-tight sm:text-2xl {{ $dark ? 'text-white' : 'text-slate-950' }}">{{ $value }}</strong>
    <span class="mt-1 block text-[10px] font-semibold uppercase tracking-[0.18em] {{ $dark ? 'text-white/45' : 'text-slate-400' }}">{{ $label }}</span>
</div>
