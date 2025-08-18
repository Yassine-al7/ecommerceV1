<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supplier;

class SupplierSeeder extends Seeder
{
    public function run()
    {
        Supplier::create([
            'name' => 'Supplier One',
            'contact_person' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '1234567890',
            'address' => '123 Main St, City, Country',
        ]);

        Supplier::create([
            'name' => 'Supplier Two',
            'contact_person' => 'Jane Smith',
            'email' => 'jane@example.com',
            'phone' => '0987654321',
            'address' => '456 Elm St, City, Country',
        ]);

        Supplier::create([
            'name' => 'Supplier Three',
            'contact_person' => 'Alice Johnson',
            'email' => 'alice@example.com',
            'phone' => '1122334455',
            'address' => '789 Oak St, City, Country',
        ]);
    }
}
