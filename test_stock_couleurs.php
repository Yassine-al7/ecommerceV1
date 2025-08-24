<?php
/**
 * Test du stock par couleur pour identifier le problème
 */

require_once 'vendor/autoload.php';

use App\Models\Product;

// Simuler l'environnement Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧪 TEST DU STOCK PAR COULEUR\n";
echo "==============================\n\n";

try {
    // Récupérer tous les produits avec leurs stocks par couleur
    $products = Product::all();

    if ($products->isEmpty()) {
        echo "❌ Aucun produit trouvé dans la base de données\n";
        exit;
    }

    echo "✅ Produits trouvés: " . $products->count() . "\n\n";

    foreach ($products as $index => $product) {
        echo "📦 Produit " . ($index + 1) . ": {$product->name}\n";
        echo "   🏷️ ID: {$product->id}\n";
        echo "   💰 Prix: {$product->prix_vente} MAD\n";
        echo "   📦 Stock total (quantite_stock): {$product->quantite_stock}\n";

        // Vérifier le champ stock_couleurs brut
        echo "   🔍 Stock couleurs (brut): ";
        $rawStockCouleurs = $product->getRawOriginal('stock_couleurs');
        if ($rawStockCouleurs === null) {
            echo "NULL\n";
        } elseif (is_string($rawStockCouleurs)) {
            echo "STRING: " . substr($rawStockCouleurs, 0, 100) . "...\n";
        } else {
            echo "TYPE: " . gettype($rawStockCouleurs) . "\n";
        }

        // Vérifier l'accesseur stock_couleurs
        echo "   🎯 Stock couleurs (accesseur): ";
        $stockCouleurs = $product->stock_couleurs;
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
        echo "   🎨 Couleurs: ";
        $couleurs = $product->couleur;
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

        // Vérifier les tailles
        echo "   📏 Tailles: ";
        $tailles = $product->tailles;
        if ($tailles === null) {
            echo "NULL\n";
        } elseif (is_array($tailles)) {
            echo "ARRAY avec " . count($tailles) . " éléments: " . implode(', ', $tailles) . "\n";
        } else {
            echo "TYPE: " . gettype($tailles) . " - " . json_encode($tailles) . "\n";
        }

        // Calculer le stock réel disponible
        echo "   📊 Calcul du stock réel:\n";
        if ($product->stock_couleurs && is_array($product->stock_couleurs)) {
            $stockTotal = 0;
            $stockParCouleur = [];

            foreach ($product->stock_couleurs as $stock) {
                if (is_array($stock) && isset($stock['name']) && isset($stock['quantity'])) {
                    $couleurName = $stock['name'];
                    $quantite = intval($stock['quantity']);
                    $stockTotal += $quantite;
                    $stockParCouleur[$couleurName] = $quantite;

                    $status = $quantite > 0 ? '✅' : '❌';
                    echo "      {$status} {$couleurName}: {$quantite} unités\n";
                }
            }

            echo "      📦 Stock total calculé: {$stockTotal}\n";
            echo "      📦 Stock total en base: {$product->quantite_stock}\n";

            if ($stockTotal !== $product->quantite_stock) {
                echo "      ⚠️ DIFFÉRENCE DÉTECTÉE! Le stock total ne correspond pas\n";
            }
        } else {
            echo "      ❌ Pas de données de stock par couleur\n";
        }

        echo "\n" . str_repeat("-", 80) . "\n\n";
    }

    echo "🎯 DIAGNOSTIC:\n";
    echo "1. Vérifiez que le champ 'stock_couleurs' contient des données JSON valides\n";
    echo "2. Vérifiez que chaque couleur a une quantité définie\n";
    echo "3. Vérifiez que les quantités sont des nombres\n";
    echo "4. Vérifiez que le stock total correspond à la somme des stocks par couleur\n\n";

    echo "🔧 SOLUTIONS POSSIBLES:\n";
    echo "1. Mettre à jour les produits avec des stocks par couleur valides\n";
    echo "2. Corriger le format JSON dans la base de données\n";
    echo "3. Vérifier que les migrations ont bien créé les colonnes\n";
    echo "4. Vérifier que les seeders ont bien rempli les données\n\n";

} catch (Exception $e) {
    echo "❌ ERREUR: " . $e->getMessage() . "\n";
    echo "📍 Fichier: " . $e->getFile() . "\n";
    echo "📍 Ligne: " . $e->getLine() . "\n";
    echo "🔍 Trace:\n" . $e->getTraceAsString() . "\n";
}
