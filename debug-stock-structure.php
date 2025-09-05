<?php

require_once 'vendor/autoload.php';

use App\Models\Product;
use Illuminate\Support\Facades\DB;

// Charger l'application Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 Debugging stock structure...\n";

// Récupérer tous les produits
$products = Product::all();

foreach ($products as $product) {
    echo "\n📦 Product: {$product->name} (ID: {$product->id})\n";
    echo "   Stock total: {$product->quantite_stock}\n";
    echo "   Couleurs: " . json_encode($product->couleur) . "\n";
    echo "   Stock couleurs: " . json_encode($product->stock_couleurs) . "\n";
    
    if (!empty($product->stock_couleurs)) {
        echo "   Structure détaillée:\n";
        foreach ($product->stock_couleurs as $index => $stock) {
            echo "     [{$index}] Type: " . gettype($stock) . "\n";
            if (is_array($stock)) {
                echo "     [{$index}] Keys: " . implode(', ', array_keys($stock)) . "\n";
                echo "     [{$index}] Values: " . json_encode($stock) . "\n";
            } else {
                echo "     [{$index}] Value: " . $stock . "\n";
            }
        }
    }
}

echo "\n✅ Debug completed!\n";
