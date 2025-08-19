<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== VÉRIFICATION RELATION PRODUCT_USER ===\n";

$relation = DB::table('product_user')
    ->where('product_id', 8)
    ->where('user_id', 16)
    ->first();

if ($relation) {
    echo "Relation trouvée pour Manteau (ID:8) et Vendeur (ID:16):\n";
    echo "Prix Admin: {$relation->prix_admin}\n";
    echo "Prix Vente: {$relation->prix_vente}\n";
    echo "Visible: {$relation->visible}\n";
} else {
    echo "Relation NON trouvée!\n";
}

echo "\n=== TOUTES LES RELATIONS DU VENDEUR 16 ===\n";
$allRelations = DB::table('product_user')->where('user_id', 16)->get();
foreach ($allRelations as $rel) {
    echo "Product {$rel->product_id}: Admin={$rel->prix_admin}, Vente={$rel->prix_vente}\n";
}

echo "\n=== VÉRIFICATION PRODUIT 8 ===\n";
$product = DB::table('produits')->where('id', 8)->first();
if ($product) {
    echo "Produit 8 (Manteau):\n";
    echo "Nom: {$product->name}\n";
    echo "Prix Admin (produit): {$product->prix_admin}\n";
    echo "Prix Vente (produit): {$product->prix_vente}\n";
} else {
    echo "Produit 8 non trouvé!\n";
}
