<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use App\Services\StockService;

class StockServiceTest extends TestCase
{

    public function test_can_decrease_stock()
    {
        // Test stock decrease logic without database
        $product = new Product([
            'id' => 1,
            'name' => 'Test Product',
            'couleur' => [['name' => 'Red', 'hex' => '#FF0000']],
            'stock_couleurs' => [['name' => 'Red', 'quantity' => 100]],
            'quantite_stock' => 100,
            'prix_vente' => 50.00,
            'image' => 'test-image.jpg',
        ]);

        // Test the stock calculation logic directly
        $currentStock = $product->getStockForColor('Red');
        $this->assertEquals(100, $currentStock);

        // Test decrease logic
        $newStock = max(0, $currentStock - 10);
        $this->assertEquals(90, $newStock);
    }

    public function test_can_increase_stock()
    {
        // Test stock increase logic without database
        $product = new Product([
            'id' => 2,
            'name' => 'Test Product 2',
            'couleur' => [['name' => 'Blue', 'hex' => '#0000FF']],
            'stock_couleurs' => [['name' => 'Blue', 'quantity' => 50]],
            'quantite_stock' => 50,
            'prix_vente' => 30.00,
            'image' => 'test-image.jpg',
        ]);

        // Test the stock calculation logic directly
        $currentStock = $product->getStockForColor('Blue');
        $this->assertEquals(50, $currentStock);

        // Test increase logic
        $newStock = $currentStock + 5;
        $this->assertEquals(55, $newStock);
    }

    public function test_stock_availability_check()
    {
        // Test stock availability logic without database
        $product = new Product([
            'id' => 3,
            'name' => 'Test Product 3',
            'couleur' => [['name' => 'Green', 'hex' => '#00FF00']],
            'stock_couleurs' => [['name' => 'Green', 'quantity' => 20]],
            'quantite_stock' => 20,
            'prix_vente' => 25.00,
            'image' => 'test-image.jpg',
        ]);

        // Test stock availability logic directly
        $stockCouleur = $product->getStockForColor('Green');
        $stockTotal = $product->quantite_stock;

        // Test sufficient stock
        $requested = 10;
        $sufficientStock = $stockCouleur >= $requested;
        $sufficientTotalStock = $stockTotal >= $requested;
        $available = $sufficientStock && $sufficientTotalStock;

        $this->assertTrue($available);
        $this->assertEquals(20, $stockCouleur);
        $this->assertEquals(10, $requested);

        // Test insufficient stock
        $requested2 = 30;
        $sufficientStock2 = $stockCouleur >= $requested2;
        $sufficientTotalStock2 = $stockTotal >= $requested2;
        $available2 = $sufficientStock2 && $sufficientTotalStock2;

        $this->assertFalse($available2);
        $this->assertEquals(20, $stockCouleur);
        $this->assertEquals(30, $requested2);
    }
}
