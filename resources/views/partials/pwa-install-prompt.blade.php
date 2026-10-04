@php
    $pwaSiteName = \App\Helpers\SettingsHelper::siteName();
@endphp

<aside
    id="pwa-install-prompt"
    aria-labelledby="pwa-install-title"
    class="fixed inset-x-3 bottom-3 z-[9998] hidden overflow-hidden rounded-[1.75rem] border border-white/10 bg-[#101010]/95 text-white shadow-[0_30px_80px_-24px_rgba(0,0,0,.7)] backdrop-blur-xl sm:bottom-5 sm:left-5 sm:right-auto sm:w-[25rem]"
>
    <div class="relative p-5">
        <div class="absolute -right-12 -top-16 h-40 w-40 rounded-full bg-red-600/30 blur-3xl" aria-hidden="true"></div>
        <div class="relative flex items-start gap-4">
            <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-red-500 to-red-700 text-lg font-bold shadow-lg shadow-red-950/50">
                {{ strtoupper(mb_substr($pwaSiteName, 0, 1)) }}
            </div>
            <div class="min-w-0 flex-1">
                <span class="text-[10px] font-bold uppercase tracking-[.2em] text-red-400">Ready to install</span>
                <h2 id="pwa-install-title" class="mt-1 text-lg font-semibold tracking-tight">Take {{ $pwaSiteName }} with you.</h2>
                <p class="mt-1 text-xs leading-5 text-white/55">Faster access, a focused app window and order updates from your home screen.</p>
            </div>
            <button type="button" id="pwa-dismiss-prompt" class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-white/5 text-white/50 transition hover:bg-white/10 hover:text-white" aria-label="Dismiss install prompt">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12" /></svg>
            </button>
        </div>

        <div class="relative mt-5 grid grid-cols-3 gap-2 text-center">
            <div class="rounded-xl bg-white/5 px-2 py-2"><strong class="block text-xs">Quick</strong><span class="text-[10px] text-white/40">Launch</span></div>
            <div class="rounded-xl bg-white/5 px-2 py-2"><strong class="block text-xs">Secure</strong><span class="text-[10px] text-white/40">Session</span></div>
            <div class="rounded-xl bg-white/5 px-2 py-2"><strong class="block text-xs">Live</strong><span class="text-[10px] text-white/40">Updates</span></div>
        </div>

        <div class="relative mt-4 flex gap-2">
            <button type="button" id="pwa-install-button" class="hidden flex-1 rounded-2xl bg-red-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-red-500">Install app</button>
            <button type="button" id="pwa-ios-button" class="hidden flex-1 rounded-2xl bg-white px-4 py-3 text-sm font-semibold text-slate-950 transition hover:bg-slate-100">Show iPhone steps</button>
        </div>

        <p id="pwa-ios-hint" class="relative mt-3 hidden rounded-xl border border-white/10 bg-white/5 px-3 py-2.5 text-xs leading-5 text-white/65">In Safari, tap Share, scroll down and choose “Add to Home Screen”.</p>
        <p id="pwa-install-unavailable" class="relative mt-3 hidden rounded-xl border border-amber-400/20 bg-amber-400/10 px-3 py-2.5 text-xs leading-5 text-amber-200">Installation is not offered in this browser session. Use Safari’s Add to Home Screen or try Chrome on Android or desktop.</p>
    </div>
</aside>
