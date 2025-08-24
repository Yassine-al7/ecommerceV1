<?php
/**
 * Test de l'affichage du stock pour diagnostiquer le problème
 */

require_once 'vendor/autoload.php';

use App\Models\Product;

// Simuler l'environnement Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧪 TEST DE L'AFFICHAGE DU STOCK\n";
echo "================================\n\n";

try {
    // Récupérer tous les produits
    $products = Product::all();

    if ($products->isEmpty()) {
        echo "❌ Aucun produit trouvé dans la base de données\n";
        exit;
    }

    echo "✅ Produits trouvés: " . $products->count() . "\n\n";

    foreach ($products as $index => $product) {
        echo "📦 Produit " . ($index + 1) . ": {$product->name}\n";
        echo "   🏷️ ID: {$product->id}\n";
        echo "   📦 Stock total (quantite_stock): {$product->quantite_stock}\n";

        // Vérifier le champ stock_couleurs brut
        $rawStockCouleurs = $product->getRawOriginal('stock_couleurs');
        echo "   🔍 Stock couleurs (brut): ";
        if ($rawStockCouleurs === null) {
            echo "NULL\n";
        } elseif (is_string($rawStockCouleurs)) {
            echo "STRING: " . substr($rawStockCouleurs, 0, 100) . "...\n";
        } else {
            echo "TYPE: " . gettype($rawStockCouleurs) . "\n";
        }

        // Vérifier l'accesseur stock_couleurs
        $stockCouleurs = $product->stock_couleurs;
        echo "   🎯 Stock couleurs (accesseur): ";
        if ($stockCouleurs === null) {
            echo "NULL\n";
        } elseif (is_array($stockCouleurs)) {
            echo "ARRAY avec " . count($stockCouleurs) . " éléments\n";
            foreach ($stockCouleurs as $i => $stock) {
                if (is_array($stock)) {
                    $name = $stock['name'] ?? 'N/A';
                    $quantity = $stock['quantity'] ?? 'N/A';
                    echo "      [{$i}] {$name}: {$quantity}\n";
                } else {
                    echo "      [{$i}] " . gettype($stock) . ": " . json_encode($stock) . "\n";
                }
            }
        } else {
            echo "TYPE: " . gettype($stockCouleurs) . " - " . json_encode($stockCouleurs) . "\n";
        }

        // Vérifier les couleurs
        $couleurs = $product->couleur;
        echo "   🎨 Couleurs: ";
        if ($couleurs === null) {
            echo "NULL\n";
        } elseif (is_array($couleurs)) {
            echo "ARRAY avec " . count($couleurs) . " éléments\n";
            foreach ($couleurs as $i => $couleur) {
                if (is_array($couleur)) {
                    $name = $couleur['name'] ?? 'N/A';
                    $hex = $couleur['hex'] ?? 'N/A';
                    echo "      [{$i}] {$name} (#{$hex})\n";
                } else {
                    echo "      [{$i}] " . gettype($couleur) . ": " . json_encode($couleur) . "\n";
                }
            }
        } else {
            echo "TYPE: " . gettype($couleurs) . " - " . json_encode($couleurs) . "\n";
        }

        // Simuler ce qui se passe dans le formulaire
        echo "   🔍 SIMULATION DU FORMULAIRE:\n";
        if ($couleurs && is_array($couleurs) && $stockCouleurs && is_array($stockCouleurs)) {
            foreach ($couleurs as $couleur) {
                $couleurName = is_array($couleur) ? $couleur['name'] : $couleur;

                // Chercher le stock pour cette couleur
                $stockCouleur = 0;
                $couleurTrouvee = false;

                foreach ($stockCouleurs as $stock) {
                    if (is_array($stock) && isset($stock['name']) && $stock['name'] === $couleurName) {
                        $stockCouleur = intval($stock['quantity'] ?? 0);
                        $couleurTrouvee = true;
                        break;
                    }
                }

                if ($couleurTrouvee) {
                    echo "      ✅ {$couleurName}: Stock réel = {$stockCouleur}\n";
                } else {
                    echo "      ❌ {$couleurName}: Stock non trouvé\n";
                }
            }
        } else {
            echo "      ⚠️ Données insuffisantes pour la simulation\n";
        }

        echo "\n" . str_repeat("-", 80) . "\n\n";
    }

    echo "🎯 DIAGNOSTIC DU PROBLÈME:\n";
    echo "1. Vérifiez que 'stock_couleurs' contient des données valides\n";
    echo "2. Vérifiez que chaque couleur a une quantité définie\n";
    echo "3. Vérifiez que les quantités sont des nombres\n";
    echo "4. Vérifiez que le stock affiché correspond au stock réel de la couleur\n\n";

    echo "🔧 SOLUTIONS POSSIBLES:\n";
    echo "1. Corriger les données de stock par couleur\n";
    echo "2. Modifier la logique d'affichage du stock\n";
    echo "3. Implémenter la mise à jour automatique du stock\n\n";

} catch (Exception $e) {
    echo "❌ ERREUR: " . $e->getMessage() . "\n";
    echo "📍 Fichier: " . $e->getFile() . "\n";
    echo "📍 Ligne: " . $e->getLine() . "\n";
}
