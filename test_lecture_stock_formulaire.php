<?php
/**
 * Test de la lecture du stock dans le formulaire
 */

require_once 'vendor/autoload.php';

use App\Models\Product;

// Simuler l'environnement Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧪 TEST DE LA LECTURE DU STOCK DANS LE FORMULAIRE\n";
echo "==================================================\n\n";

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

        // Simuler les attributs data du formulaire
        $dataStock = $product->quantite_stock;
        $dataStockCouleurs = json_encode($product->stock_couleurs);
        $dataCouleurs = json_encode($product->couleur);

        echo "   🔍 Attributs data du formulaire:\n";
        echo "      data-stock: {$dataStock}\n";
        echo "      data-stock-couleurs: {$dataStockCouleurs}\n";
        echo "      data-couleurs: {$dataCouleurs}\n\n";

        // Simuler la logique JavaScript du formulaire
        echo "   🎯 SIMULATION DE LA LOGIQUE JAVASCRIPT:\n";

        $couleurs = $product->couleur;
        $stockCouleurs = $product->stock_couleurs;

        if ($couleurs && is_array($couleurs) && $stockCouleurs && is_array($stockCouleurs)) {
            echo "      📋 Traitement des couleurs:\n";

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
                        echo "         ✅ {$couleurName}: Stock = {$stockCouleur} (disponible)\n";
                    } else {
                        echo "         ❌ {$couleurName}: Stock = {$stockCouleur} (masquée)\n";
                    }
                } else {
                    echo "         ⚠️ {$couleurName}: Stock non trouvé\n";
                }
            }

            echo "\n      📊 Résultat du filtrage:\n";
            if (count($couleursDisponibles) > 0) {
                foreach ($couleursDisponibles as $couleur) {
                    echo "         • {$couleur['name']} (Stock: {$couleur['stock']})\n";
                }
            } else {
                echo "         ❌ Aucune couleur disponible\n";
            }

            // Simuler l'affichage dans le dropdown
            echo "\n      🎨 AFFICHAGE DANS LE DROPDOWN:\n";
            echo "         <option value=\"\">Sélectionner une couleur</option>\n";

            foreach ($couleursDisponibles as $couleur) {
                $optionText = "{$couleur['name']} (Stock: {$couleur['stock']})";
                echo "         <option value=\"{$couleur['name']}\" data-stock=\"{$couleur['stock']}\">{$optionText}</option>\n";
            }

        } else {
            echo "      ❌ Données insuffisantes pour le traitement\n";
        }

        echo "\n" . str_repeat("-", 80) . "\n\n";
    }

    echo "🎯 DIAGNOSTIC DU PROBLÈME:\n";
    echo "1. Vérifiez que 'data-stock-couleurs' est bien passé au formulaire\n";
    echo "2. Vérifiez que le parsing JSON fonctionne correctement\n";
    echo "3. Vérifiez que la logique de filtrage est appliquée\n";
    echo "4. Vérifiez que les attributs 'data-stock' sont corrects\n\n";

    echo "🔧 SOLUTIONS IMPLÉMENTÉES:\n";
    echo "1. ✅ Logs de debug ajoutés dans le JavaScript\n";
    echo "2. ✅ Logique de filtrage améliorée\n";
    echo "3. ✅ Gestion des erreurs de parsing\n";
    echo "4. ✅ Fallback vers le stock total si nécessaire\n\n";

    echo "🚀 POUR TESTER:\n";
    echo "1. Allez sur votre page d'édition de commande admin\n";
    echo "2. Sélectionnez un produit\n";
    echo "3. Ouvrez la console JavaScript (F12)\n";
    echo "4. Vérifiez les logs de debug\n";
    echo "5. Vérifiez que le stock affiché correspond au stock réel\n\n";

} catch (Exception $e) {
    echo "❌ ERREUR: " . $e->getMessage() . "\n";
    echo "📍 Fichier: " . $e->getFile() . "\n";
    echo "📍 Ligne: " . $e->getLine() . "\n";
}
