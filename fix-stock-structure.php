<?php

require_once 'vendor/autoload.php';

use App\Models\Product;
use Illuminate\Support\Facades\DB;

// Charger l'application Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔧 Fixing stock structure...\n";

// Récupérer tous les produits
$products = Product::all();
$fixedCount = 0;

foreach ($products as $product) {
    $needsUpdate = false;
    $stockCouleurs = $product->stock_couleurs;

    if (!empty($stockCouleurs)) {
        $correctedStock = [];

        foreach ($stockCouleurs as $stock) {
            if (is_array($stock)) {
                // Vérifier si 'name' est un objet/array au lieu d'une string
                if (isset($stock['name']) && is_array($stock['name'])) {
                    // Extraire le nom de la couleur de l'objet
                    $colorName = $stock['name']['name'] ?? 'Unknown';
                    $correctedStock[] = [
                        'name' => $colorName,
                        'quantity' => $stock['quantity'] ?? 0
                    ];
                    $needsUpdate = true;
                    echo "✅ Fixed product {$product->name}: {$colorName}\n";
                } elseif (isset($stock['name']) && is_string($stock['name'])) {
                    // Structure correcte, garder tel quel
                    $correctedStock[] = $stock;
                } else {
                    echo "⚠️ Skipping invalid stock structure for {$product->name}\n";
                }
            }
        }

        if ($needsUpdate) {
            $product->stock_couleurs = $correctedStock;

            // Recalculer le stock total
            $stockTotal = 0;
            foreach ($correctedStock as $stock) {
                if (is_array($stock) && isset($stock['quantity'])) {
                    $stockTotal += (int)$stock['quantity'];
                }
            }
            $product->quantite_stock = $stockTotal;

            $product->save();
            $fixedCount++;
        }
    }
}

echo "🎯 Stock structure fix completed! Fixed {$fixedCount} products.\n";
