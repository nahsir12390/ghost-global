<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Electronics',
                'slug' => 'electronics',
                'description' => 'Phones, accessories, audio gear, computing devices, and everyday tech.',
                'image' => 'https://picsum.photos/seed/electronics-category/800/800',
            ],
            [
                'name' => 'Fashion',
                'slug' => 'fashion',
                'description' => 'Clothing, footwear, bags, and wearable basics for daily life.',
                'image' => 'https://picsum.photos/seed/fashion-category/800/800',
            ],
            [
                'name' => 'Home & Kitchen',
                'slug' => 'home-kitchen',
                'description' => 'Kitchen tools, home improvement items, and practical household products.',
                'image' => 'https://picsum.photos/seed/home-kitchen-category/800/800',
            ],
            [
                'name' => 'Beauty & Personal Care',
                'slug' => 'beauty-personal-care',
                'description' => 'Personal care, skincare, grooming tools, and beauty essentials.',
                'image' => 'https://picsum.photos/seed/beauty-category/800/800',
            ],
            [
                'name' => 'Books & Learning',
                'slug' => 'books-learning',
                'description' => 'Printed books, eBooks, practical guides, and educational learning tools.',
                'image' => 'https://picsum.photos/seed/books-category/800/800',
            ],
        ];

        foreach ($categories as $categoryData) {
            Category::updateOrCreate(
                ['slug' => $categoryData['slug']],
                array_merge($categoryData, ['is_active' => true])
            );
        }

        Product::query()->delete();

        $categoryMap = Category::query()->get()->keyBy('slug');
        $vendors = User::query()->where('role', 'vendor')->get()->keyBy('email');

        $products = [
            [
                'vendor_email' => 'aisha@demo-store.com',
                'category_slug' => 'electronics',
                'name' => 'iPhone 15 Pro 256GB',
                'description' => 'Premium Apple smartphone with strong battery life, smooth camera performance, and enough storage for work and media.',
                'price' => 1425000,
                'compare_price' => 1499000,
                'quantity' => 12,
                'is_featured' => true,
                'images' => ['https://picsum.photos/seed/iphone-15-pro/900/900'],
            ],
            [
                'vendor_email' => 'aisha@demo-store.com',
                'category_slug' => 'electronics',
                'name' => 'Samsung Galaxy S24 256GB',
                'description' => 'Fast Android flagship built for photos, social media, video, and everyday multitasking.',
                'price' => 1049000,
                'compare_price' => 1120000,
                'quantity' => 15,
                'is_featured' => true,
                'images' => ['https://picsum.photos/seed/galaxy-s24/900/900'],
            ],
            [
                'vendor_email' => 'aisha@demo-store.com',
                'category_slug' => 'electronics',
                'name' => 'Infinix Note 40 Pro',
                'description' => 'Mid-range smartphone with strong battery life, clean display, and good value for everyday users.',
                'price' => 385000,
                'compare_price' => 420000,
                'quantity' => 24,
                'is_featured' => false,
                'images' => ['https://picsum.photos/seed/infinix-note-40/900/900'],
            ],
            [
                'vendor_email' => 'aisha@demo-store.com',
                'category_slug' => 'electronics',
                'name' => 'Oraimo FreePods Pro',
                'description' => 'Wireless earbuds with clean audio, portable charging case, and easy everyday Bluetooth pairing.',
                'price' => 32000,
                'compare_price' => 38000,
                'quantity' => 50,
                'is_featured' => false,
                'images' => ['https://picsum.photos/seed/oraimo-freepods/900/900'],
            ],
            [
                'vendor_email' => 'aisha@demo-store.com',
                'category_slug' => 'electronics',
                'name' => 'HP Envy 14 Laptop',
                'description' => 'Portable laptop for office work, browsing, design basics, presentations, and online classes.',
                'price' => 860000,
                'compare_price' => 925000,
                'quantity' => 8,
                'is_featured' => true,
                'images' => ['https://picsum.photos/seed/hp-envy-14/900/900'],
            ],
            [
                'vendor_email' => 'chinedu@demo-store.com',
                'category_slug' => 'fashion',
                'name' => 'Nike Air Max 270',
                'description' => 'Popular sneaker with comfortable cushioning for walking, commuting, and casual daily wear.',
                'price' => 45000,
                'compare_price' => 52000,
                'quantity' => 30,
                'is_featured' => true,
                'images' => ['https://picsum.photos/seed/nike-air-max-270/900/900'],
            ],
            [
                'vendor_email' => 'chinedu@demo-store.com',
                'category_slug' => 'fashion',
                'name' => 'Levi 501 Straight Jeans',
                'description' => 'Classic denim jeans with a simple fit that pairs easily with tees, polos, and sneakers.',
                'price' => 28500,
                'compare_price' => 34000,
                'quantity' => 40,
                'is_featured' => false,
                'images' => ['https://picsum.photos/seed/levis-501/900/900'],
            ],
            [
                'vendor_email' => 'chinedu@demo-store.com',
                'category_slug' => 'fashion',
                'name' => 'Unisex Canvas Backpack',
                'description' => 'Lightweight backpack for school, casual outings, and carrying small daily essentials.',
                'price' => 18000,
                'compare_price' => 22000,
                'quantity' => 35,
                'is_featured' => false,
                'images' => ['https://picsum.photos/seed/canvas-backpack/900/900'],
            ],
            [
                'vendor_email' => 'chinedu@demo-store.com',
                'category_slug' => 'fashion',
                'name' => 'Men Basic Polo Shirt',
                'description' => 'Comfortable short-sleeve polo shirt that works well for office-casual and weekend wear.',
                'price' => 9500,
                'compare_price' => 12000,
                'quantity' => 60,
                'is_featured' => false,
                'images' => ['https://picsum.photos/seed/mens-polo/900/900'],
            ],
            [
                'vendor_email' => 'chinedu@demo-store.com',
                'category_slug' => 'fashion',
                'name' => 'Women Flat Leather Sandals',
                'description' => 'Simple open sandals designed for comfort, movement, and warm-weather daily use.',
                'price' => 13500,
                'compare_price' => 16000,
                'quantity' => 28,
                'is_featured' => true,
                'images' => ['https://picsum.photos/seed/women-sandals/900/900'],
            ],
            [
                'vendor_email' => 'fatima@demo-store.com',
                'category_slug' => 'home-kitchen',
                'name' => 'Stainless Electric Kettle 2L',
                'description' => 'Fast-boiling electric kettle suitable for tea, coffee, noodles, and light kitchen use.',
                'price' => 16500,
                'compare_price' => 21000,
                'quantity' => 25,
                'is_featured' => true,
                'images' => ['https://picsum.photos/seed/electric-kettle/900/900'],
            ],
            [
                'vendor_email' => 'fatima@demo-store.com',
                'category_slug' => 'home-kitchen',
                'name' => 'Non-Stick Cookware Set',
                'description' => 'Kitchen cookware set for daily cooking with easy cleaning and practical family use.',
                'price' => 59000,
                'compare_price' => 68000,
                'quantity' => 14,
                'is_featured' => true,
                'images' => ['https://picsum.photos/seed/cookware-set/900/900'],
            ],
            [
                'vendor_email' => 'fatima@demo-store.com',
                'category_slug' => 'home-kitchen',
                'name' => 'Wall Mounted Spice Rack',
                'description' => 'Compact spice rack to keep your kitchen more organized and easier to use every day.',
                'price' => 8500,
                'compare_price' => 10500,
                'quantity' => 32,
                'is_featured' => false,
                'images' => ['https://picsum.photos/seed/spice-rack/900/900'],
            ],
            [
                'vendor_email' => 'fatima@demo-store.com',
                'category_slug' => 'beauty-personal-care',
                'name' => 'Vitamin C Face Serum',
                'description' => 'Light facial serum designed to support brighter-looking skin in a simple daily routine.',
                'price' => 12000,
                'compare_price' => 14500,
                'quantity' => 44,
                'is_featured' => false,
                'images' => ['https://picsum.photos/seed/vitamin-c-serum/900/900'],
            ],
            [
                'vendor_email' => 'fatima@demo-store.com',
                'category_slug' => 'beauty-personal-care',
                'name' => 'Rechargeable Hair Clipper',
                'description' => 'Cordless clipper for clean home grooming with easy charging and simple maintenance.',
                'price' => 27000,
                'compare_price' => 32000,
                'quantity' => 18,
                'is_featured' => false,
                'images' => ['https://picsum.photos/seed/hair-clipper/900/900'],
            ],
            [
                'vendor_email' => 'aisha@demo-store.com',
                'category_slug' => 'books-learning',
                'name' => 'Laravel for Business Projects',
                'description' => 'Practical course for building real Laravel applications with auth, products, orders, and admin flows.',
                'product_type' => Product::TYPE_COURSE,
                'price' => 25000,
                'compare_price' => 35000,
                'quantity' => 0,
                'is_featured' => true,
                'images' => ['https://picsum.photos/seed/laravel-course/900/900'],
                'course_access_url' => 'https://example.com/courses/laravel-business-projects',
                'access_instructions' => 'After payment, open your dashboard and go to My Courses to start learning.',
            ],
            [
                'vendor_email' => 'aisha@demo-store.com',
                'category_slug' => 'books-learning',
                'name' => 'Social Media Sales Starter eBook',
                'description' => 'Short digital guide that explains how to write product posts, price clearly, and sell faster online.',
                'product_type' => Product::TYPE_DIGITAL,
                'price' => 6500,
                'compare_price' => 9000,
                'quantity' => 0,
                'is_featured' => false,
                'images' => ['https://picsum.photos/seed/social-sales-ebook/900/900'],
                'download_link' => 'https://example.com/downloads/social-media-sales-starter',
            ],
            [
                'vendor_email' => 'chinedu@demo-store.com',
                'category_slug' => 'books-learning',
                'name' => 'Mini Course: Product Photography with Your Phone',
                'description' => 'Beginner-friendly course that teaches how to take cleaner product photos with simple lighting and phone settings.',
                'product_type' => Product::TYPE_COURSE,
                'price' => 18000,
                'compare_price' => 24000,
                'quantity' => 0,
                'is_featured' => false,
                'images' => ['https://picsum.photos/seed/product-photo-course/900/900'],
                'course_access_url' => 'https://example.com/courses/product-photography-phone',
                'access_instructions' => 'Course access appears under My Courses immediately after payment confirmation.',
            ],
            [
                'vendor_email' => 'fatima@demo-store.com',
                'category_slug' => 'books-learning',
                'name' => 'Kitchen Budget Planner Template',
                'description' => 'Digital planning template for food shopping, monthly kitchen budgeting, and home stock tracking.',
                'product_type' => Product::TYPE_DIGITAL,
                'price' => 3500,
                'compare_price' => 5000,
                'quantity' => 0,
                'is_featured' => false,
                'images' => ['https://picsum.photos/seed/kitchen-budget-template/900/900'],
                'download_link' => 'https://example.com/downloads/kitchen-budget-planner',
            ],
        ];

        foreach ($products as $index => $productData) {
            $vendor = $vendors->get($productData['vendor_email']);
            $category = $categoryMap->get($productData['category_slug']);

            if (! $vendor || ! $category) {
                continue;
            }

            $name = $productData['name'];

            Product::create([
                'vendor_id' => $vendor->id,
                'category_id' => $category->id,
                'name' => $name,
                'slug' => Str::slug($name),
                'description' => $productData['description'],
                'product_type' => $productData['product_type'] ?? Product::TYPE_PHYSICAL,
                'price' => $productData['price'],
                'compare_price' => $productData['compare_price'] ?? null,
                'quantity' => $productData['quantity'] ?? 0,
                'sku' => 'DEMO-' . strtoupper(Str::padLeft((string) ($index + 1), 4, '0')),
                'images' => $productData['images'],
                'download_link' => $productData['download_link'] ?? null,
                'course_access_url' => $productData['course_access_url'] ?? null,
                'access_instructions' => $productData['access_instructions'] ?? null,
                'is_featured' => $productData['is_featured'] ?? false,
                'is_active' => true,
            ]);
        }
    }
}
