<?php

use App\Helpers\SettingsHelper;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('storefront branding and currency follow deployment settings', function () {
    SettingsHelper::set('site_name', 'Northstar Market');
    SettingsHelper::set('site_tagline', 'Everything closer to you.');
    SettingsHelper::set('site_description', 'A marketplace configured for a new region.');
    SettingsHelper::set('site_currency', 'USD');
    SettingsHelper::set('site_currency_symbol', '$');

    expect(SettingsHelper::siteName())->toBe('Northstar Market')
        ->and(SettingsHelper::siteTagline())->toBe('Everything closer to you.')
        ->and(SettingsHelper::currency(1250))->toBe('$1,250.00')
        ->and(SettingsHelper::browserStorageKey('followed-stores'))->toBe('northstar-market-followed-stores');

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Northstar Market')
        ->assertSee('Everything closer to you.');

    $this->get(route('pwa.manifest'))
        ->assertOk()
        ->assertJsonPath('name', 'Northstar Market')
        ->assertJsonPath('description', 'A marketplace configured for a new region.');
});

test('an administrator can upload application branding', function () {
    Storage::fake('public');
    $admin = User::factory()->create(['role' => 'admin', 'is_admin' => true]);

    $this->actingAs($admin)->put(route('admin.settings.update'), [
        'site_name' => 'Northstar Market',
        'site_logo_upload' => UploadedFile::fake()->image('brand.png', 512, 512),
        'site_favicon_upload' => UploadedFile::fake()->image('icon.png', 128, 128),
        'platform_fee_tier_1_max' => 10000,
        'platform_fee_tier_1_rate' => 5,
        'platform_fee_tier_2_max' => 50000,
        'platform_fee_tier_2_rate' => 4,
        'platform_fee_tier_3_max' => 200000,
        'platform_fee_tier_3_rate' => 3,
        'platform_fee_tier_4_rate' => 2,
    ])->assertRedirect();

    SettingsHelper::clearCache();
    $logoPath = (string) SettingsHelper::get('site_logo');
    $faviconPath = (string) SettingsHelper::get('site_favicon');

    expect($logoPath)->toStartWith('storage/branding/')
        ->and($faviconPath)->toStartWith('storage/branding/');

    Storage::disk('public')->assertExists(str_replace('storage/', '', $logoPath));
    Storage::disk('public')->assertExists(str_replace('storage/', '', $faviconPath));
});
