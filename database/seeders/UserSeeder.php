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
