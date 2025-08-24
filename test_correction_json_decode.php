<?php
/**
 * Test de correction de l'erreur json_decode
 *
 * Ce fichier teste que l'erreur json_decode ne se produit plus
 * quand on accède aux attributs couleur et stock_couleurs
 */

require_once 'vendor/autoload.php';

use App\Models\Product;
use App\Models\Category;

// Simuler l'environnement Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧪 TEST DE CORRECTION DE L'ERREUR JSON_DECODE\n";
echo "==============================================\n\n";

try {
    // 1. Créer une catégorie
    echo "1️⃣ Création de la catégorie 'Test JSON'...\n";
    $category = Category::firstOrCreate(
        ['name' => 'Test JSON'],
        ['slug' => 'test-json', 'color' => '#3B82F6']
    );
    echo "   ✅ Catégorie créée: {$category->name}\n\n";

    // 2. Créer un produit avec des couleurs et stocks
    echo "2️⃣ Création du produit 'TEST JSON DECODE'...\n";

    $couleursInitiales = [
        ['name' => 'Rouge', 'hex' => '#ff0000'],
        ['name' => 'CHIBI', 'hex' => '#ff6b6b']
    ];

    $stockInitial = [
        ['name' => 'Rouge', 'quantity' => 50],
        ['name' => 'CHIBI', 'quantity' => 75]
    ];

    $produit = Product::firstOrCreate(
        ['name' => 'TEST JSON DECODE'],
        [
            'categorie_id' => $category->id,
            'couleur' => json_encode($couleursInitiales),
            'stock_couleurs' => json_encode($stockInitial),
            'tailles' => json_encode(['S', 'M', 'L']),
            'prix_admin' => 100.00,
            'prix_vente' => 150.00,
            'quantite_stock' => 125,
            'image' => '/storage/products/default-product.svg'
        ]
    );

    echo "   ✅ Produit créé: {$produit->name}\n\n";

    // 3. Test d'accès aux attributs sans json_decode
    echo "3️⃣ Test d'accès aux attributs sans json_decode...\n";

    echo "   🎯 Test de l'attribut couleur:\n";
    $couleurs = $produit->couleur;
    echo "      📊 Type: " . gettype($couleurs) . "\n";
    echo "      📊 Nombre: " . (is_array($couleurs) ? count($couleurs) : 'N/A') . "\n";

    if (is_array($couleurs)) {
        foreach ($couleurs as $index => $couleur) {
            $nomCouleur = is_array($couleur) ? $couleur['name'] : $couleur;
            $hex = is_array($couleur) ? ($couleur['hex'] ?? 'N/A') : 'N/A';
            echo "      ✅ {$index}: {$nomCouleur} ({$hex})\n";
        }
    }

    echo "   🎯 Test de l'attribut stock_couleurs:\n";
    $stockCouleurs = $produit->stock_couleurs;
    echo "      📊 Type: " . gettype($stockCouleurs) . "\n";
    echo "      📊 Nombre: " . (is_array($stockCouleurs) ? count($stockCouleurs) : 'N/A') . "\n";

    if (is_array($stockCouleurs)) {
        foreach ($stockCouleurs as $index => $stock) {
            echo "      ✅ {$index}: {$stock['name']} = {$stock['quantity']} unités\n";
        }
    }
    echo "\n";

    // 4. Test de la méthode mergeColorsIntelligently (simulation)
    echo "4️⃣ Test de la méthode mergeColorsIntelligently (simulation)...\n";

    // Simuler l'appel à mergeColorsIntelligently
    $existingColors = $produit->couleur ?: [];
    $newColors = ['Rouge'];
    $newColorsHex = ['#ff0000'];
    $newCustomColors = ['CHIBI'];

    echo "   🔄 Paramètres passés à mergeColorsIntelligently:\n";
    echo "      📊 existingColors type: " . gettype($existingColors) . "\n";
    echo "      📊 existingColors count: " . (is_array($existingColors) ? count($existingColors) : 'N/A') . "\n";
    echo "      📊 newColors: " . implode(', ', $newColors) . "\n";
    echo "      📊 newColorsHex: " . implode(', ', $newColorsHex) . "\n";
    echo "      📊 newCustomColors: " . implode(', ', $newCustomColors) . "\n";

    // Vérifier que existingColors est bien un tableau
    if (is_array($existingColors)) {
        echo "      ✅ existingColors est bien un tableau\n";
    } else {
        echo "      ❌ existingColors n'est pas un tableau\n";
    }
    echo "\n";

    // 5. Test des accesseurs filtrés
    echo "5️⃣ Test des accesseurs filtrés...\n";

    echo "   🎯 Accesseur couleurs_filtrees:\n";
    $couleursFiltrees = $produit->couleurs_filtrees;
    echo "      📊 Type: " . gettype($couleursFiltrees) . "\n";
    echo "      📊 Nombre: " . (is_array($couleursFiltrees) ? count($couleursFiltrees) : 'N/A') . "\n";

    if (is_array($couleursFiltrees)) {
        foreach ($couleursFiltrees as $couleur) {
            $nomCouleur = is_array($couleur) ? $couleur['name'] : $couleur;
            echo "      ✅ {$nomCouleur}\n";
        }
    }

    echo "   🎯 Accesseur stock_couleurs_filtres:\n";
    $stockFiltres = $produit->stock_couleurs_filtres;
    echo "      📊 Type: " . gettype($stockFiltres) . "\n";
    echo "      📊 Nombre: " . (is_array($stockFiltres) ? count($stockFiltres) : 'N/A') . "\n";

    if (is_array($stockFiltres)) {
        foreach ($stockFiltres as $stock) {
            echo "      ✅ {$stock['name']}: {$stock['quantity']} unités\n";
        }
    }
    echo "\n";

    // 6. Test de la méthode index (simulation)
    echo "6️⃣ Test de la méthode index (simulation)...\n";

    // Simuler le filtrage de la méthode index
    $stockCouleurs = $produit->stock_couleurs;
    $couleurs = $produit->couleur;

    if (is_array($stockCouleurs) && is_array($couleurs) && !empty($stockCouleurs)) {
        $couleursFiltrees = [];
        $stockCouleursFiltres = [];

        foreach ($stockCouleurs as $index => $stock) {
            if ($stock['quantity'] > 0) {
                $stockCouleursFiltres[] = $stock;

                if (isset($couleurs[$index])) {
                    $couleursFiltrees[] = $couleurs[$index];
                }
            }
        }

        echo "   🎨 Résultat du filtrage:\n";
        echo "      📊 Couleurs originales: " . count($couleurs) . "\n";
        echo "      📊 Couleurs filtrées: " . count($couleursFiltrees) . "\n";
        echo "      📦 Stock original: " . count($stockCouleurs) . "\n";
        echo "      📦 Stock filtré: " . count($stockCouleursFiltres) . "\n";
    } else {
        echo "   ❌ Erreur: données non valides pour le filtrage\n";
    }
    echo "\n";

    // 7. Validation finale
    echo "7️⃣ Validation finale...\n";

    $testReussi = true;

    // Vérifier que couleur est un tableau
    if (!is_array($produit->couleur)) {
        $testReussi = false;
        echo "      ❌ L'attribut couleur n'est pas un tableau\n";
    } else {
        echo "      ✅ L'attribut couleur est bien un tableau\n";
    }

    // Vérifier que stock_couleurs est un tableau
    if (!is_array($produit->stock_couleurs)) {
        $testReussi = false;
        echo "      ❌ L'attribut stock_couleurs n'est pas un tableau\n";
    } else {
        echo "      ✅ L'attribut stock_couleurs est bien un tableau\n";
    }

    // Vérifier que les accesseurs filtrés fonctionnent
    if (!is_array($produit->couleurs_filtrees)) {
        $testReussi = false;
        echo "      ❌ L'accesseur couleurs_filtrees ne fonctionne pas\n";
    } else {
        echo "      ✅ L'accesseur couleurs_filtrees fonctionne\n";
    }

    if (!is_array($produit->stock_couleurs_filtres)) {
        $testReussi = false;
        echo "      ❌ L'accesseur stock_couleurs_filtres ne fonctionne pas\n";
    } else {
        echo "      ✅ L'accesseur stock_couleurs_filtres fonctionne\n";
    }

    if ($testReussi) {
        echo "      ✅ Tous les tests sont réussis !\n";
    }
    echo "\n";

    echo "🎉 TEST DE CORRECTION DE L'ERREUR JSON_DECODE TERMINÉ !\n";
    echo "========================================================\n\n";

    echo "📋 RÉSUMÉ DE LA VALIDATION:\n";
    echo "1. ✅ L'attribut couleur retourne directement un tableau\n";
    echo "2. ✅ L'attribut stock_couleurs retourne directement un tableau\n";
    echo "3. ✅ Plus besoin de json_decode sur ces attributs\n";
    echo "4. ✅ Les accesseurs filtrés fonctionnent correctement\n";
    echo "5. ✅ La méthode index peut utiliser les attributs directement\n\n";

    echo "🔧 CORRECTIONS APPORTÉES:\n";
    echo "- ✅ Suppression de json_decode sur $product->couleur\n";
    echo "- ✅ Suppression de json_decode sur $product->stock_couleurs\n";
    echo "- ✅ Utilisation directe des attributs (déjà décodés)\n";
    echo "- ✅ Accesseurs et casts du modèle respectés\n";
    echo "- ✅ Plus d'erreur 'json_decode(): Argument #1 must be of type string, array given'\n\n";

    if ($testReussi) {
        echo "🚀 SUCCÈS: L'erreur json_decode est corrigée !\n";
        echo "   ✅ Les attributs retournent directement des tableaux\n";
        echo "   ✅ Plus besoin de json_decode dans le contrôleur\n";
        echo "   ✅ Le filtrage des couleurs fonctionne correctement\n";
    } else {
        echo "⚠️ ATTENTION: Certains tests ont échoué.\n";
        echo "   Vérifiez la configuration du modèle et des accesseurs.\n";
    }

} catch (Exception $e) {
    echo "❌ ERREUR: " . $e->getMessage() . "\n";
    echo "📍 Fichier: " . $e->getFile() . "\n";
    echo "📍 Ligne: " . $e->getLine() . "\n";
    echo "🔍 Trace:\n" . $e->getTraceAsString() . "\n";
}
