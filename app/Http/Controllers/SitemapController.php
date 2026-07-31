<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Response;

class SitemapController extends Controller
{
    /**
     * Generate XML sitemap for Google Search Console
     */
    public function index()
    {
        $xml = $this->generateSitemap();
        return Response::view('sitemap', compact('xml'), 200)->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    /**
     * Generate sitemap XML content
     */
    private function generateSitemap(): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

        // Add homepage with highest priority
        $xml .= $this->getUrl(route('home'), now()->toAtomString(), 1.0, 'daily');

        // Add shop page
        $xml .= $this->getUrl(route('shop'), now()->toAtomString(), 0.9, 'daily');

        // Add about page
        $xml .= $this->getUrl(route('about'), now()->toAtomString(), 0.8, 'monthly');

        // Add contact page
        $xml .= $this->getUrl(route('contact'), now()->toAtomString(), 0.8, 'monthly');

        // Add privacy page
        $xml .= $this->getUrl(route('privacy'), now()->toAtomString(), 0.7, 'yearly');

        // Add terms page
        $xml .= $this->getUrl(route('terms'), now()->toAtomString(), 0.7, 'yearly');

        // Add all active products
        $products = Product::where('is_active', true)
            ->orderBy('updated_at', 'desc')
            ->get();

        foreach ($products as $product) {
            $xml .= $this->getUrl(
                route('product.show', $product->slug),
                $product->updated_at->toAtomString(),
                0.8,
                'weekly'
            );
        }

        // Add all active categories
        $categories = Category::where('is_active', true)
            ->orderBy('updated_at', 'desc')
            ->get();

        foreach ($categories as $category) {
            $xml .= $this->getUrl(
                route('category.show', $category->slug),
                $category->updated_at->toAtomString(),
                0.7,
                'weekly'
            );
        }

        $xml .= '</urlset>' . PHP_EOL;

        return $xml;
    }

    /**
     * Get single URL entry for sitemap
     */
    private function getUrl(string $loc, string $lastmod, float $priority, string $changefreq): string
    {
        return "    <url>\n"
            . "        <loc>" . htmlspecialchars($loc, ENT_XML1) . "</loc>\n"
            . "        <lastmod>" . $lastmod . "</lastmod>\n"
            . "        <changefreq>" . $changefreq . "</changefreq>\n"
            . "        <priority>" . $priority . "</priority>\n"
            . "    </url>\n";
    }
}
