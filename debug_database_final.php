<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== VÉRIFICATION COMPLÈTE DES DONNÉES ===\n\n";

$userId = 16; // Vendeur ID

echo "1. DONNÉES DE LA TABLE product_user :\n";
$pivotData = DB::table('product_user')->where('user_id', $userId)->get();
foreach ($pivotData as $pivot) {
    echo "Product ID: {$pivot->product_id} | Prix Admin: {$pivot->prix_admin} | Prix Vente: {$pivot->prix_vente}\n";
}

echo "\n2. DONNÉES DE LA TABLE produits :\n";
$productIds = $pivotData->pluck('product_id');
$products = DB::table('produits')->whereIn('id', $productIds)->get();
foreach ($products as $product) {
    echo "ID: {$product->id} | Nom: {$product->name} | Image: {$product->image}\n";
}

echo "\n3. REQUÊTE EXACTE DU CONTRÔLEUR :\n";
$controllerQuery = DB::table('product_user as pu')
    ->join('produits as p', 'pu.product_id', '=', 'p.id')
    ->leftJoin('categories as c', 'p.categorie_id', '=', 'c.id')
    ->where('pu.user_id', $userId)
    ->orderBy('p.id', 'asc')
    ->select([
        'p.id',
        'p.name',
        'p.image',
        'pu.prix_admin',
        'pu.prix_vente',
        'c.name as category_name'
    ])
    ->get();

foreach ($controllerQuery as $result) {
    echo "ID: {$result->id} | Nom: {$result->name} | Prix Admin: {$result->prix_admin} | Image: " . ($result->image ?? 'NULL') . "\n";
}

echo "\n4. VÉRIFICATION S'IL Y A DES DOUBLONS :\n";
$duplicates = DB::table('product_user')
    ->select('product_id', 'user_id', DB::raw('COUNT(*) as count'))
    ->where('user_id', $userId)
    ->groupBy('product_id', 'user_id')
    ->having('count', '>', 1)
    ->get();

if ($duplicates->count() > 0) {
    echo "ATTENTION: Doublons trouvés !\n";
    foreach ($duplicates as $dup) {
        echo "Product ID: {$dup->product_id} | User ID: {$dup->user_id} | Count: {$dup->count}\n";
    }
} else {
    echo "Aucun doublon trouvé.\n";
}

echo "\n=== FIN VÉRIFICATION ===\n";
