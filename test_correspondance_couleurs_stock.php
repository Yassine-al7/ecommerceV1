<?php
/**
 * Test de la correspondance exacte entre couleurs et stocks
 */

require_once 'vendor/autoload.php';

use App\Models\Product;

// Simuler l'environnement Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 TEST DE CORRESPONDANCE COULEURS-STOCK\n";
echo "=========================================\n\n";

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
        echo "   📦 Stock total: {$product->quantite_stock}\n\n";

        // Analyser les couleurs
        $couleurs = $product->couleur;
        $stockCouleurs = $product->stock_couleurs;

        if (!$couleurs || !is_array($couleurs)) {
            echo "   ❌ Pas de couleurs définies\n\n";
            continue;
        }

        if (!$stockCouleurs || !is_array($stockCouleurs)) {
            echo "   ❌ Pas de stock par couleur défini\n\n";
            continue;
        }

        echo "   🎨 ANALYSE DÉTAILLÉE:\n";
        echo "   " . str_repeat("-", 50) . "\n";

        // Analyser chaque couleur
        foreach ($couleurs as $couleurIndex => $couleur) {
            $couleurName = is_array($couleur) ? $couleur['name'] : $couleur;
            $couleurHex = is_array($couleur) ? ($couleur['hex'] ?? 'N/A') : 'N/A';

            echo "   🔍 Couleur [{$couleurIndex}]: {$couleurName} (#{$couleurHex})\n";

            // Chercher le stock correspondant
            $stockTrouve = false;
            $stockCouleur = 0;

            foreach ($stockCouleurs as $stockIndex => $stock) {
                if (is_array($stock) && isset($stock['name'])) {
                    $stockName = $stock['name'];
                    $stockQuantity = $stock['quantity'] ?? 'N/A';

                    echo "      📊 Vérification stock [{$stockIndex}]: {$stockName} (qty: {$stockQuantity})\n";

                    if ($stockName === $couleurName) {
                        $stockCouleur = intval($stockQuantity);
                        $stockTrouve = true;
                        echo "      ✅ CORRESPONDANCE TROUVÉE! {$couleurName} = {$stockCouleur}\n";
                        break;
                    } else {
                        echo "      ❌ {$stockName} !== {$couleurName}\n";
                    }
                } else {
                    echo "      ⚠️ Format de stock invalide: " . json_encode($stock) . "\n";
                }
            }

            if (!$stockTrouve) {
                echo "      ❌ AUCUNE CORRESPONDANCE TROUVÉE pour {$couleurName}\n";
            }

            echo "\n";
        }

        // Résumé des correspondances
        echo "   📊 RÉSUMÉ DES CORRESPONDANCES:\n";
        echo "   " . str_repeat("-", 50) . "\n";

        $correspondances = 0;
        $totalCouleurs = count($couleurs);

        foreach ($couleurs as $couleur) {
            $couleurName = is_array($couleur) ? $couleur['name'] : $couleur;

            foreach ($stockCouleurs as $stock) {
                if (is_array($stock) && isset($stock['name']) && $stock['name'] === $couleurName) {
                    $correspondances++;
                    break;
                }
            }
        }

        echo "   🎯 Correspondances trouvées: {$correspondances}/{$totalCouleurs}\n";

        if ($correspondances === $totalCouleurs) {
            echo "   ✅ Toutes les couleurs ont une correspondance de stock\n";
        } elseif ($correspondances > 0) {
            echo "   ⚠️ Certaines couleurs ont une correspondance de stock\n";
        } else {
            echo "   ❌ Aucune correspondance de stock trouvée\n";
        }

        // Suggestions de correction
        if ($correspondances < $totalCouleurs) {
            echo "\n   🔧 SUGGESTIONS DE CORRECTION:\n";
            echo "   " . str_repeat("-", 50) . "\n";

            echo "   1. Vérifiez l'orthographe des noms de couleurs\n";
            echo "   2. Vérifiez les espaces et caractères spéciaux\n";
            echo "   3. Vérifiez la casse (majuscules/minuscules)\n";
            echo "   4. Vérifiez que les données sont bien synchronisées\n\n";
        }

        echo "\n" . str_repeat("=", 80) . "\n\n";
    }

    echo "🎯 DIAGNOSTIC COMPLET:\n";
    echo "1. ✅ Correspondances exactes vérifiées\n";
    echo "2. ✅ Formats de données analysés\n";
    echo "3. ✅ Suggestions de correction fournies\n\n";

    echo "🚀 POUR CORRIGER:\n";
    echo "1. Vérifiez les noms exacts des couleurs\n";
    echo "2. Synchronisez les données de stock\n";
    echo "3. Testez à nouveau le formulaire\n\n";

} catch (Exception $e) {
    echo "❌ ERREUR: " . $e->getMessage() . "\n";
    echo "📍 Fichier: " . $e->getFile() . "\n";
    echo "📍 Ligne: " . $e->getLine() . "\n";
}
