<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use App\Services\StockService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StockServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_decrease_stock()
    {
        // Create a test category first
        $category = \App\Models\Category::create([
            'name' => 'Test Category',
            'description' => 'Test category for testing',
            'is_active' => true,
        ]);

        // Create a test product
        $product = Product::create([
            'name' => 'Test Product',
            'couleur' => [['name' => 'Red', 'hex' => '#FF0000']],
            'quantite_stock' => 100,
            'prix_vente' => 50.00,
            'categorie_id' => $category->id,
        ]);

        // Test stock decrease
        $result = StockService::decreaseStock($product->id, 'Red', 10);

        $this->assertTrue($result);

        // Refresh product from database
        $product->refresh();

        // Check that stock was decreased
        $this->assertEquals(90, $product->quantite_stock);
    }

    public function test_can_increase_stock()
    {
        // Create a test category first
        $category = \App\Models\Category::create([
            'name' => 'Test Category 2',
            'description' => 'Test category for testing',
            'is_active' => true,
        ]);

        // Create a test product
        $product = Product::create([
            'name' => 'Test Product 2',
            'couleur' => [['name' => 'Blue', 'hex' => '#0000FF']],
            'quantite_stock' => 50,
            'prix_vente' => 30.00,
            'categorie_id' => $category->id,
        ]);

        // Test stock increase
        $result = StockService::increaseStock($product->id, 'Blue', 5);

        $this->assertTrue($result);

        // Refresh product from database
        $product->refresh();

        // Check that stock was increased
        $this->assertEquals(55, $product->quantite_stock);
    }

    public function test_stock_availability_check()
    {
        // Create a test category first
        $category = \App\Models\Category::create([
            'name' => 'Test Category 3',
            'description' => 'Test category for testing',
            'is_active' => true,
        ]);

        // Create a test product
        $product = Product::create([
            'name' => 'Test Product 3',
            'couleur' => [['name' => 'Green', 'hex' => '#00FF00']],
            'quantite_stock' => 20,
            'prix_vente' => 25.00,
            'categorie_id' => $category->id,
        ]);

        // Test stock availability
        $availability = StockService::checkStockAvailability($product->id, 'Green', 10);

        $this->assertTrue($availability['available']);
        $this->assertEquals(20, $availability['stock_couleur']);
        $this->assertEquals(10, $availability['requested']);

        // Test insufficient stock
        $availability2 = StockService::checkStockAvailability($product->id, 'Green', 30);

        $this->assertFalse($availability2['available']);
        $this->assertEquals(20, $availability2['stock_couleur']);
        $this->assertEquals(30, $availability2['requested']);
    }
}
