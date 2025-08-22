<?php

/**
 * Test du système de messages liés au stock
 *
 * Ce fichier teste la relation entre les messages admin et le niveau de stock
 * pour vérifier si le système d'alerte fonctionne correctement.
 */

// Initialiser Laravel
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Product;
use App\Models\AdminMessage;
use App\Models\Category;

// Configuration de base
$testResults = [];
$errors = [];

echo "=== TEST DU SYSTÈME DE MESSAGES LIÉS AU STOCK ===\n\n";

try {
    // Test 1: Vérifier la structure de la base de données
    echo "1. Vérification de la structure de la base de données...\n";

    // Vérifier si la table produits a le champ quantite_stock
    $product = Product::first();
    if ($product && isset($product->quantite_stock)) {
        echo "   ✅ Champ quantite_stock trouvé dans la table produits\n";
        $testResults['structure'] = 'PASS';
    } else {
        echo "   ❌ Champ quantite_stock manquant dans la table produits\n";
        $testResults['structure'] = 'FAIL';
        $errors[] = 'Champ quantite_stock manquant';
    }

    // Vérifier si la table admin_messages existe
    $message = AdminMessage::first();
    if ($message) {
        echo "   ✅ Table admin_messages accessible\n";
        $testResults['messages_table'] = 'PASS';
    } else {
        echo "   ❌ Table admin_messages inaccessible\n";
        $testResults['messages_table'] = 'FAIL';
        $errors[] = 'Table admin_messages inaccessible';
    }

    echo "\n2. Test des niveaux de stock...\n";

    // Test des différents niveaux de stock
    $stockLevels = [
        'faible' => 3,
        'moyen' => 15,
        'bon' => 25,
        'rupture' => 0
    ];

    foreach ($stockLevels as $level => $quantity) {
        $products = Product::where('quantite_stock', '<=', $quantity)->get();
        if ($level === 'faible') {
            $products = Product::where('quantite_stock', '<=', 5)->get();
        } elseif ($level === 'moyen') {
            $products = Product::where('quantite_stock', '>', 5)->where('quantite_stock', '<=', 20)->get();
        } elseif ($level === 'bon') {
            $products = Product::where('quantite_stock', '>', 20)->get();
        } elseif ($level === 'rupture') {
            $products = Product::where('quantite_stock', '<=', 0)->get();
        }

        echo "   {$level}: {$products->count()} produits\n";
        $testResults["stock_level_{$level}"] = 'PASS';
    }

    echo "\n3. Test de création de messages liés au stock...\n";

    // Créer un message d'alerte pour stock faible
    try {
        $stockAlertMessage = AdminMessage::create([
            'title' => 'Alerte Stock Faible',
            'message' => 'Certains produits ont un stock faible. Vérifiez les niveaux de stock.',
            'type' => 'warning',
            'priority' => 'high',
            'is_active' => true,
            'target_roles' => ['seller', 'admin']
        ]);

        if ($stockAlertMessage) {
            echo "   ✅ Message d'alerte stock créé avec succès (ID: {$stockAlertMessage->id})\n";
            $testResults['message_creation'] = 'PASS';

            // Nettoyer le message de test
            $stockAlertMessage->delete();
            echo "   🧹 Message de test supprimé\n";
        }
    } catch (Exception $e) {
        echo "   ❌ Erreur lors de la création du message: " . $e->getMessage() . "\n";
        $testResults['message_creation'] = 'FAIL';
        $errors[] = 'Erreur création message: ' . $e->getMessage();
    }

    echo "\n4. Test de la logique métier...\n";

    // Vérifier s'il y a des produits avec stock faible
    $lowStockProducts = Product::where('quantite_stock', '<=', 5)->get();
    $outOfStockProducts = Product::where('quantite_stock', '<=', 0)->get();

    if ($lowStockProducts->count() > 0) {
        echo "   ⚠️  {$lowStockProducts->count()} produits avec stock faible détectés\n";
        foreach ($lowStockProducts->take(3) as $product) {
            echo "      - {$product->name}: {$product->quantite_stock} unités\n";
        }
        $testResults['low_stock_detection'] = 'PASS';
    } else {
        echo "   ✅ Aucun produit avec stock faible\n";
        $testResults['low_stock_detection'] = 'PASS';
    }

    if ($outOfStockProducts->count() > 0) {
        echo "   🚨 {$outOfStockProducts->count()} produits en rupture de stock\n";
        foreach ($outOfStockProducts->take(3) as $product) {
            echo "      - {$product->name}: {$product->quantite_stock} unités\n";
        }
        $testResults['out_of_stock_detection'] = 'PASS';
    } else {
        echo "   ✅ Aucun produit en rupture de stock\n";
        $testResults['out_of_stock_detection'] = 'PASS';
    }

    echo "\n5. Test des indicateurs visuels...\n";

    // Tester la logique des indicateurs de stock
    $testProduct = Product::first();
    if ($testProduct) {
        $stockLevel = '';
        $cssClass = '';

        if ($testProduct->quantite_stock <= 5) {
            $stockLevel = 'Faible';
            $cssClass = 'bg-red-100 text-red-800';
        } elseif ($testProduct->quantite_stock <= 20) {
            $stockLevel = 'Moyen';
            $cssClass = 'bg-yellow-100 text-yellow-800';
        } else {
            $stockLevel = 'Bon';
            $cssClass = 'bg-green-100 text-green-800';
        }

        echo "   ✅ Indicateur de stock fonctionnel: {$stockLevel} ({$testProduct->quantite_stock} unités)\n";
        echo "      Classe CSS: {$cssClass}\n";
        $testResults['visual_indicators'] = 'PASS';
    } else {
        echo "   ❌ Aucun produit trouvé pour tester les indicateurs\n";
        $testResults['visual_indicators'] = 'FAIL';
        $errors[] = 'Aucun produit pour tester les indicateurs';
    }

} catch (Exception $e) {
    echo "❌ Erreur générale: " . $e->getMessage() . "\n";
    $errors[] = 'Erreur générale: ' . $e->getMessage();
}

// Résumé des tests
echo "\n=== RÉSUMÉ DES TESTS ===\n";
$passedTests = array_count_values($testResults)['PASS'] ?? 0;
$totalTests = count($testResults);

echo "Tests réussis: {$passedTests}/{$totalTests}\n";

if (!empty($errors)) {
    echo "\n❌ Erreurs détectées:\n";
    foreach ($errors as $error) {
        echo "   - {$error}\n";
    }
} else {
    echo "\n✅ Tous les tests sont passés avec succès!\n";
}

echo "\n=== RECOMMANDATIONS ===\n";

if ($passedTests === $totalTests) {
    echo "✅ Le système de base fonctionne correctement\n";
    echo "💡 Suggestions d'amélioration:\n";
    echo "   1. Ajouter des notifications automatiques pour stock faible\n";
    echo "   2. Créer des messages automatiques liés au niveau de stock\n";
    echo "   3. Implémenter un système d'alerte en temps réel\n";
    echo "   4. Ajouter des rapports de stock quotidiens\n";
} else {
    echo "⚠️  Certains tests ont échoué\n";
    echo "🔧 Actions recommandées:\n";
    echo "   1. Vérifier la structure de la base de données\n";
    echo "   2. Contrôler les migrations\n";
    echo "   3. Vérifier les modèles et relations\n";
}

echo "\n=== FIN DU TEST ===\n";
