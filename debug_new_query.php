<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== TEST DE LA NOUVELLE REQUÊTE ===\n\n";

$userId = 16; // Vendeur ID

$products = DB::table('product_user')
    ->join('produits', 'product_user.product_id', '=', 'produits.id')
    ->leftJoin('categories', 'produits.categorie_id', '=', 'categories.id')
    ->where('product_user.user_id', $userId)
    ->select([
        'produits.*',
        'product_user.prix_admin as pivot_prix_admin',
        'product_user.prix_vente as pivot_prix_vente',
        'product_user.visible as pivot_visible',
        'product_user.created_at as pivot_created_at',
        'product_user.updated_at as pivot_updated_at',
        'categories.name as category_name'
    ])
    ->get();

echo "Nombre de produits récupérés: " . $products->count() . "\n\n";

foreach ($products as $product) {
    echo "--- PRODUIT {$product->id} ---\n";
    echo "Nom: {$product->name}\n";
    echo "Catégorie: " . ($product->category_name ?? 'NULL') . "\n";
    echo "Pivot Prix Admin: {$product->pivot_prix_admin}\n";
    echo "Pivot Prix Vente: {$product->pivot_prix_vente}\n";
    echo "Pivot Visible: {$product->pivot_visible}\n";
    echo "Image: " . ($product->image ?? 'NULL') . "\n";
    echo "Couleur: " . ($product->couleur ?? 'NULL') . "\n";
    echo "\n";
}

echo "=== FIN TEST ===\n";
