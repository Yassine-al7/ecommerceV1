<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $sellerRole = Role::firstOrCreate(['name' => 'seller']);

        // Create admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role_id' => $adminRole->id,
        ]);

        // Create seller users
        User::create([
            'name' => 'Seller One',
            'email' => 'seller1@example.com',
            'password' => bcrypt('password'),
            'role_id' => $sellerRole->id,
        ]);

        User::create([
            'name' => 'Seller Two',
            'email' => 'seller2@example.com',
            'password' => bcrypt('password'),
            'role_id' => $sellerRole->id,
        ]);
    }
}
