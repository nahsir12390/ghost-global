<?php

test('the web app manifest exposes install metadata and shortcuts', function () {
    $response = $this->get(route('pwa.manifest'));

    $response
        ->assertOk()
        ->assertHeader('Content-Type', 'application/manifest+json')
        ->assertJsonPath('id', '/')
        ->assertJsonPath('display', 'standalone')
        ->assertJsonCount(3, 'shortcuts')
        ->assertJsonPath('shortcuts.0.url', route('shop', absolute: false));
});

test('the service worker uses safe offline navigation and asset revalidation', function () {
    $this->get(route('pwa.service-worker'))
        ->assertOk()
        ->assertHeader('Service-Worker-Allowed', '/')
        ->assertSee('fetchWithTimeout', false)
        ->assertSee('cache.put(request, copy)', false);
});

test('the offline fallback provides a retry experience', function () {
    $offlinePage = file_get_contents(public_path('offline.html'));

    expect($offlinePage)
        ->toContain('You’re')
        ->toContain('id="retry"');
});
