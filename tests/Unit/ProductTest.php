<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_product()
    {
        $product = Product::create([
            'name' => 'Test Product',
            'couleur' => [['name' => 'Red', 'hex' => '#FF0000']],
            'quantite_stock' => 100,
            'prix_vente' => 50.00,
            'categorie_id' => 1,
        ]);

        $this->assertInstanceOf(Product::class, $product);
        $this->assertEquals('Test Product', $product->name);
        $this->assertEquals(100, $product->quantite_stock);
    }

    public function test_can_get_stock_for_color()
    {
        $product = Product::create([
            'name' => 'Test Product',
            'couleur' => [['name' => 'Red', 'hex' => '#FF0000']],
            'stock_couleurs' => [['name' => 'Red', 'quantity' => 50]],
            'quantite_stock' => 50,
            'prix_vente' => 50.00,
            'categorie_id' => 1,
        ]);

        $stock = $product->getStockForColor('Red');
        $this->assertEquals(50, $stock);
    }

    public function test_can_decrease_color_stock()
    {
        $product = Product::create([
            'name' => 'Test Product',
            'couleur' => [['name' => 'Red', 'hex' => '#FF0000']],
            'stock_couleurs' => [['name' => 'Red', 'quantity' => 50]],
            'quantite_stock' => 50,
            'prix_vente' => 50.00,
            'categorie_id' => 1,
        ]);

        $newStock = $product->decreaseColorStock('Red', 10);
        $this->assertEquals(40, $newStock);

        $product->refresh();
        $this->assertEquals(40, $product->quantite_stock);
    }

    public function test_can_increase_color_stock()
    {
        $product = Product::create([
            'name' => 'Test Product',
            'couleur' => [['name' => 'Blue', 'hex' => '#0000FF']],
            'stock_couleurs' => [['name' => 'Blue', 'quantity' => 30]],
            'quantite_stock' => 30,
            'prix_vente' => 40.00,
            'categorie_id' => 1,
        ]);

        $newStock = $product->increaseColorStock('Blue', 5);
        $this->assertEquals(35, $newStock);

        $product->refresh();
        $this->assertEquals(35, $product->quantite_stock);
    }
}
