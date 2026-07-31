<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Store Admin',
                'password' => Hash::make('password'),
                'is_admin' => true,
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        $vendors = [
            [
                'name' => 'Aisha Bello',
                'email' => 'aisha@demo-store.com',
                'store_name' => 'Aisha Gadgets Hub',
                'store_description' => 'Smartphones, accessories, and daily-use electronics selected for fast-moving local shoppers.',
                'phone' => '2348011111111',
                'address' => 'Plot 12, Aminu Kano Crescent, Wuse 2, Abuja',
                'store_whatsapp' => '2348011111111',
                'store_instagram' => 'aishagadgets',
                'store_facebook' => 'aishagadgets',
                'store_website' => 'aishagadgets.demo',
            ],
            [
                'name' => 'Chinedu Okafor',
                'email' => 'chinedu@demo-store.com',
                'store_name' => 'Urban Style Market',
                'store_description' => 'Fashion basics, sneakers, and modern everyday wear for students and young professionals.',
                'phone' => '2348022222222',
                'address' => '14 Relief Market Road, Onitsha, Anambra',
                'store_whatsapp' => '2348022222222',
                'store_instagram' => 'urbanstylemarket',
                'store_facebook' => 'urbanstylemarket',
                'store_website' => 'urbanstylemarket.demo',
            ],
            [
                'name' => 'Fatima Musa',
                'email' => 'fatima@demo-store.com',
                'store_name' => 'Home Harvest Plus',
                'store_description' => 'Home essentials, kitchen tools, beauty products, and practical items for family living.',
                'phone' => '2348033333333',
                'address' => '8 Emir Palace Road, Kano Municipal, Kano',
                'store_whatsapp' => '2348033333333',
                'store_instagram' => 'homeharvestplus',
                'store_facebook' => 'homeharvestplus',
                'store_website' => 'homeharvestplus.demo',
            ],
        ];

        foreach ($vendors as $vendor) {
            User::updateOrCreate(
                ['email' => $vendor['email']],
                [
                    'name' => $vendor['name'],
                    'password' => Hash::make('password'),
                    'is_admin' => false,
                    'role' => 'vendor',
                    'email_verified_at' => now(),
                    'store_name' => $vendor['store_name'],
                    'store_description' => $vendor['store_description'],
                    'phone' => $vendor['phone'],
                    'address' => $vendor['address'],
                    'store_whatsapp' => $vendor['store_whatsapp'],
                    'store_instagram' => $vendor['store_instagram'],
                    'store_facebook' => $vendor['store_facebook'],
                    'store_website' => $vendor['store_website'],
                    'verification_status' => 'approved',
                    'verified_at' => now(),
                    'vendor_is_active' => true,
                    'verification_email' => $vendor['email'],
                    'verification_phone' => $vendor['phone'],
                    'bank_name' => 'Demo Bank',
                    'bank_account_name' => $vendor['name'],
                    'bank_account_number' => '0123456789',
                    'bank_verification_status' => 'approved',
                    'bank_verified_at' => now(),
                ]
            );
        }

        $customers = [
            ['name' => 'John Doe', 'email' => 'john@example.com', 'phone' => '2348041111111', 'address' => '23 Admiralty Way, Lekki, Lagos'],
            ['name' => 'Jane Smith', 'email' => 'jane@example.com', 'phone' => '2348052222222', 'address' => '11 GRA Road, Port Harcourt, Rivers'],
            ['name' => 'Michael Johnson', 'email' => 'michael@example.com', 'phone' => '2348063333333', 'address' => '5 Airport Road, Benin City, Edo'],
            ['name' => 'Grace Williams', 'email' => 'grace@example.com', 'phone' => '2348074444444', 'address' => '42 Sabo Street, Kaduna North, Kaduna'],
            ['name' => 'David Emmanuel', 'email' => 'david@example.com', 'phone' => '2348085555555', 'address' => '16 Zaria Road, Jos, Plateau'],
        ];

        foreach ($customers as $customer) {
            User::updateOrCreate(
                ['email' => $customer['email']],
                [
                    'name' => $customer['name'],
                    'password' => Hash::make('password'),
                    'is_admin' => false,
                    'role' => 'customer',
                    'email_verified_at' => now(),
                    'phone' => $customer['phone'],
                    'address' => $customer['address'],
                ]
            );
        }
    }
}
