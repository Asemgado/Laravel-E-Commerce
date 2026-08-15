<?php

namespace Database\Seeders;

use App\Enums\UserRolesEnum;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin1@example.com',
            'password' => Hash::make('password'),
            'role' => UserRolesEnum::ADMIN,
            'email_verified_at' => now(),
        ]);

        // Salesman
        $salesman = User::factory()->create([
            'name' => 'Salesman User',
            'email' => 'salesman@example.com',
            'password' => Hash::make('password'),
            'role' => UserRolesEnum::SALESMAN,
            'email_verified_at' => now(),
        ]);

        // Customer
        $customer = User::factory()->create([
            'name' => 'Customer User',
            'email' => 'customer@example.com',
            'password' => Hash::make('password'),
            'role' => UserRolesEnum::CUSTOMER,
            'email_verified_at' => now(),
        ]);

        // Sample products
        Product::create([
            'name' => 'Laptop',
            'description' => 'Gaming laptop with 16GB RAM',
            'price' => 1200.00,
            'stock_quantity' => 10,
            'user_id' => $salesman->id,
        ]);

        Product::create([
            'name' => 'Smartphone',
            'description' => 'Latest Android smartphone',
            'price' => 799.99,
            'stock_quantity' => 25,
            'user_id' => $salesman->id,
        ]);

        Product::create([
            'name' => 'Wireless Headphones',
            'description' => 'Noise-canceling over-ear headphones',
            'price' => 249.50,
            'stock_quantity' => 18,
            'user_id' => $admin->id,
        ]);

        Product::create([
            'name' => 'Orange Juice',
            'description' => 'Fresh orange juice bottle',
            'price' => 7.00,
            'stock_quantity' => 50,
            'user_id' => $salesman->id,
        ]);

        Product::create([
            'name' => 'Coffee Beans',
            'description' => 'Premium roasted coffee beans',
            'price' => 15.75,
            'stock_quantity' => 30,
            'user_id' => $salesman->id,
        ]);

        $this->command->info('Seeded admin, salesman, customer, and sample products.');
    }
}
