<?php
/**
 * Test du filtrage des couleurs selon le stock disponible
 */

require_once 'vendor/autoload.php';

use App\Models\Product;

// Simuler l'environnement Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧪 TEST DU FILTRAGE DES COULEURS PAR STOCK\n";
echo "===========================================\n\n";

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
        echo "   📦 Stock total: {$product->quantite_stock}\n";

        // Analyser les couleurs et leur stock
        $couleurs = $product->couleur;
        $stockCouleurs = $product->stock_couleurs;

        if (!$couleurs || !is_array($couleurs)) {
            echo "   ❌ Pas de couleurs définies\n\n";
            continue;
        }

        echo "   🎨 Couleurs disponibles: " . count($couleurs) . "\n";

        // Analyser chaque couleur
        $couleursDisponibles = [];
        $couleursIndisponibles = [];

        foreach ($couleurs as $couleur) {
            $couleurName = is_array($couleur) ? $couleur['name'] : $couleur;

            // Chercher le stock pour cette couleur
            $stockCouleur = 0;
            $couleurTrouvee = false;

            if ($stockCouleurs && is_array($stockCouleurs)) {
                foreach ($stockCouleurs as $stockData) {
                    if (is_array($stockData) && isset($stockData['name']) && $stockData['name'] === $couleurName) {
                        $stockCouleur = intval($stockData['quantity'] ?? 0);
                        $couleurTrouvee = true;
                        break;
                    }
                }
            }

            if ($couleurTrouvee) {
                if ($stockCouleur > 0) {
                    $couleursDisponibles[] = [
                        'name' => $couleurName,
                        'stock' => $stockCouleur
                    ];
                    echo "      ✅ {$couleurName}: {$stockCouleur} unités\n";
                } else {
                    $couleursIndisponibles[] = [
                        'name' => $couleurName,
                        'stock' => $stockCouleur
                    ];
                    echo "      ❌ {$couleurName}: {$stockCouleur} unités (RUPTURE)\n";
                }
            } else {
                $couleursIndisponibles[] = [
                    'name' => $couleurName,
                    'stock' => 'N/A'
                ];
                echo "      ⚠️ {$couleurName}: Stock non défini\n";
            }
        }

        // Résumé du filtrage
        echo "   📊 Résumé du filtrage:\n";
        echo "      🟢 Couleurs disponibles: " . count($couleursDisponibles) . "\n";
        echo "      🔴 Couleurs indisponibles: " . count($couleursIndisponibles) . "\n";

        if (count($couleursDisponibles) > 0) {
            echo "      ✅ Ce produit sera affiché dans la liste\n";
            echo "      📋 Couleurs qui apparaîtront:\n";
            foreach ($couleursDisponibles as $couleur) {
                echo "         • {$couleur['name']} (Stock: {$couleur['stock']})\n";
            }
        } else {
            echo "      ❌ Ce produit ne sera PAS affiché (aucune couleur disponible)\n";
        }

        if (count($couleursIndisponibles) > 0) {
            echo "      🚫 Couleurs qui seront masquées:\n";
            foreach ($couleursIndisponibles as $couleur) {
                $stock = $couleur['stock'];
                $raison = $stock === 'N/A' ? 'Stock non défini' : 'Stock = 0';
                echo "         • {$couleur['name']} - {$raison}\n";
            }
        }

        echo "\n" . str_repeat("-", 80) . "\n\n";
    }

    echo "🎯 LOGIQUE DE FILTRAGE IMPLÉMENTÉE:\n";
    echo "✅ Les couleurs avec stock > 0 sont affichées\n";
    echo "❌ Les couleurs avec stock = 0 sont masquées\n";
    echo "⚠️ Les couleurs sans stock défini sont masquées\n";
    echo "📋 Le stock est affiché à côté de chaque couleur\n";
    echo "🚨 Les alertes ne s'affichent que pour les couleurs sélectionnées\n\n";

    echo "🔧 POUR TESTER:\n";
    echo "1. Allez sur votre page d'édition de commande admin\n";
    echo "2. Sélectionnez un produit\n";
    echo "3. Vérifiez que seules les couleurs avec stock > 0 apparaissent\n";
    echo "4. Vérifiez que le stock est affiché à côté de chaque couleur\n";
    echo "5. Sélectionnez une couleur et vérifiez les alertes\n\n";

} catch (Exception $e) {
    echo "❌ ERREUR: " . $e->getMessage() . "\n";
    echo "📍 Fichier: " . $e->getFile() . "\n";
    echo "📍 Ligne: " . $e->getLine() . "\n";
}
