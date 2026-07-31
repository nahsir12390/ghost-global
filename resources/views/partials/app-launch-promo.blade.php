@php
    $launchSiteName = \App\Helpers\SettingsHelper::get('site_name', config('app.name', 'E-Commerce'));
@endphp

<div
    x-data="{
        showModal: false,
        showBanner: false,
        init() {
            const dismissed = localStorage.getItem('app-launch-promo-dismissed') === '1';
            const modalSeen = localStorage.getItem('app-launch-promo-modal-seen') === '1';

            if (!dismissed) {
                this.showBanner = true;
            }

            if (!dismissed && !modalSeen) {
                setTimeout(() => {
                    this.showModal = true;
                    this.showBanner = false;
                }, 900);
            }
        },
        closeModal(showBanner = true) {
            this.showModal = false;
            localStorage.setItem('app-launch-promo-modal-seen', '1');
            this.showBanner = showBanner && localStorage.getItem('app-launch-promo-dismissed') !== '1';
        },
        dismissAll() {
            this.showModal = false;
            this.showBanner = false;
            localStorage.setItem('app-launch-promo-dismissed', '1');
            localStorage.setItem('app-launch-promo-modal-seen', '1');
        }
    }"
    x-cloak
    class="pointer-events-none"
>
    <div
        x-show="showModal"
        x-transition.opacity
        class="pointer-events-auto fixed inset-0 z-[120] flex items-end bg-slate-950/60 px-4 py-4 sm:items-center sm:justify-center sm:px-6"
        style="display: none;"
    >
        <div
            x-show="showModal"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-6 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-6 sm:translate-y-0 sm:scale-95"
            @click.away="closeModal(true)"
            class="w-full max-w-lg overflow-hidden rounded-[2rem] bg-white shadow-2xl"
            style="display: none;"
        >
            <div class="relative overflow-hidden bg-gradient-to-br from-red-600 via-red-700 to-slate-950 px-5 py-6 text-white sm:px-8 sm:py-8">
                <button
                    type="button"
                    @click="dismissAll()"
                    class="absolute right-4 top-4 inline-flex h-10 w-10 items-center justify-center rounded-full bg-white/10 text-white transition hover:bg-white/20"
                    aria-label="Close app launch announcement"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>

                <div class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em]">
                    <span class="inline-flex h-2.5 w-2.5 rounded-full bg-emerald-300"></span>
                    Launch Update
                </div>

                <h2 class="mt-5 text-2xl font-bold leading-tight sm:text-4xl">
                    {{ $launchSiteName }} Mobile App is Launching Soon
                </h2>
                <p class="mt-3 max-w-md text-sm leading-6 text-red-50 sm:text-base">
                    We're preparing a faster mobile shopping experience with app-style browsing, order tracking, and launch-only perks.
                </p>
            </div>

            <div class="space-y-5 px-5 py-5 sm:px-8 sm:py-7">
                <div class="grid gap-3 sm:grid-cols-3">
                    <div class="rounded-2xl border border-red-100 bg-red-50/60 p-4">
                        <p class="text-sm font-semibold text-slate-900">Faster access</p>
                        <p class="mt-1 text-xs leading-5 text-slate-600">Open the store quickly from your phone.</p>
                    </div>
                    <div class="rounded-2xl border border-red-100 bg-red-50/60 p-4">
                        <p class="text-sm font-semibold text-slate-900">Order updates</p>
                        <p class="mt-1 text-xs leading-5 text-slate-600">Track orders and alerts more easily.</p>
                    </div>
                    <div class="rounded-2xl border border-red-100 bg-red-50/60 p-4">
                        <p class="text-sm font-semibold text-slate-900">Launch perks</p>
                        <p class="mt-1 text-xs leading-5 text-slate-600">Expect special offers when it goes live.</p>
                    </div>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-sm text-slate-500">
                        We’ll keep the web app fully available while the mobile app gets ready.
                    </p>
                    <div class="flex flex-col gap-3 sm:flex-row">
                        <button
                            type="button"
                            @click="dismissAll()"
                            class="rounded-xl border border-slate-200 px-4 py-3 text-sm font-medium text-slate-600 transition hover:bg-slate-50"
                        >
                            Don’t show again
                        </button>
                        <button
                            type="button"
                            @click="closeModal(true)"
                            class="rounded-xl bg-red-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-red-700"
                        >
                            Continue Shopping
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div
        x-show="showBanner && !showModal"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-4"
        class="pointer-events-auto fixed bottom-24 left-4 right-4 z-[110] sm:bottom-6 sm:left-6 sm:right-auto sm:max-w-md"
        style="display: none;"
    >
        <div class="overflow-hidden rounded-[1.75rem] border border-red-100 bg-white shadow-2xl">
            <div class="flex items-start gap-4 px-4 py-4 sm:px-5">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-red-600 to-rose-600 text-lg font-bold text-white">
                    A
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-semibold text-slate-900">Mobile App Launching Soon</p>
                    <p class="mt-1 text-xs leading-5 text-slate-600">
                        A dedicated app version of {{ $launchSiteName }} is on the way.
                    </p>
                    <div class="mt-3 flex flex-wrap items-center gap-2">
                        <button
                            type="button"
                            @click="showModal = true; showBanner = false"
                            class="rounded-full bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-700 transition hover:bg-red-100"
                        >
                            Learn more
                        </button>
                        <button
                            type="button"
                            @click="dismissAll()"
                            class="rounded-full bg-slate-100 px-3 py-1.5 text-xs font-medium text-slate-600 transition hover:bg-slate-200"
                        >
                            Dismiss
                        </button>
                    </div>
                </div>
                <button
                    type="button"
                    @click="dismissAll()"
                    class="inline-flex h-9 w-9 items-center justify-center rounded-full text-slate-400 transition hover:bg-slate-100 hover:text-slate-600"
                    aria-label="Dismiss launch banner"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>

