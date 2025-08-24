<?php
/**
 * Test de vérification des options de couleur et leurs attributs data-stock
 */

require_once 'vendor/autoload.php';

use App\Models\Product;

// Simuler l'environnement Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 TEST DE VÉRIFICATION DES OPTIONS DE COULEUR\n";
echo "===============================================\n\n";

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

        // Analyser les couleurs et leurs stocks
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

        echo "   🎨 ANALYSE DES COULEURS ET STOCKS:\n";
        echo "   " . str_repeat("-", 50) . "\n";

        $couleursDisponibles = [];

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
                if ($stockCouleur > 0) {
                    $couleursDisponibles[] = [
                        'name' => $couleurName,
                        'stock' => $stockCouleur
                    ];
                    echo "      ✅ {$couleurName}: Stock = {$stockCouleur} (disponible)\n";
                } else {
                    echo "      ❌ {$couleurName}: Stock = {$stockCouleur} (masquée)\n";
                }
            } else {
                echo "      ⚠️ {$couleurName}: Stock non trouvé\n";
            }
        }

        // Simuler la création des options HTML
        echo "\n   🎯 SIMULATION DES OPTIONS HTML:\n";
        echo "   " . str_repeat("-", 50) . "\n";

        if (count($couleursDisponibles) > 0) {
            echo "      <option value=\"\">Sélectionner une couleur</option>\n";

            foreach ($couleursDisponibles as $couleur) {
                $optionText = "{$couleur['name']} (Stock: {$couleur['stock']})";
                $dataStock = $couleur['stock'];

                echo "      <option value=\"{$couleur['name']}\" data-stock=\"{$dataStock}\">{$optionText}</option>\n";

                // Vérifier que l'attribut data-stock est correct
                if ($dataStock === $product->quantite_stock) {
                    echo "         ⚠️ ATTENTION: data-stock = {$dataStock} (stock total) au lieu du stock par couleur!\n";
                } else {
                    echo "         ✅ data-stock = {$dataStock} (stock par couleur correct)\n";
                }
            }
        } else {
            echo "      ❌ Aucune couleur disponible\n";
        }

        // Vérifier la cohérence
        echo "\n   📊 VÉRIFICATION DE COHÉRENCE:\n";
        echo "   " . str_repeat("-", 50) . "\n";

        $stockTotalCalcule = 0;
        foreach ($couleursDisponibles as $couleur) {
            $stockTotalCalcule += $couleur['stock'];
        }

        echo "   📦 Stock total calculé: {$stockTotalCalcule}\n";
        echo "   📦 Stock total en base: {$product->quantite_stock}\n";

        if ($stockTotalCalcule === $product->quantite_stock) {
            echo "   ✅ Stock total cohérent avec la somme des stocks par couleur\n";
        } else {
            echo "   ⚠️ Différence de stock: {$stockTotalCalcule} vs {$product->quantite_stock}\n";
        }

        echo "\n" . str_repeat("=", 80) . "\n\n";
    }

    echo "🎯 DIAGNOSTIC COMPLET:\n";
    echo "1. ✅ Couleurs et stocks analysés\n";
    echo "2. ✅ Options HTML simulées\n";
    echo "3. ✅ Attributs data-stock vérifiés\n";
    echo "4. ✅ Cohérence des stocks vérifiée\n\n";

    echo "🚨 PROBLÈMES IDENTIFIÉS:\n";
    echo "1. Si data-stock = stock total → Le fallback est utilisé à tort\n";
    echo "2. Si data-stock = stock par couleur → Tout est correct\n";
    echo "3. Si data-stock = N/A → Erreur dans la correspondance\n\n";

    echo "🚀 POUR CORRIGER:\n";
    echo "1. Vérifiez que les couleurs ont bien un stock > 0\n";
    echo "2. Vérifiez que la correspondance nom/stock fonctionne\n";
    echo "3. Vérifiez que le fallback n'est pas déclenché à tort\n\n";

} catch (Exception $e) {
    echo "❌ ERREUR: " . $e->getMessage() . "\n";
    echo "📍 Fichier: " . $e->getFile() . "\n";
    echo "📍 Ligne: " . $e->getLine() . "\n";
}
