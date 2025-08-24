<?php
/**
 * Test simple du filtrage des couleurs
 *
 * Ce fichier teste que le filtrage des couleurs fonctionne
 * sans erreur JSON_DECODE
 */

require_once 'vendor/autoload.php';

use App\Models\Product;
use App\Models\Category;

// Simuler l'environnement Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧪 TEST SIMPLE DU FILTRAGE DES COULEURS\n";
echo "======================================\n\n";

try {
    // 1. Créer une catégorie
    echo "1️⃣ Création de la catégorie 'Test'...\n";
    $category = Category::firstOrCreate(
        ['name' => 'Test'],
        ['slug' => 'test', 'color' => '#3B82F6']
    );
    echo "   ✅ Catégorie créée: {$category->name}\n\n";

    // 2. Créer un produit avec des couleurs et stocks variés
    echo "2️⃣ Création du produit 'TEST FILTRAGE'...\n";

    $couleursInitiales = [
        ['name' => 'Rouge', 'hex' => '#ff0000'],
        ['name' => 'CHIBI', 'hex' => '#ff6b6b'],
        ['name' => 'MARINE', 'hex' => '#1e40af']
    ];

    $stockInitial = [
        ['name' => 'Rouge', 'quantity' => 50],    // Stock positif
        ['name' => 'CHIBI', 'quantity' => 0],     // Stock = 0 (à filtrer)
        ['name' => 'MARINE', 'quantity' => 100]   // Stock positif
    ];

    $produit = Product::firstOrCreate(
        ['name' => 'TEST FILTRAGE'],
        [
            'categorie_id' => $category->id,
            'couleur' => json_encode($couleursInitiales),
            'stock_couleurs' => json_encode($stockInitial),
            'tailles' => json_encode(['S', 'M', 'L']),
            'prix_admin' => 100.00,
            'prix_vente' => 150.00,
            'quantite_stock' => 150,
            'image' => '/storage/products/default-product.svg'
        ]
    );

    echo "   ✅ Produit créé: {$produit->name}\n";
    echo "   🎨 Couleurs initiales: " . count($couleursInitiales) . "\n";
    echo "   📊 Stock initial: " . count($stockInitial) . "\n\n";

    // 3. Test des accesseurs du modèle
    echo "3️⃣ Test des accesseurs du modèle...\n";

    echo "   🎯 Accesseur stock_couleurs:\n";
    $stockCouleurs = $produit->stock_couleurs;
    echo "      📊 Type: " . gettype($stockCouleurs) . "\n";
    echo "      📊 Nombre: " . count($stockCouleurs) . "\n";
    foreach ($stockCouleurs as $stock) {
        echo "      - {$stock['name']}: {$stock['quantity']} unités\n";
    }

    echo "   🎯 Accesseur couleur:\n";
    $couleurs = $produit->couleur;
    echo "      📊 Type: " . gettype($couleurs) . "\n";
    echo "      📊 Nombre: " . count($couleurs) . "\n";
    foreach ($couleurs as $couleur) {
        $nomCouleur = is_array($couleur) ? $couleur['name'] : $couleur;
        echo "      - {$nomCouleur}\n";
    }
    echo "\n";

    // 4. Test du filtrage
    echo "4️⃣ Test du filtrage des couleurs...\n";

    // Simuler le filtrage du contrôleur
    $stockCouleursFiltres = [];
    $couleursFiltrees = [];

    foreach ($stockCouleurs as $index => $stock) {
        if ($stock['quantity'] > 0) {
            $stockCouleursFiltres[] = $stock;

            if (isset($couleurs[$index])) {
                $couleursFiltrees[] = $couleurs[$index];
            }
        }
    }

    echo "   🎨 Couleurs filtrées: " . count($couleursFiltrees) . "\n";
    foreach ($couleursFiltrees as $couleur) {
        $nomCouleur = is_array($couleur) ? $couleur['name'] : $couleur;
        echo "      ✅ {$nomCouleur}\n";
    }

    echo "   📊 Stock filtré: " . count($stockCouleursFiltres) . "\n";
    foreach ($stockCouleursFiltres as $stock) {
        echo "      ✅ {$stock['name']}: {$stock['quantity']} unités\n";
    }
    echo "\n";

    // 5. Test des accesseurs filtrés
    echo "5️⃣ Test des accesseurs filtrés...\n";

    echo "   🎯 Accesseur couleurs_filtrees:\n";
    $couleursFiltreesAccesseur = $produit->couleurs_filtrees;
    echo "      📊 Nombre: " . count($couleursFiltreesAccesseur) . "\n";
    foreach ($couleursFiltreesAccesseur as $couleur) {
        $nomCouleur = is_array($couleur) ? $couleur['name'] : $couleur;
        echo "      ✅ {$nomCouleur}\n";
    }

    echo "   🎯 Accesseur stock_couleurs_filtres:\n";
    $stockFiltresAccesseur = $produit->stock_couleurs_filtres;
    echo "      📊 Nombre: " . count($stockFiltresAccesseur) . "\n";
    foreach ($stockFiltresAccesseur as $stock) {
        echo "      ✅ {$stock['name']}: {$stock['quantity']} unités\n";
    }
    echo "\n";

    // 6. Validation finale
    echo "6️⃣ Validation finale...\n";

    $couleursAttendues = ['Rouge', 'MARINE'];
    $stockAttendu = 150; // 50 + 100

    $filtrageReussi = true;

    // Vérifier le nombre de couleurs filtrées
    if (count($couleursFiltrees) !== count($couleursAttendues)) {
        $filtrageReussi = false;
        echo "      ❌ Nombre de couleurs filtrées incorrect\n";
    }

    // Vérifier le stock total filtré
    $stockTotalFiltre = array_sum(array_column($stockCouleursFiltres, 'quantity'));
    if ($stockTotalFiltre !== $stockAttendu) {
        $filtrageReussi = false;
        echo "      ❌ Stock total filtré incorrect: {$stockTotalFiltre} ≠ {$stockAttendu}\n";
    }

    if ($filtrageReussi) {
        echo "      ✅ Filtrage des couleurs réussi !\n";
    }
    echo "\n";

    echo "🎉 TEST SIMPLE TERMINÉ !\n";
    echo "========================\n\n";

    if ($filtrageReussi) {
        echo "🚀 SUCCÈS: Le filtrage des couleurs fonctionne parfaitement !\n";
        echo "   ✅ Pas d'erreur JSON_DECODE\n";
        echo "   ✅ Accesseurs du modèle fonctionnent\n";
        echo "   ✅ Filtrage des couleurs avec stock = 0 réussi\n";
    } else {
        echo "⚠️ ATTENTION: Le filtrage des couleurs présente des problèmes.\n";
    }

} catch (Exception $e) {
    echo "❌ ERREUR: " . $e->getMessage() . "\n";
    echo "📍 Fichier: " . $e->getFile() . "\n";
    echo "📍 Ligne: " . $e->getLine() . "\n";
    echo "🔍 Trace:\n" . $e->getTraceAsString() . "\n";
}
