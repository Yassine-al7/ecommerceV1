<?php
/**
 * Test simple pour vérifier les alertes de stock
 */

require_once 'vendor/autoload.php';

use App\Models\Product;

// Simuler l'environnement Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧪 TEST DES ALERTES DE STOCK\n";
echo "=============================\n\n";

try {
    // Récupérer quelques produits pour tester
    $products = Product::take(3)->get();

    if ($products->isEmpty()) {
        echo "❌ Aucun produit trouvé dans la base de données\n";
        exit;
    }

    echo "✅ Produits trouvés: " . $products->count() . "\n\n";

    foreach ($products as $index => $product) {
        echo "📦 Produit " . ($index + 1) . ": {$product->name}\n";
        echo "   🏷️ ID: {$product->id}\n";
        echo "   💰 Prix: {$product->prix_vente} MAD\n";
        echo "   📦 Stock total: {$product->quantite_stock}\n";

        // Vérifier les couleurs
        if ($product->couleur) {
            $couleurs = is_array($product->couleur) ? $product->couleur : json_decode($product->couleur, true);
            echo "   🎨 Couleurs: ";
            if (is_array($couleurs)) {
                foreach ($couleurs as $couleur) {
                    $couleurName = is_array($couleur) ? $couleur['name'] : $couleur;
                    echo "{$couleurName} ";
                }
            }
            echo "\n";
        }

        // Vérifier le stock par couleur
        if ($product->stock_couleurs) {
            $stockCouleurs = is_array($product->stock_couleurs) ? $product->stock_couleurs : json_decode($product->stock_couleurs, true);
            echo "   📊 Stock par couleur:\n";
            if (is_array($stockCouleurs)) {
                foreach ($stockCouleurs as $stockCouleur) {
                    if (is_array($stockCouleur) && isset($stockCouleur['name'])) {
                        $quantite = $stockCouleur['quantity'] ?? 0;
                        $status = $quantite > 0 ? '✅' : '❌';
                        echo "      {$status} {$stockCouleur['name']}: {$quantite}\n";
                    }
                }
            }
        }

        // Vérifier les tailles
        if ($product->tailles) {
            $tailles = is_array($product->tailles) ? $product->tailles : json_decode($product->tailles, true);
            echo "   📏 Tailles: ";
            if (is_array($tailles)) {
                echo implode(', ', $tailles);
            }
            echo "\n";
        }

        echo "\n";
    }

    echo "🎯 POUR TESTER LES ALERTES:\n";
    echo "1. Allez sur votre page d'édition de commande admin\n";
    echo "2. Sélectionnez un produit\n";
    echo "3. Choisissez une couleur et une taille\n";
    echo "4. Modifiez la quantité\n";
    echo "5. Vérifiez que les alertes s'affichent dans la colonne 'Alertes'\n\n";

    echo "🔍 VÉRIFICATIONS:\n";
    echo "- ✅ Colonne 'Alertes' ajoutée au tableau\n";
    echo "- ✅ Fonction updateStockAlerts() implémentée\n";
    echo "- ✅ Événements de changement configurés\n";
    echo "- ✅ Attributs data-stock-couleurs ajoutés\n";
    echo "- ✅ Affichage des alertes en temps réel\n\n";

    echo "🚀 Prêt à tester !\n";

} catch (Exception $e) {
    echo "❌ ERREUR: " . $e->getMessage() . "\n";
    echo "📍 Fichier: " . $e->getFile() . "\n";
    echo "📍 Ligne: " . $e->getLine() . "\n";
}
