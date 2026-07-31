@php
    $pwaSiteName = \App\Helpers\SettingsHelper::get('site_name', config('app.name', 'E-Commerce'));
@endphp

<div
    id="pwa-install-prompt"
    class="fixed inset-x-4 bottom-4 z-[9998] hidden max-w-sm rounded-3xl border border-red-100 bg-white/95 p-4 shadow-2xl backdrop-blur sm:left-4 sm:right-auto"
>
    <div class="flex items-start gap-3">
        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-red-600 text-lg font-bold text-white">
            {{ strtoupper(mb_substr($pwaSiteName, 0, 1)) }}
        </div>
        <div class="min-w-0 flex-1">
            <p class="text-sm font-semibold text-gray-900">Install {{ $pwaSiteName }}</p>
            <p class="mt-1 text-xs leading-5 text-gray-600">
                Add this app to your device for faster access and a more app-like experience.
            </p>
        </div>
        <button
            type="button"
            id="pwa-dismiss-prompt"
            class="inline-flex h-8 w-8 items-center justify-center rounded-full text-gray-400 transition hover:bg-gray-100 hover:text-gray-600"
            aria-label="Dismiss install prompt"
        >
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <div class="mt-4 flex items-center gap-3">
        <button type="button" id="pwa-install-button" class="btn-primary flex-1 text-sm">
            Install App
        </button>
        <button
            type="button"
            id="pwa-ios-button"
            class="hidden rounded-xl border border-gray-200 px-4 py-3 text-sm font-medium text-gray-700 transition hover:bg-gray-50"
        >
            iPhone Steps
        </button>
    </div>

    <p id="pwa-ios-hint" class="mt-3 hidden text-xs leading-5 text-gray-500">
        On iPhone or iPad, tap the Share button in Safari, then choose "Add to Home Screen".
    </p>

    <p id="pwa-install-unavailable" class="mt-3 hidden text-xs leading-5 text-amber-600">
        App install is not available in this browser session yet. Try Chrome or Edge on Android or desktop, or use the iPhone steps in Safari.
    </p>
</div>
