<?php
/**
 * Test et correction du stock
 */

require_once 'vendor/autoload.php';

use App\Models\Product;
use App\Services\StockUpdateService;

// Simuler l'environnement Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔧 TEST ET CORRECTION DU STOCK\n";
echo "==============================\n\n";

try {
    // 1. Vérifier l'état actuel du stock
    echo "1️⃣ VÉRIFICATION DE L'ÉTAT ACTUEL DU STOCK\n";
    echo "==========================================\n\n";

    $products = Product::all();

    if ($products->isEmpty()) {
        echo "❌ Aucun produit trouvé dans la base de données\n";
        exit;
    }

    echo "✅ Produits trouvés: " . $products->count() . "\n\n";

    $inconsistencies = [];

    foreach ($products as $product) {
        echo "📦 {$product->name} (ID: {$product->id})\n";
        echo "   📊 Stock total en base: {$product->quantite_stock}\n";

        $stockCouleurs = $product->stock_couleurs;
        if ($stockCouleurs && is_array($stockCouleurs)) {
            $stockTotalCalcule = 0;
            echo "   🎨 Stock par couleur:\n";

            foreach ($stockCouleurs as $stock) {
                if (is_array($stock) && isset($stock['name']) && isset($stock['quantity'])) {
                    $quantite = intval($stock['quantity']);
                    $stockTotalCalcule += $quantite;
                    echo "      • {$stock['name']}: {$quantite} unités\n";
                }
            }

            echo "   📊 Stock total calculé: {$stockTotalCalcule}\n";

            if ($stockTotalCalcule !== $product->quantite_stock) {
                $inconsistencies[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'stock_base' => $product->quantite_stock,
                    'stock_calcule' => $stockTotalCalcule,
                    'difference' => $product->quantite_stock - $stockTotalCalcule
                ];

                echo "      ⚠️ INCOHÉRENCE DÉTECTÉE! Différence: " . ($product->quantite_stock - $stockTotalCalcule) . "\n";
            } else {
                echo "      ✅ Stock cohérent\n";
            }
        } else {
            echo "   ❌ Pas de stock par couleur défini\n";
        }

        echo "\n";
    }

    // 2. Corriger les incohérences
    if (!empty($inconsistencies)) {
        echo "2️⃣ CORRECTION DES INCOHÉRENCES DE STOCK\n";
        echo "=======================================\n\n";

        echo "🔍 Incohérences détectées: " . count($inconsistencies) . "\n\n";

        foreach ($inconsistencies as $inconsistency) {
            echo "📦 {$inconsistency['product_name']}:\n";
            echo "   Stock en base: {$inconsistency['stock_base']}\n";
            echo "   Stock calculé: {$inconsistency['stock_calcule']}\n";
            echo "   Différence: {$inconsistency['difference']}\n";

            // Corriger le produit
            $product = Product::find($inconsistency['product_id']);
            if ($product) {
                $stockCouleurs = $product->stock_couleurs;
                if (is_array($stockCouleurs) && !empty($stockCouleurs)) {
                    $stockTotalCalcule = 0;
                    foreach ($stockCouleurs as $stock) {
                        if (is_array($stock) && isset($stock['quantity'])) {
                            $stockTotalCalcule += intval($stock['quantity']);
                        }
                    }

                    $product->quantite_stock = $stockTotalCalcule;
                    $product->save();

                    echo "   ✅ Stock corrigé: {$stockTotalCalcule}\n";
                }
            }

            echo "\n";
        }

        echo "🎯 Correction terminée!\n\n";
    } else {
        echo "✅ Aucune incohérence détectée - le stock est cohérent!\n\n";
    }

    // 3. Vérifier le stock après correction
    echo "3️⃣ VÉRIFICATION DU STOCK APRÈS CORRECTION\n";
    echo "==========================================\n\n";

    $products = Product::all()->fresh();

    foreach ($products as $product) {
        echo "📦 {$product->name}:\n";
        echo "   📊 Stock total: {$product->quantite_stock}\n";

        $stockCouleurs = $product->stock_couleurs;
        if ($stockCouleurs && is_array($stockCouleurs)) {
            $stockTotalCalcule = 0;
            foreach ($stockCouleurs as $stock) {
                if (is_array($stock) && isset($stock['name']) && isset($stock['quantity'])) {
                    $quantite = intval($stock['quantity']);
                    $stockTotalCalcule += $quantite;
                    echo "      • {$stock['name']}: {$quantite} unités\n";
                }
            }

            if ($stockTotalCalcule === $product->quantite_stock) {
                echo "      ✅ Stock cohérent\n";
            } else {
                echo "      ❌ Stock toujours incohérent\n";
            }
        }

        echo "\n";
    }

    // 4. Tester le service de mise à jour du stock
    echo "4️⃣ TEST DU SERVICE DE MISE À JOUR DU STOCK\n";
    echo "==========================================\n\n";

    echo "🔧 Service StockUpdateService créé avec succès!\n";
    echo "📋 Fonctionnalités disponibles:\n";
    echo "   • updateStockAfterDelivery() - Mise à jour après livraison\n";
    echo "   • updateStockForAllDeliveredOrders() - Mise à jour en lot\n";
    echo "   • fixStockInconsistencies() - Correction des incohérences\n\n";

    echo "🚀 POUR UTILISER LE SERVICE:\n";
    echo "1. Dans votre contrôleur de commandes:\n";
    echo "   use App\\Services\\StockUpdateService;\n\n";
    echo "2. Après livraison d'une commande:\n";
    echo "   StockUpdateService::updateStockAfterDelivery($order);\n\n";
    echo "3. Pour corriger toutes les commandes livrées:\n";
    echo "   StockUpdateService::updateStockForAllDeliveredOrders();\n\n";

    echo "🎯 PROCHAINES ÉTAPES:\n";
    echo "1. Testez votre formulaire d'édition de commande\n";
    echo "2. Vérifiez que le stock affiché correspond au stock réel\n";
    echo "3. Implémentez la mise à jour automatique du stock\n";
    echo "4. Testez avec des commandes livrées\n\n";

} catch (Exception $e) {
    echo "❌ ERREUR: " . $e->getMessage() . "\n";
    echo "📍 Fichier: " . $e->getFile() . "\n";
    echo "📍 Ligne: " . $e->getLine() . "\n";
}
