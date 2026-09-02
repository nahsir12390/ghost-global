import './bootstrap';

const loadStorefrontExperience = () => {
    if (document.querySelector('[data-storefront-home]')) {
        import('./storefront-experience');
    }
};

const loadAuthExperience = () => {
    if (document.querySelector('[data-auth-experience]')) {
        import('./auth-experience');
    }
};

document.addEventListener('DOMContentLoaded', loadStorefrontExperience);
document.addEventListener('livewire:navigated', loadStorefrontExperience);
document.addEventListener('DOMContentLoaded', loadAuthExperience);
document.addEventListener('livewire:navigated', loadAuthExperience);

const initialiseInterfacePolish = () => {
    if (document.documentElement.dataset.interfaceReady === 'true') return;
    document.documentElement.dataset.interfaceReady = 'true';

    const progress = document.createElement('div');
    progress.className = 'navigation-progress';
    progress.setAttribute('aria-hidden', 'true');
    document.body.appendChild(progress);

    document.addEventListener('click', (event) => {
        const link = event.target.closest('a[href]');
        if (!link || link.target === '_blank' || link.hasAttribute('download')) return;
        const url = new URL(link.href, window.location.href);
        if (url.origin !== window.location.origin || url.href === window.location.href || url.hash) return;
        progress.classList.add('navigation-progress--active');
    });

    window.addEventListener('pageshow', () => progress.classList.remove('navigation-progress--active'));

    if (window.matchMedia('(hover: hover) and (pointer: fine)').matches) {
        document.addEventListener('pointermove', (event) => {
            const card = event.target.closest('[data-tilt-card]');
            if (!card) return;
            const bounds = card.getBoundingClientRect();
            const rotateX = ((event.clientY - bounds.top) / bounds.height - 0.5) * -5;
            const rotateY = ((event.clientX - bounds.left) / bounds.width - 0.5) * 5;
            card.style.setProperty('--tilt-x', `${rotateX}deg`);
            card.style.setProperty('--tilt-y', `${rotateY}deg`);
        });

        document.addEventListener('pointerout', (event) => {
            const card = event.target.closest('[data-tilt-card]');
            if (!card || card.contains(event.relatedTarget)) return;
            card.style.removeProperty('--tilt-x');
            card.style.removeProperty('--tilt-y');
        });
    }
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initialiseInterfacePolish);
} else {
    initialiseInterfacePolish();
}

let deferredInstallPrompt = null;
let hasReloadedForServiceWorker = false;

const isIos = () =>
    /iphone|ipad|ipod/i.test(window.navigator.userAgent) &&
    !window.matchMedia('(display-mode: standalone)').matches;

const isStandalone = () =>
    window.matchMedia('(display-mode: standalone)').matches ||
    window.navigator.standalone === true;

const hideInstallPrompt = () => {
    const prompt = document.getElementById('pwa-install-prompt');
    if (prompt) {
        prompt.classList.add('hidden');
    }
};

const hideInstallHints = () => {
    document.getElementById('pwa-ios-hint')?.classList.add('hidden');
    document.getElementById('pwa-install-unavailable')?.classList.add('hidden');
};

const showUnavailableInstallHint = () => {
    const prompt = document.getElementById('pwa-install-prompt');

    if (!prompt) {
        return;
    }

    hideInstallHints();
    prompt.classList.remove('hidden');
    document.getElementById('pwa-install-unavailable')?.classList.remove('hidden');
};

const showIosInstallGuide = () => {
    const prompt = document.getElementById('pwa-install-prompt');

    if (!prompt) {
        return;
    }

    hideInstallHints();
    prompt.classList.remove('hidden');
    document.getElementById('pwa-ios-button')?.classList.remove('hidden');
    document.getElementById('pwa-ios-hint')?.classList.remove('hidden');
};

