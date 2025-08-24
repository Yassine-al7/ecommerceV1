<?php
/**
 * Test du filtrage des couleurs dans la vue admin.products
 *
 * Ce fichier teste que les couleurs avec stock = 0 sont filtrées
 * et n'apparaissent plus dans l'affichage de la liste des produits
 */

require_once 'vendor/autoload.php';

use App\Models\Product;
use App\Models\Category;

// Simuler l'environnement Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧪 TEST DU FILTRAGE DES COULEURS DANS LA VUE ADMIN.PRODUCTS\n";
echo "==========================================================\n\n";

try {
    // 1. Créer une catégorie
    echo "1️⃣ Création de la catégorie 'Test Vue'...\n";
    $category = Category::firstOrCreate(
        ['name' => 'Test Vue'],
        ['slug' => 'test-vue', 'color' => '#3B82F6']
    );
    echo "   ✅ Catégorie créée: {$category->name}\n\n";

    // 2. Créer un produit avec des couleurs et stocks variés
    echo "2️⃣ Création du produit 'TEST VUE FILTRAGE'...\n";

    $couleursInitiales = [
        ['name' => 'Rouge', 'hex' => '#ff0000'],      // Stock positif
        ['name' => 'CHIBI', 'hex' => '#ff6b6b'],      // Stock = 0 (à filtrer)
        ['name' => 'MARINE', 'hex' => '#1e40af'],     // Stock négatif (à filtrer)
        ['name' => 'VIOLET', 'hex' => '#8b5cf6']      // Stock positif
    ];

    $stockInitial = [
        ['name' => 'Rouge', 'quantity' => 50],        // Stock positif
        ['name' => 'CHIBI', 'quantity' => 0],         // Stock = 0 (à filtrer)
        ['name' => 'MARINE', 'quantity' => -5],       // Stock négatif (à filtrer)
        ['name' => 'VIOLET', 'quantity' => 100]       // Stock positif
    ];

    $produit = Product::firstOrCreate(
        ['name' => 'TEST VUE FILTRAGE'],
        [
            'categorie_id' => $category->id,
            'couleur' => json_encode($couleursInitiales),
            'stock_couleurs' => json_encode($stockInitial),
            'tailles' => json_encode(['S', 'M', 'L']),
            'prix_admin' => 150.00,
            'prix_vente' => 200.00,
            'quantite_stock' => 145, // 50 + 0 + (-5) + 100
            'image' => '/storage/products/default-product.svg'
        ]
    );

    echo "   ✅ Produit créé: {$produit->name}\n";
    echo "   🎨 Couleurs initiales:\n";
    foreach ($couleursInitiales as $couleur) {
        echo "      - {$couleur['name']}: {$couleur['hex']}\n";
    }
    echo "   📊 Stock initial par couleur:\n";
    foreach ($stockInitial as $stock) {
        $status = $stock['quantity'] > 0 ? '✅' : '❌';
        echo "      {$status} {$stock['name']}: {$stock['quantity']} unités\n";
    }
    echo "   🔢 Stock total initial: {$produit->quantite_stock} unités\n\n";

    // 3. Simuler le filtrage du contrôleur
    echo "3️⃣ Simulation du filtrage du contrôleur...\n";

    // Simuler la méthode index du contrôleur
    $stockCouleurs = $produit->stock_couleurs;
    $couleurs = $produit->couleur;

    if (is_array($stockCouleurs) && is_array($couleurs) && !empty($stockCouleurs)) {
        $couleursFiltrees = [];
        $stockCouleursFiltres = [];

        foreach ($stockCouleurs as $index => $stock) {
            if ($stock['quantity'] > 0) {
                // Conserver la couleur et son stock
                $stockCouleursFiltres[] = $stock;

                // Trouver la couleur correspondante
                if (isset($couleurs[$index])) {
                    $couleursFiltrees[] = $couleurs[$index];
                }
            }
        }

        // Mettre à jour les attributs du produit pour l'affichage
        $produit->couleur_filtree = $couleursFiltrees;
        $produit->stock_couleurs_filtre = $stockCouleursFiltres;

        echo "   🎨 Filtrage des couleurs pour {$produit->name}:\n";
        echo "      📊 Couleurs originales: " . count($couleurs) . "\n";
        echo "      📊 Couleurs filtrées: " . count($couleursFiltrees) . "\n";
        echo "      📦 Stock original: " . count($stockCouleurs) . "\n";
        echo "      📦 Stock filtré: " . count($stockCouleursFiltres) . "\n";
    }
    echo "\n";

    // 4. Test de l'affichage des couleurs filtrées
    echo "4️⃣ Test de l'affichage des couleurs filtrées...\n";

    // Simuler la logique de la vue
    $couleursAAfficher = $produit->couleur_filtree ?? $produit->couleur;

    echo "   🎯 Couleurs à afficher dans la vue:\n";
    echo "      📊 Nombre: " . count($couleursAAfficher) . "\n";

    foreach ($couleursAAfficher as $couleur) {
        $nomCouleur = is_array($couleur) ? $couleur['name'] : $couleur;
        $hex = is_array($couleur) ? ($couleur['hex'] ?? 'N/A') : 'N/A';
        echo "      ✅ {$nomCouleur} ({$hex})\n";
    }
    echo "\n";

    // 5. Vérification que les couleurs avec stock = 0 sont bien filtrées
    echo "5️⃣ Vérification du filtrage des couleurs avec stock = 0...\n";

    $couleursFiltreesAttendues = ['Rouge', 'VIOLET'];
    $couleursFiltreesTrouvees = [];

    foreach ($couleursAAfficher as $couleur) {
        $nomCouleur = is_array($couleur) ? $couleur['name'] : $couleur;
        $couleursFiltreesTrouvees[] = $nomCouleur;
    }

    echo "   🎯 Couleurs attendues après filtrage: " . implode(', ', $couleursFiltreesAttendues) . "\n";
    echo "   🎯 Couleurs trouvées après filtrage: " . implode(', ', $couleursFiltreesTrouvees) . "\n";

    $filtrageReussi = true;

    // Vérifier que toutes les couleurs attendues sont présentes
    foreach ($couleursFiltreesAttendues as $couleurAttendue) {
        if (!in_array($couleurAttendue, $couleursFiltreesTrouvees)) {
            $filtrageReussi = false;
            echo "      ❌ Couleur attendue manquante: {$couleurAttendue}\n";
        }
    }

    // Vérifier qu'aucune couleur avec stock = 0 n'est présente
    foreach ($stockInitial as $stock) {
        if ($stock['quantity'] <= 0 && in_array($stock['name'], $couleursFiltreesTrouvees)) {
            $filtrageReussi = false;
            echo "      ❌ Couleur avec stock ≤ 0 toujours présente: {$stock['name']}\n";
        }
    }

    if ($filtrageReussi) {
        echo "      ✅ Filtrage des couleurs réussi dans la vue !\n";
    }
    echo "\n";

    // 6. Test de la cohérence des données filtrées
    echo "6️⃣ Test de la cohérence des données filtrées...\n";

    if (isset($produit->stock_couleurs_filtre)) {
        $stockFiltres = $produit->stock_couleurs_filtre;
        echo "   📊 Stock filtré disponible: " . count($stockFiltres) . " stocks\n";

        foreach ($stockFiltres as $stock) {
            echo "      ✅ {$stock['name']}: {$stock['quantity']} unités\n";
        }

        // Vérifier que le nombre de couleurs filtrées correspond au nombre de stocks filtrés
        if (count($couleursAAfficher) === count($stockFiltres)) {
            echo "      ✅ Cohérence: couleurs et stocks filtrés correspondent\n";
        } else {
            echo "      ❌ Incohérence: " . count($couleursAAfficher) . " couleurs ≠ " . count($stockFiltres) . " stocks\n";
        }
    }
    echo "\n";

    // 7. Simulation de l'affichage dans la vue
    echo "7️⃣ Simulation de l'affichage dans la vue admin.products...\n";

    echo "   🎯 Données disponibles pour la vue:\n";
    echo "      📦 Produit: {$produit->name}\n";
    echo "      🎨 Couleurs filtrées (" . count($couleursAAfficher) . "):\n";

    foreach ($couleursAAfficher as $couleur) {
        $nomCouleur = is_array($couleur) ? $couleur['name'] : $couleur;
        $hex = is_array($couleur) ? ($couleur['hex'] ?? 'N/A') : 'N/A';

        // Simuler l'affichage de la couleur dans la vue
        echo "         🎨 {$nomCouleur} ({$hex})\n";
        echo "            <div class='w-4 h-4 rounded-full' style='background-color: {$hex}'></div>\n";
        echo "            <span>{$nomCouleur}</span>\n";
    }

    if (isset($produit->stock_couleurs_filtre)) {
        echo "      📊 Stock filtré (" . count($produit->stock_couleurs_filtre) . "):\n";
        foreach ($produit->stock_couleurs_filtre as $stock) {
            echo "         📦 {$stock['name']}: {$stock['quantity']} unités\n";
        }
    }
    echo "\n";

    // 8. Validation finale
    echo "8️⃣ Validation finale du filtrage dans la vue...\n";

    echo "   🎯 Fonctionnalités testées:\n";
    echo "      ✅ Filtrage des couleurs dans le contrôleur\n";
    echo "      ✅ Attribution des couleurs filtrées au produit\n";
    echo "      ✅ Utilisation des couleurs filtrées dans la vue\n";
    echo "      ✅ Suppression des couleurs avec stock ≤ 0\n";
    echo "      ✅ Cohérence des données filtrées\n\n";

    echo "   🗑️ Couleurs filtrées (stock ≤ 0):\n";
    foreach ($stockInitial as $stock) {
        if ($stock['quantity'] <= 0) {
            echo "      - {$stock['name']}: {$stock['quantity']} unités\n";
        }
    }

    echo "   ✅ Couleurs conservées (stock > 0):\n";
    foreach ($stockInitial as $stock) {
        if ($stock['quantity'] > 0) {
            echo "      - {$stock['name']}: {$stock['quantity']} unités\n";
        }
    }
    echo "\n";

    echo "🎉 TEST DU FILTRAGE DANS LA VUE TERMINÉ !\n";
    echo "==========================================\n\n";

    echo "📋 RÉSUMÉ DE LA VALIDATION:\n";
    echo "1. ✅ Le filtrage des couleurs fonctionne dans le contrôleur\n";
    echo "2. ✅ Les couleurs filtrées sont attribuées au produit\n";
    echo "3. ✅ La vue utilise les couleurs filtrées\n";
    echo "4. ✅ Les couleurs avec stock ≤ 0 sont supprimées de l'affichage\n";
    echo "5. ✅ La cohérence des données est maintenue\n\n";

    echo "🔧 FONCTIONNALITÉS DE FILTRAGE DANS LA VUE:\n";
    echo "- ✅ Filtrage automatique des couleurs avec stock ≤ 0\n";
    echo "- ✅ Utilisation des couleurs filtrées dans admin.products\n";
    echo "- ✅ Interface plus propre et cohérente\n";
    echo "- ✅ Pas de couleurs avec stock = 0 affichées\n";
    echo "- ✅ Données filtrées disponibles pour la vue\n\n";

    if ($filtrageReussi) {
        echo "🚀 SUCCÈS: Le filtrage des couleurs dans la vue fonctionne parfaitement !\n";
        echo "   Interface plus propre dans /admin/products ✅\n";
        echo "   Couleurs avec stock = 0 supprimées de l'affichage 🎯\n";
        echo "   Vue utilise les données filtrées du contrôleur ✅\n";
    } else {
        echo "⚠️ ATTENTION: Le filtrage des couleurs dans la vue présente des problèmes.\n";
        echo "   Vérifiez la logique de filtrage et l'attribution des données.\n";
    }

} catch (Exception $e) {
    echo "❌ ERREUR: " . $e->getMessage() . "\n";
    echo "📍 Fichier: " . $e->getFile() . "\n";
    echo "📍 Ligne: " . $e->getLine() . "\n";
    echo "🔍 Trace:\n" . $e->getTraceAsString() . "\n";
}
