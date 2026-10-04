<?php

namespace App\Http\Controllers;

use App\Helpers\SettingsHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response as ResponseFactory;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class PwaController extends Controller
{
    public function manifest(): Response
    {
        $siteName = SettingsHelper::siteName();
        $siteDescription = SettingsHelper::siteDescription();

        return response()->json([
            'id' => '/',
            'name' => $siteName,
            'short_name' => mb_strimwidth($siteName, 0, 12, ''),
            'description' => $siteDescription,
            'start_url' => '/?source=pwa',
            'scope' => '/',
            'display' => 'standalone',
            'display_override' => ['window-controls-overlay', 'standalone', 'minimal-ui'],
            'background_color' => '#090909',
            'theme_color' => '#090909',
            'orientation' => 'any',
            'lang' => str_replace('_', '-', app()->getLocale()),
            'dir' => 'ltr',
            'categories' => ['shopping', 'business', 'lifestyle'],
            'prefer_related_applications' => false,
            'shortcuts' => [
                [
                    'name' => 'Shop products',
                    'short_name' => 'Shop',
                    'description' => 'Browse products available from '.$siteName.'.',
                    'url' => route('shop', absolute: false),
                    'icons' => [[
                        'src' => route('pwa.icon', ['size' => 192]),
                        'sizes' => '192x192',
                        'type' => 'image/png',
                    ]],
                ],
                [
                    'name' => 'Shopping cart',
                    'short_name' => 'Cart',
                    'description' => 'Review items in your cart.',
                    'url' => route('cart', absolute: false),
                    'icons' => [[
                        'src' => route('pwa.icon', ['size' => 192]),
                        'sizes' => '192x192',
                        'type' => 'image/png',
                    ]],
                ],
                [
                    'name' => 'Track an order',
                    'short_name' => 'Track',
                    'description' => 'Check the latest status of an order.',
                    'url' => route('tracking.index', absolute: false),
                    'icons' => [[
                        'src' => route('pwa.icon', ['size' => 192]),
                        'sizes' => '192x192',
                        'type' => 'image/png',
                    ]],
                ],
            ],
            'icons' => [
                ['src' => route('pwa.icon', ['size' => 192]), 'sizes' => '192x192', 'type' => 'image/png', 'purpose' => 'any'],
                ['src' => route('pwa.icon', ['size' => 512]), 'sizes' => '512x512', 'type' => 'image/png', 'purpose' => 'any'],
                ['src' => route('pwa.icon', ['size' => 512, 'variant' => 'maskable']), 'sizes' => '512x512', 'type' => 'image/png', 'purpose' => 'maskable'],
            ],
        ], 200, ['Content-Type' => 'application/manifest+json', 'Cache-Control' => 'no-cache, must-revalidate']);
    }

    public function serviceWorker(Request $request): Response
    {
        $version = implode('-', [config('app.name'), filemtime(public_path('favicon.ico')), filemtime(resource_path('views/pwa/service-worker.blade.php')), filemtime(resource_path('js/app.js'))]);
        $siteName = SettingsHelper::siteName();
        $cachePrefix = Str::slug($siteName ?: 'storefront');

        $script = view('pwa.service-worker', [
            'cacheName' => $cachePrefix.'-'.md5($version),
            'offlineUrl' => url('/offline.html'),
            'manifestUrl' => route('pwa.manifest'),
            'icon192Url' => route('pwa.icon', ['size' => 192]),
        ])->render();

        return response($script, 200, ['Content-Type' => 'application/javascript; charset=UTF-8', 'Cache-Control' => 'no-cache, no-store, must-revalidate', 'Service-Worker-Allowed' => '/']);
    }

    public function icon(Request $request, int $size): Response
    {
        $size = in_array($size, [192, 512], true) ? $size : 192;
        $variant = $request->string('variant')->toString() === 'maskable' ? 'maskable' : 'any';
        $siteName = SettingsHelper::siteName();
        $logoPath = $this->localLogoPath();

        if (function_exists('imagecreatetruecolor')) {
            $icon = $this->buildDynamicPngIcon($siteName, $size, $variant, $logoPath);
            if ($icon !== null) return response($icon, 200, ['Content-Type' => 'image/png', 'Cache-Control' => 'no-cache, no-store, must-revalidate']);
        }

        return ResponseFactory::make($this->buildFallbackSvgIcon($siteName, $size), 200, ['Content-Type' => 'image/svg+xml', 'Cache-Control' => 'no-cache, no-store, must-revalidate']);
    }

    protected function localLogoPath(): string
    {
        $configuredLogo = trim((string) SettingsHelper::get('site_logo', ''));
        if ($configuredLogo !== '' && ! Str::startsWith($configuredLogo, ['http://', 'https://'])) {
            $candidate = public_path(ltrim($configuredLogo, '/'));
            if (is_file($candidate)) return $candidate;
        }
        return public_path('storage/logo.png');
    }

    protected function buildDynamicPngIcon(string $siteName, int $size, string $variant, string $logoPath): ?string
    {
        $canvas = imagecreatetruecolor($size, $size);
        if (! $canvas) return null;
        imagealphablending($canvas, true); imagesavealpha($canvas, true);
        $background = imagecolorallocate($canvas, 15, 23, 42); imagefill($canvas, 0, 0, $background);
        $red = imagecolorallocate($canvas, 220, 38, 38); $rose = imagecolorallocate($canvas, 244, 63, 94); $white = imagecolorallocate($canvas, 255, 255, 255); $softWhite = imagecolorallocatealpha($canvas, 255, 255, 255, 70);
        for ($y = 0; $y < $size; $y++) { $blend = $size > 1 ? $y / ($size - 1) : 0; $r=(int)round(15+((220-15)*$blend)); $g=(int)round(23+((38-23)*$blend)); $b=(int)round(42+((38-42)*$blend)); imageline($canvas,0,$y,$size,$y,imagecolorallocate($canvas,$r,$g,$b)); }
        imagefilledellipse($canvas,(int)round($size*.8),(int)round($size*.18),(int)round($size*.55),(int)round($size*.55),imagecolorallocatealpha($canvas,244,63,94,95));
        imagefilledellipse($canvas,(int)round($size*.22),(int)round($size*.85),(int)round($size*.72),(int)round($size*.72),imagecolorallocatealpha($canvas,220,38,38,102));
        $innerPadding=(int)round($size*($variant==='maskable'?.18:.12)); $innerSize=$size-($innerPadding*2); $panelColor=imagecolorallocatealpha($canvas,255,255,255,8);
        imagefilledrectangle($canvas,$innerPadding,$innerPadding,$innerPadding+$innerSize,$innerPadding+$innerSize,$panelColor);
        $logoRendered=false;
        if (is_file($logoPath)) {
            $logoType=@exif_imagetype($logoPath); $source=match($logoType){IMAGETYPE_PNG=>@imagecreatefrompng($logoPath),IMAGETYPE_JPEG=>@imagecreatefromjpeg($logoPath),IMAGETYPE_WEBP=>function_exists('imagecreatefromwebp')?@imagecreatefromwebp($logoPath):false,default=>false};
            if ($source!==false) { $targetSize=(int)round($innerSize*.58); $destinationX=(int)round(($size-$targetSize)/2); $destinationY=(int)round(($size-$targetSize)/2); imagealphablending($source,true); imagesavealpha($source,true); imagecopyresampled($canvas,$source,$destinationX,$destinationY,0,0,$targetSize,$targetSize,imagesx($source),imagesy($source)); imagedestroy($source); $logoRendered=true; }
        }
        if (!$logoRendered) {
            $initials=$this->siteInitials($siteName); $font=5; $textWidth=imagefontwidth($font)*strlen($initials); $textHeight=imagefontheight($font); $scale=max(1,(int)floor($size/96)); $x=(int)round(($size-($textWidth*$scale))/2); $y=(int)round(($size-($textHeight*$scale))/2);
            for($i=0;$i<$scale;$i++) for($j=0;$j<$scale;$j++) imagestring($canvas,$font,$x+$i,$y+$j,$initials,$white);
            $label=strtoupper(mb_substr(trim($siteName),0,18)); $labelFont=2; $labelWidth=imagefontwidth($labelFont)*strlen($label); imagestring($canvas,$labelFont,max(0,(int)round(($size-$labelWidth)/2)),(int)round($size*.77),$label,$softWhite);
        }
        imagefilledellipse($canvas,(int)round($size*.22),(int)round($size*.22),(int)round($size*.08),(int)round($size*.08),$rose); imagefilledellipse($canvas,(int)round($size*.77),(int)round($size*.77),(int)round($size*.06),(int)round($size*.06),$red);
        ob_start(); imagepng($canvas); $binary=ob_get_clean(); imagedestroy($canvas); return $binary?:null;
    }

    protected function buildFallbackSvgIcon(string $siteName, int $size): string
    {
        $initials=e($this->siteInitials($siteName)); $label=e(Str::upper(Str::limit(trim($siteName),18,''))); $fontSize=$size>=512?156:62; $labelSize=$size>=512?28:14;
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="{$size}" height="{$size}" viewBox="0 0 {$size} {$size}" fill="none"><defs><linearGradient id="bg" x1="0" y1="0" x2="1" y2="1"><stop offset="0%" stop-color="#0f172a"/><stop offset="100%" stop-color="#dc2626"/></linearGradient></defs><rect width="{$size}" height="{$size}" rx="{$size}" fill="url(#bg)"/><circle cx="{$size}" cy="0" r="{$size}" fill="#f43f5e" opacity="0.12"/><circle cx="0" cy="{$size}" r="{$size}" fill="#ef4444" opacity="0.12"/><text x="50%" y="48%" text-anchor="middle" dominant-baseline="middle" fill="#ffffff" font-family="Arial, sans-serif" font-size="{$fontSize}" font-weight="700">{$initials}</text><text x="50%" y="78%" text-anchor="middle" fill="rgba(255,255,255,0.72)" font-family="Arial, sans-serif" font-size="{$labelSize}" font-weight="600" letter-spacing="2">{$label}</text></svg>
SVG;
    }

    protected function siteInitials(string $siteName): string
    {
        $words=preg_split('/[\s\-_]+/',trim($siteName))?:[]; $words=array_values(array_filter($words));
        if(count($words)>=2) return Str::upper(mb_substr($words[0],0,1).mb_substr($words[1],0,1));
        $normalized=preg_replace('/[^A-Za-z0-9]/','',$siteName)?:'S'; return Str::upper(mb_substr($normalized,0,2));
    }
}
