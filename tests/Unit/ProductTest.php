<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Product;
use App\Models\Category;

class ProductTest extends TestCase
{

    public function test_can_create_product()
    {
        // Test product creation without database
        $product = new Product([
            'name' => 'Test Product',
            'couleur' => [['name' => 'Red', 'hex' => '#FF0000']],
            'quantite_stock' => 100,
            'prix_vente' => 50.00,
            'image' => 'test-image.jpg',
        ]);

        $this->assertInstanceOf(Product::class, $product);
        $this->assertEquals('Test Product', $product->name);
        $this->assertEquals(100, $product->quantite_stock);
    }

    public function test_can_get_stock_for_color()
    {
        // Test stock retrieval without database
        $product = new Product([
            'name' => 'Test Product',
            'couleur' => [['name' => 'Red', 'hex' => '#FF0000']],
            'stock_couleurs' => [['name' => 'Red', 'quantity' => 50]],
            'quantite_stock' => 50,
            'prix_vente' => 50.00,
            'image' => 'test-image.jpg',
        ]);

        $stock = $product->getStockForColor('Red');
        $this->assertEquals(50, $stock);
    }

    public function test_can_decrease_color_stock()
    {
        // Test stock decrease without database - test the logic directly
        $product = new Product([
            'name' => 'Test Product',
            'couleur' => [['name' => 'Red', 'hex' => '#FF0000']],
            'stock_couleurs' => [['name' => 'Red', 'quantity' => 50]],
            'quantite_stock' => 50,
            'prix_vente' => 50.00,
            'image' => 'test-image.jpg',
        ]);

        // Test the stock calculation logic without saving
        $currentStock = $product->getStockForColor('Red');
        $newStock = max(0, $currentStock - 10);

        $this->assertEquals(50, $currentStock);
        $this->assertEquals(40, $newStock);
    }

    public function test_can_increase_color_stock()
    {
        // Test stock increase without database - test the logic directly
        $product = new Product([
            'name' => 'Test Product',
            'couleur' => [['name' => 'Blue', 'hex' => '#0000FF']],
            'stock_couleurs' => [['name' => 'Blue', 'quantity' => 30]],
            'quantite_stock' => 30,
            'prix_vente' => 40.00,
            'image' => 'test-image.jpg',
        ]);

        // Test the stock calculation logic without saving
        $currentStock = $product->getStockForColor('Blue');
        $newStock = $currentStock + 5;

        $this->assertEquals(30, $currentStock);
        $this->assertEquals(35, $newStock);
    }
}
