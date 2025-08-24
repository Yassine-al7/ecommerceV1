<?php
/**
 * Script pour corriger les données de stock par couleur
 */

require_once 'vendor/autoload.php';

use App\Models\Product;

// Simuler l'environnement Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔧 CORRECTION DES DONNÉES DE STOCK PAR COULEUR\n";
echo "===============================================\n\n";

try {
    // Récupérer tous les produits
    $products = Product::all();

    if ($products->isEmpty()) {
        echo "❌ Aucun produit trouvé dans la base de données\n";
        exit;
    }

    echo "✅ Produits trouvés: " . $products->count() . "\n\n";

    $productsCorriges = 0;

    foreach ($products as $product) {
        echo "🔍 Vérification du produit: {$product->name} (ID: {$product->id})\n";

        $needsUpdate = false;
        $stockCouleurs = $product->stock_couleurs;
        $couleurs = $product->couleur;

        // Vérifier si le produit a des couleurs mais pas de stock par couleur
        if ($couleurs && is_array($couleurs) && (!is_array($stockCouleurs) || empty($stockCouleurs))) {
            echo "   ⚠️ Produit avec couleurs mais sans stock par couleur\n";
            $needsUpdate = true;

            // Créer un stock par couleur basé sur le stock total
            $stockTotal = $product->quantite_stock;
            $nombreCouleurs = count($couleurs);
            $stockParCouleur = intval($stockTotal / $nombreCouleurs);

            $nouveauStockCouleurs = [];
            foreach ($couleurs as $couleur) {
                $couleurName = is_array($couleur) ? $couleur['name'] : $couleur;
                $nouveauStockCouleurs[] = [
                    'name' => $couleurName,
                    'quantity' => $stockParCouleur
                ];
            }

            echo "   📊 Création du stock par couleur: {$stockParCouleur} par couleur\n";
            $product->stock_couleurs = $nouveauStockCouleurs;

        } elseif (is_array($stockCouleurs) && !empty($stockCouleurs)) {
            // Vérifier que chaque couleur a une quantité valide
            foreach ($stockCouleurs as $index => $stock) {
                if (!is_array($stock) || !isset($stock['name']) || !isset($stock['quantity'])) {
                    echo "   ⚠️ Format de stock invalide pour l'index {$index}\n";
                    $needsUpdate = true;

                    // Corriger le format
                    if (is_string($stock)) {
                        $stockCouleurs[$index] = [
                            'name' => $stock,
                            'quantity' => $product->quantite_stock
                        ];
                    } else {
                        $stockCouleurs[$index] = [
                            'name' => 'Couleur ' . ($index + 1),
                            'quantity' => $product->quantite_stock
                        ];
                    }
                }
            }

            if ($needsUpdate) {
                $product->stock_couleurs = $stockCouleurs;
            }
        }

        // Vérifier que le stock total correspond à la somme des stocks par couleur
        if (is_array($stockCouleurs) && !empty($stockCouleurs)) {
            $stockCalcule = 0;
            foreach ($stockCouleurs as $stock) {
                if (is_array($stock) && isset($stock['quantity'])) {
                    $stockCalcule += intval($stock['quantity']);
                }
            }

            if ($stockCalcule !== $product->quantite_stock) {
                echo "   ⚠️ Différence de stock: calculé {$stockCalcule} vs base {$product->quantite_stock}\n";
                $needsUpdate = true;

                // Mettre à jour le stock total pour qu'il corresponde
                $product->quantite_stock = $stockCalcule;
            }
        }

        // Sauvegarder si des modifications ont été apportées
        if ($needsUpdate) {
            try {
                $product->save();
                echo "   ✅ Produit mis à jour\n";
                $productsCorriges++;
            } catch (Exception $e) {
                echo "   ❌ Erreur lors de la sauvegarde: " . $e->getMessage() . "\n";
            }
        } else {
            echo "   ✅ Produit déjà correct\n";
        }

        echo "\n";
    }

    echo "🎯 RÉSUMÉ DE LA CORRECTION:\n";
    echo "✅ Produits vérifiés: " . $products->count() . "\n";
    echo "✅ Produits corrigés: " . $productsCorriges . "\n";
    echo "✅ Produits déjà corrects: " . ($products->count() - $productsCorriges) . "\n\n";

    if ($productsCorriges > 0) {
        echo "🚀 Les données ont été corrigées ! Maintenant testez votre formulaire:\n";
        echo "1. Allez sur votre page d'édition de commande admin\n";
        echo "2. Sélectionnez un produit\n";
        echo "3. Choisissez une couleur\n";
        echo "4. Vérifiez que le stock affiché correspond au stock réel de la couleur\n";
        echo "5. Vérifiez que les alertes s'affichent correctement\n\n";
    } else {
        echo "ℹ️ Aucune correction nécessaire. Le problème pourrait être ailleurs.\n";
        echo "🔍 Lancez 'php test_stock_couleurs.php' pour un diagnostic plus détaillé.\n\n";
    }

} catch (Exception $e) {
    echo "❌ ERREUR: " . $e->getMessage() . "\n";
    echo "📍 Fichier: " . $e->getFile() . "\n";
    echo "📍 Ligne: " . $e->getLine() . "\n";
}
