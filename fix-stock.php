<?php

require_once 'vendor/autoload.php';

use App\Models\Product;
use Illuminate\Support\Facades\DB;

// Charger l'application Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔧 Fixing stock issues...\n";

// Récupérer tous les produits
$products = Product::all();

$fixedCount = 0;

foreach ($products as $product) {
    $needsUpdate = false;

    // Vérifier si le stock_couleurs est vide mais qu'il y a des couleurs
    if (empty($product->stock_couleurs) && !empty($product->couleur)) {
        $couleurs = is_array($product->couleur) ? $product->couleur : [$product->couleur];
        $stockCouleurs = [];

        foreach ($couleurs as $couleur) {
            $stockCouleurs[] = [
                'name' => $couleur,
                'quantity' => $product->quantite_stock ?? 0
            ];
        }

        $product->stock_couleurs = $stockCouleurs;
        $needsUpdate = true;

        echo "✅ Initialized stock_couleurs for product: {$product->name}\n";
    }

    // Recalculer le stock total basé sur stock_couleurs
    if (!empty($product->stock_couleurs)) {
        $stockTotal = 0;
        foreach ($product->stock_couleurs as $stockCouleur) {
            if (is_array($stockCouleur) && isset($stockCouleur['quantity'])) {
                $stockTotal += (int)$stockCouleur['quantity'];
            }
        }

        if ($stockTotal !== $product->quantite_stock) {
            $product->quantite_stock = $stockTotal;
            $needsUpdate = true;

            echo "✅ Updated total stock for product: {$product->name} (was: {$product->getRawOriginal('quantite_stock')}, now: {$stockTotal})\n";
        }
    }

    if ($needsUpdate) {
        $product->save();
        $fixedCount++;
    }
}

echo "🎯 Stock fix completed! Fixed {$fixedCount} products.\n";
