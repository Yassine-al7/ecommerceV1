<?php
/**
 * Test de diagnostic des couleurs pour les vendeurs
 *
 * Ce fichier diagnostique pourquoi les couleurs ne s'affichent pas
 * dans le formulaire de création de commandes des vendeurs
 */

require_once 'vendor/autoload.php';

use App\Models\Product;
use App\Models\Category;
use App\Models\User;

// Simuler l'environnement Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 DIAGNOSTIC DES COULEURS POUR LES VENDEURS\n";
echo "============================================\n\n";

try {
    // 1. Créer une catégorie
    echo "1️⃣ Création de la catégorie 'Djellaba'...\n";
    $category = Category::firstOrCreate(
        ['name' => 'Djellaba'],
        ['slug' => 'djellaba', 'color' => '#3B82F6']
    );
    echo "   ✅ Catégorie créée: {$category->name}\n\n";

    // 2. Créer un vendeur
    echo "2️⃣ Création d'un vendeur de test...\n";
    $vendeur = User::firstOrCreate(
        ['email' => 'vendeur.djellaba@example.com'],
        [
            'name' => 'Vendeur Djellaba',
            'password' => bcrypt('password'),
            'role' => 'seller'
        ]
    );
    echo "   ✅ Vendeur créé: {$vendeur->name} (ID: {$vendeur->id})\n\n";

    // 3. Créer un djellaba avec des couleurs et stocks
    echo "3️⃣ Création du djellaba 'DJELLABA TEST'...\n";

    $couleursInitiales = [
        ['name' => 'Rouge', 'hex' => '#ff0000'],
        ['name' => 'Bleu', 'hex' => '#0000ff'],
        ['name' => 'Vert', 'hex' => '#00ff00']
    ];

    $stockInitial = [
        ['name' => 'Rouge', 'quantity' => 25],
        ['name' => 'Bleu', 'quantity' => 30],
        ['name' => 'Vert', 'quantity' => 20]
    ];

    $djellaba = Product::firstOrCreate(
        ['name' => 'DJELLABA TEST'],
        [
            'categorie_id' => $category->id,
            'couleur' => json_encode($couleursInitiales),
            'stock_couleurs' => json_encode($stockInitial),
            'tailles' => json_encode(['S', 'M', 'L', 'XL']),
            'prix_admin' => 150.00,
            'prix_vente' => 200.00,
            'quantite_stock' => 75, // 25 + 30 + 20
            'image' => '/storage/products/default-product.svg'
        ]
    );

    echo "   ✅ Djellaba créé: {$djellaba->name}\n";
    echo "   🎨 Couleurs initiales: " . count($couleursInitiales) . "\n";
    echo "   📊 Stock initial: " . count($stockInitial) . "\n";
    echo "   🔢 Stock total: {$djellaba->quantite_stock} unités\n\n";

    // 4. Assigner le djellaba au vendeur
    echo "4️⃣ Assignment du djellaba au vendeur...\n";

    $djellaba->assignedUsers()->syncWithoutDetaching([
        $vendeur->id => [
            'prix_admin' => $djellaba->prix_admin,
            'prix_vente' => $djellaba->prix_vente,
            'visible' => true
        ]
    ]);

    echo "   ✅ Djellaba assigné au vendeur\n\n";

    // 5. Test des attributs du modèle
    echo "5️⃣ Test des attributs du modèle Product...\n";

    echo "   🎯 Test de l'attribut couleur:\n";
    $couleurs = $djellaba->couleur;
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
    $stockCouleurs = $djellaba->stock_couleurs;
    echo "      📊 Type: " . gettype($stockCouleurs) . "\n";
    echo "      📊 Nombre: " . (is_array($stockCouleurs) ? count($stockCouleurs) : 'N/A') . "\n";

    if (is_array($stockCouleurs)) {
        foreach ($stockCouleurs as $index => $stock) {
            echo "      ✅ {$index}: {$stock['name']} = {$stock['quantity']} unités\n";
        }
    }
    echo "\n";

    // 6. Simuler la requête du contrôleur vendeur
    echo "6️⃣ Simulation de la requête du contrôleur vendeur...\n";

    // Simuler exactement la requête du contrôleur
    $products = $vendeur->assignedProducts()
        ->select('produits.id', 'produits.name', 'produits.tailles', 'produits.image', 'produits.prix_admin', 'produits.couleur', 'produits.stock_couleurs', 'produits.categorie_id', 'produits.quantite_stock')
        ->with('category:id,name,slug')
        ->get();

    echo "   🔄 Produits assignés récupérés: " . $products->count() . "\n";

    foreach ($products as $product) {
        echo "   🔍 Produit: {$product->name} (ID: {$product->id})\n";
        echo "      📊 Couleur brute: " . $product->getRawOriginal('couleur') . "\n";
        echo "      📊 Stock couleurs brute: " . $product->getRawOriginal('stock_couleurs') . "\n";
        echo "      📊 Couleur accesseur: " . json_encode($product->couleur) . "\n";
        echo "      📊 Stock couleurs accesseur: " . json_encode($product->stock_couleurs) . "\n";
        echo "      📊 Tailles: " . json_encode($product->tailles) . "\n";
        echo "      📊 Quantité stock: " . ($product->quantite_stock ?? 'N/A') . "\n";
    }
    echo "\n";

    // 7. Test du filtrage des couleurs
    echo "7️⃣ Test du filtrage des couleurs...\n";

    foreach ($products as $product) {
        echo "   🔍 Traitement du produit: {$product->name}\n";

        // 🆕 FILTRER LES COULEURS AVEC STOCK ≤ 0
        if (!empty($product->stock_couleurs)) {
            // Les accesseurs du modèle ont déjà décodé les données en tableaux
            $stockCouleurs = $product->stock_couleurs;
            $couleurs = $product->couleur;

            echo "      📊 Stock couleurs (type): " . gettype($stockCouleurs) . "\n";
            echo "      📊 Couleurs (type): " . gettype($couleurs) . "\n";
            echo "      📊 Stock couleurs (contenu): " . json_encode($stockCouleurs) . "\n";
            echo "      📊 Couleurs (contenu): " . json_encode($couleurs) . "\n";

            if (is_array($stockCouleurs) && is_array($couleurs)) {
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
                    } else {
                        echo "      🗑️ Couleur filtrée: {$stock['name']} (stock: {$stock['quantity']})\n";
                    }
                }

                // Mettre à jour les attributs du produit pour l'affichage
                $product->couleur = $couleursFiltrees;
                $product->stock_couleurs = $stockCouleursFiltres;

                echo "      🎨 Résultat du filtrage:", "\n";
                echo "         📊 Couleurs originales: " . count($couleurs) . "\n";
                echo "         📊 Couleurs filtrées: " . count($couleursFiltrees) . "\n";
                echo "         📦 Stock original: " . count($stockCouleurs) . "\n";
                echo "         📦 Stock filtré: " . count($stockCouleursFiltres) . "\n";
            } else {
                echo "      ❌ Erreur: données non valides pour le filtrage\n";
                echo "         stockCouleurs est un tableau: " . (is_array($stockCouleurs) ? 'OUI' : 'NON') . "\n";
                echo "         couleurs est un tableau: " . (is_array($couleurs) ? 'OUI' : 'NON') . "\n";
            }
        } else {
            echo "      ⚠️ Pas de stock_couleurs pour ce produit\n";
        }
        echo "\n";
    }

    // 8. Test final des données filtrées
    echo "8️⃣ Test final des données filtrées...\n";

    foreach ($products as $product) {
        echo "   🎯 Produit final: {$product->name}\n";

        $couleurs = $product->couleur;
        $stockCouleurs = $product->stock_couleurs;

        echo "      🎨 Couleurs finales (" . (is_array($couleurs) ? count($couleurs) : 'N/A') . "):\n";
        if (is_array($couleurs)) {
            foreach ($couleurs as $couleur) {
                $nomCouleur = is_array($couleur) ? $couleur['name'] : $couleur;
                echo "         ✅ {$nomCouleur}\n";
            }
        } else {
            echo "         ❌ Couleurs non disponibles\n";
        }

        echo "      📊 Stock final (" . (is_array($stockCouleurs) ? count($stockCouleurs) : 'N/A') . "):\n";
        if (is_array($stockCouleurs)) {
            foreach ($stockCouleurs as $stock) {
                echo "         ✅ {$stock['name']}: {$stock['quantity']} unités\n";
            }
        } else {
            echo "         ❌ Stock non disponible\n";
        }
        echo "\n";
    }

    // 9. Diagnostic des problèmes potentiels
    echo "9️⃣ Diagnostic des problèmes potentiels...\n";

    foreach ($products as $product) {
        echo "   🔍 Diagnostic pour {$product->name}:\n";

        // Vérifier les casts du modèle
        $casts = $product->getCasts();
        echo "      📋 Casts du modèle:\n";
        foreach ($casts as $attribute => $cast) {
            echo "         - {$attribute}: {$cast}\n";
        }

        // Vérifier les accesseurs
        $couleurAccesseur = $product->couleur;
        $stockAccesseur = $product->stock_couleurs;

        echo "      🔧 Accesseurs:\n";
        echo "         - couleur: " . gettype($couleurAccesseur) . " = " . json_encode($couleurAccesseur) . "\n";
        echo "         - stock_couleurs: " . gettype($stockAccesseur) . " = " . json_encode($stockAccesseur) . "\n";

        // Vérifier les valeurs brutes
        $couleurBrute = $product->getRawOriginal('couleur');
        $stockBrute = $product->getRawOriginal('stock_couleurs');

        echo "      📊 Valeurs brutes:\n";
        echo "         - couleur: " . gettype($couleurBrute) . " = " . $couleurBrute . "\n";
        echo "         - stock_couleurs: " . gettype($stockBrute) . " = " . $stockBrute . "\n";
        echo "\n";
    }

    // 10. Validation finale
    echo "🔟 Validation finale...\n";

    $problemeIdentifie = false;

    foreach ($products as $product) {
        $couleurs = $product->couleur;
        $stockCouleurs = $product->stock_couleurs;

        if (!is_array($couleurs) || empty($couleurs)) {
            $problemeIdentifie = true;
            echo "   ❌ Problème: couleurs non disponibles pour {$product->name}\n";
        }

        if (!is_array($stockCouleurs) || empty($stockCouleurs)) {
            $problemeIdentifie = true;
            echo "   ❌ Problème: stock couleurs non disponible pour {$product->name}\n";
        }

        if (is_array($couleurs) && is_array($stockCouleurs) && count($couleurs) !== count($stockCouleurs)) {
            $problemeIdentifie = true;
            echo "   ❌ Problème: incohérence couleurs/stocks pour {$product->name}\n";
        }
    }

    if (!$problemeIdentifie) {
        echo "   ✅ Aucun problème identifié - les couleurs devraient s'afficher\n";
    }
    echo "\n";

    echo "🎉 DIAGNOSTIC TERMINÉ !\n";
    echo "=======================\n\n";

    if ($problemeIdentifie) {
        echo "⚠️ PROBLÈMES IDENTIFIÉS:\n";
        echo "   - Vérifiez les casts du modèle Product\n";
        echo "   - Vérifiez les accesseurs du modèle\n";
        echo "   - Vérifiez la structure des données en base\n";
    } else {
        echo "✅ DIAGNOSTIC RÉUSSI:\n";
        echo "   - Les couleurs devraient s'afficher correctement\n";
        echo "   - Vérifiez le JavaScript de la vue\n";
        echo "   - Vérifiez la console du navigateur\n";
    }

} catch (Exception $e) {
    echo "❌ ERREUR: " . $e->getMessage() . "\n";
    echo "📍 Fichier: " . $e->getFile() . "\n";
    echo "📍 Ligne: " . $e->getLine() . "\n";
    echo "🔍 Trace:\n" . $e->getTraceAsString() . "\n";
}