const showInstallPrompt = () => {
    const prompt = document.getElementById('pwa-install-prompt');

    if (!prompt || isStandalone()) {
        return;
    }

    if (window.localStorage.getItem('pwa-install-dismissed') === '1') {
        return;
    }

    const installButton = document.getElementById('pwa-install-button');
    const iosButton = document.getElementById('pwa-ios-button');
    const iosHint = document.getElementById('pwa-ios-hint');

    if (!installButton || !iosButton || !iosHint) {
        return;
    }

    hideInstallHints();
    installButton.classList.toggle('hidden', !deferredInstallPrompt);
    iosButton.classList.toggle('hidden', !isIos());
    iosHint.classList.add('hidden');

    if (deferredInstallPrompt || isIos()) {
        prompt.classList.remove('hidden');
    }
};

const promptInstall = async () => {
    if (isStandalone()) {
        hideInstallPrompt();
        return;
    }

    if (deferredInstallPrompt) {
        hideInstallHints();

        try {
            await deferredInstallPrompt.prompt();
            await deferredInstallPrompt.userChoice;
        } catch (error) {
            console.warn('PWA install prompt failed:', error);
            showUnavailableInstallHint();
            return;
        }

        deferredInstallPrompt = null;
        hideInstallPrompt();
        return;
    }

    if (isIos()) {
        showIosInstallGuide();
        return;
    }

    showUnavailableInstallHint();
};

const registerGlobalInstallActions = () => {
    window.triggerStoreInstallPrompt = promptInstall;
    window.showStoreIosInstallGuide = showIosInstallGuide;
    window.triggerKeffiInstallPrompt = promptInstall;
    window.showKeffiIosInstallGuide = showIosInstallGuide;
};

const registerInstallHandlers = () => {
    const dismissButton = document.getElementById('pwa-dismiss-prompt');
    const installButton = document.getElementById('pwa-install-button');
    const iosButton = document.getElementById('pwa-ios-button');
    const iosHint = document.getElementById('pwa-ios-hint');

    dismissButton?.addEventListener('click', () => {
        window.localStorage.setItem('pwa-install-dismissed', '1');
        hideInstallPrompt();
    });

    iosButton?.addEventListener('click', () => {
        iosHint?.classList.toggle('hidden');
        document.getElementById('pwa-install-unavailable')?.classList.add('hidden');
    });

    installButton?.addEventListener('click', promptInstall);
};

const activateWaitingServiceWorker = (registration) => {
    if (registration?.waiting) {
        registration.waiting.postMessage({ type: 'SKIP_WAITING' });
    }
};

const registerServiceWorker = () => {
    if (!('serviceWorker' in navigator)) {
        return;
    }

    navigator.serviceWorker.addEventListener('controllerchange', () => {
        if (hasReloadedForServiceWorker) {
            return;
        }

        hasReloadedForServiceWorker = true;
        window.location.reload();
    });

    window.addEventListener('load', async () => {
        try {
            const registration = await navigator.serviceWorker.register('/sw.js');

            if (registration.waiting) {
                activateWaitingServiceWorker(registration);
            }

            registration.addEventListener('updatefound', () => {
                const newWorker = registration.installing;

                if (!newWorker) {
                    return;
                }

                newWorker.addEventListener('statechange', () => {
                    if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                        activateWaitingServiceWorker(registration);
                    }
                });
            });

            setTimeout(() => {
                registration.update().catch(() => {});
            }, 3000);
        } catch (error) {
            console.error('Service worker registration failed:', error);
        }
    });
};

document.addEventListener('DOMContentLoaded', () => {
    registerGlobalInstallActions();
    registerInstallHandlers();
    showInstallPrompt();
    registerServiceWorker();
});

window.addEventListener('beforeinstallprompt', (event) => {
    event.preventDefault();
    deferredInstallPrompt = event;
    showInstallPrompt();
});

window.addEventListener('appinstalled', () => {
    deferredInstallPrompt = null;
    hideInstallPrompt();
    window.localStorage.removeItem('pwa-install-dismissed');
});
