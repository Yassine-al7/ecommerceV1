<?php
/**
 * TEST DEBUG : Vérifier les données du produit DJELLABA
 */

// Charger l'autoloader de Composer
require_once 'vendor/autoload.php';

// Démarrer Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 TEST DEBUG PRODUIT DJELLABA\n";
echo "==============================\n\n";

try {
    // Récupérer le produit DJELLABA
    $product = \App\Models\Product::where('name', 'DJELLABA')->first();

    if (!$product) {
        echo "❌ Produit DJELLABA non trouvé\n";
        exit(1);
    }

    echo "🎯 PRODUIT : {$product->name} (ID: {$product->id})\n";
    echo "   Stock global : {$product->quantite_stock}\n";

    // Vérifier les couleurs
    $couleurs = $product->couleur;
    echo "   Couleurs (raw) : " . json_encode($couleurs) . "\n";
    echo "   Type couleurs : " . gettype($couleurs) . "\n";

    // Vérifier le stock par couleur
    $stockCouleurs = $product->stock_couleurs;
    echo "   Stock par couleur (raw) : " . json_encode($stockCouleurs) . "\n";
    echo "   Type stock_couleurs : " . gettype($stockCouleurs) . "\n";

    // Vérifier les attributs bruts
    echo "\n🔍 ATTRIBUTS BRUTS :\n";
    echo "   stock_couleurs brut : " . $product->getRawOriginal('stock_couleurs') . "\n";
    echo "   Type brut : " . gettype($product->getRawOriginal('stock_couleurs')) . "\n";

    // Vérifier si c'est une chaîne JSON
    $rawStockCouleurs = $product->getRawOriginal('stock_couleurs');
    if (is_string($rawStockCouleurs)) {
        echo "   Longueur chaîne : " . strlen($rawStockCouleurs) . "\n";
        echo "   Premier caractère : " . ord($rawStockCouleurs[0]) . "\n";
        echo "   Dernier caractère : " . ord($rawStockCouleurs[-1]) . "\n";

        // Essayer de parser
        $parsed = json_decode($rawStockCouleurs, true);
        echo "   JSON parsé : " . json_encode($parsed) . "\n";
        echo "   Erreur JSON : " . json_last_error_msg() . "\n";
    }

    echo "\n✅ Test terminé !\n";

} catch (Exception $e) {
    echo "❌ Erreur lors du test : " . $e->getMessage() . "\n";
    echo "Stack trace :\n" . $e->getTraceAsString() . "\n";
}
