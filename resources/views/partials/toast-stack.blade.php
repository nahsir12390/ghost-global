@php
    $initialToasts = [];

    if (session('success')) {
        $initialToasts[] = ['message' => session('success'), 'type' => 'success'];
    }

    if (session('error')) {
        $initialToasts[] = ['message' => session('error'), 'type' => 'error'];
    }

    if (session('status')) {
        $initialToasts[] = ['message' => session('status'), 'type' => 'info'];
    }

    $viewErrors = $errors ?? null;

    if ($viewErrors && method_exists($viewErrors, 'all')) {
        foreach ($viewErrors->all() as $error) {
            $initialToasts[] = ['message' => $error, 'type' => 'error'];
        }
    }
@endphp

<div
    id="app-toast-root"
    class="pointer-events-none fixed inset-x-0 top-4 z-[9999] flex justify-center px-4 sm:justify-end sm:px-6"
    aria-live="polite"
    aria-atomic="true"
></div>

<script>
    (() => {
        if (window.__keffiToastInitialized) {
            return;
        }

        window.__keffiToastInitialized = true;
        window.__keffiToastLivewireBound = false;

        const initialToasts = @json($initialToasts);
        const icons = {
            success:
                '<svg class="h-5 w-5 text-emerald-600" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>',
            error:
                '<svg class="h-5 w-5 text-red-600" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>',
            warning:
                '<svg class="h-5 w-5 text-amber-600" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.981-1.742 2.981H4.42c-1.53 0-2.492-1.647-1.742-2.98l5.58-9.92zM11 13a1 1 0 10-2 0 1 1 0 002 0zm-1-6a1 1 0 00-1 1v3a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>',
            info:
                '<svg class="h-5 w-5 text-sky-600" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M18 10A8 8 0 112 10a8 8 0 0116 0zm-8-4a1 1 0 100 2 1 1 0 000-2zm-1 4a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2h-.001V12A1 1 0 0010 10H9z" clip-rule="evenodd" /></svg>',
        };

        const toneClasses = {
            success: 'border-emerald-200 bg-white text-slate-900',
            error: 'border-red-200 bg-white text-slate-900',
            warning: 'border-amber-200 bg-white text-slate-900',
            info: 'border-sky-200 bg-white text-slate-900',
        };

        function getRoot() {
            return document.getElementById('app-toast-root');
        }

        function normalizePayload(payload, fallbackType = 'success') {
            if (!payload) {
                return null;
            }

            if (typeof payload === 'string') {
                return { message: payload, type: fallbackType };
            }

            if (Array.isArray(payload)) {
                if (typeof payload[0] === 'string') {
                    return {
                        message: payload[0],
                        type: payload[1] || fallbackType,
                    };
                }

                return normalizePayload(payload[0] ?? null, fallbackType);
            }

            if (payload.detail) {
                return normalizePayload(payload.detail, fallbackType);
            }

            if (payload.message) {
                return {
                    message: payload.message,
                    type: payload.type || fallbackType,
                };
            }

            return null;
        }

        function removeToast(toast) {
            if (!toast || !toast.parentNode) {
                return;
            }

            toast.classList.remove('translate-y-0', 'opacity-100', 'scale-100');
            toast.classList.add('-translate-y-2', 'opacity-0', 'scale-95');

            window.setTimeout(() => {
                toast.remove();
            }, 220);
        }

        function renderToast(payload) {
            const data = normalizePayload(payload);
            const root = getRoot();

            if (!root || !data?.message) {
                return;
            }

            const type = ['success', 'error', 'warning', 'info'].includes(data.type) ? data.type : 'success';
            const toast = document.createElement('div');

            toast.className = [
                'pointer-events-auto w-full max-w-sm overflow-hidden rounded-2xl border shadow-xl shadow-slate-900/10 backdrop-blur',
                'transition duration-200 ease-out opacity-0 -translate-y-2 scale-95',
                toneClasses[type],
            ].join(' ');

            toast.innerHTML = `
                <div class="flex items-start gap-3 px-4 py-3.5 sm:px-5">
                    <div class="mt-0.5 shrink-0">${icons[type]}</div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold capitalize">${type}</p>
                        <p class="mt-1 text-sm leading-5 text-slate-600">${data.message}</p>
                    </div>
                    <button type="button" class="shrink-0 rounded-full p-1 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600" aria-label="Dismiss notification">
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            `;

            root.appendChild(toast);

            requestAnimationFrame(() => {
                toast.classList.remove('opacity-0', '-translate-y-2', 'scale-95');
                toast.classList.add('opacity-100', 'translate-y-0', 'scale-100');
            });

            const dismissButton = toast.querySelector('button');
            dismissButton?.addEventListener('click', () => removeToast(toast));

            window.setTimeout(() => {
                removeToast(toast);
            }, type === 'error' ? 6500 : 4500);
        }

        function dispatchToast(message, type = 'success') {
            renderToast({ message, type });
        }

        window.AppNotify = dispatchToast;
        window.KeffiCart = window.KeffiCart || {};
        window.KeffiCart.notify = dispatchToast;

        window.addEventListener('app:notify', (event) => {
            renderToast(event);
        });

        function bindLivewireListeners() {
            if (!window.Livewire?.on || window.__keffiToastLivewireBound) {
                return;
            }

            window.__keffiToastLivewireBound = true;
            window.Livewire.on('notify', (...payload) => renderToast(payload));
            window.Livewire.on('showNotification', (...payload) => renderToast(payload));
        }

        bindLivewireListeners();
        document.addEventListener('livewire:initialized', bindLivewireListeners);

        initialToasts.forEach((toast, index) => {
            window.setTimeout(() => renderToast(toast), index * 120);
        });
    })();
</script>
