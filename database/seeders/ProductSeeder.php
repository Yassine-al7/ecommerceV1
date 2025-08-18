<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run()
    {
        Product::create([
            'name' => 'Product 1',
            'description' => 'Description for Product 1',
            'price' => 100.00,
            'supplier_id' => 1,
            'stock' => 50,
            'color' => 'Red',
            'size' => 'M',
        ]);

        Product::create([
            'name' => 'Product 2',
            'description' => 'Description for Product 2',
            'price' => 150.00,
            'supplier_id' => 1,
            'stock' => 30,
            'color' => 'Blue',
            'size' => 'L',
        ]);

        Product::create([
            'name' => 'Product 3',
            'description' => 'Description for Product 3',
            'price' => 200.00,
            'supplier_id' => 2,
            'stock' => 20,
            'color' => 'Green',
            'size' => 'S',
        ]);
    }
}
