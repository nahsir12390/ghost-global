import './bootstrap';

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
