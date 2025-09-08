<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Product;

echo "=== DIAGNOSTIC DES IMAGES ===\n\n";

// Récupérer un produit
$product = Product::first();

if (!$product) {
    echo "❌ Aucun produit trouvé\n";
    exit;
}

echo "📦 Produit: {$product->name} (ID: {$product->id})\n";
echo "🖼️ Image path: {$product->image}\n\n";

// Tester les différentes URLs possibles
$imagePath = $product->image;
$src = trim($imagePath ?? '', '/');

echo "=== TESTS D'URLS ===\n";

// URL 1: Chemin direct actuel
$url1 = '/storage/app/public/' . ltrim($imagePath, '/');
echo "1. URL actuelle: {$url1}\n";

// URL 2: Chemin simple
$url2 = '/' . ltrim($imagePath, '/');
echo "2. URL simple: {$url2}\n";

// URL 3: Avec asset()
$url3 = asset(ltrim($imagePath, '/'));
echo "3. URL avec asset(): {$url3}\n";

// URL 4: Chemin complet vers public_html
$url4 = '/storage/' . ltrim($imagePath, '/');
echo "4. URL /storage/: {$url4}\n";

echo "\n=== VÉRIFICATION DES FICHIERS ===\n";

// Vérifier si le fichier existe dans storage/app/public
$storagePath = storage_path('app/public/' . ltrim($imagePath, '/'));
echo "📁 Chemin storage: {$storagePath}\n";
echo "✅ Fichier existe: " . (file_exists($storagePath) ? 'OUI' : 'NON') . "\n";

// Vérifier si le fichier existe dans public_html/storage
$publicPath = public_path('storage/' . ltrim($imagePath, '/'));
echo "📁 Chemin public: {$publicPath}\n";
echo "✅ Fichier existe: " . (file_exists($publicPath) ? 'OUI' : 'NON') . "\n";

// Vérifier si le fichier existe dans public_html/storage/app/public
$publicAppPath = public_path('storage/app/public/' . ltrim($imagePath, '/'));
echo "📁 Chemin public/app/public: {$publicAppPath}\n";
echo "✅ Fichier existe: " . (file_exists($publicAppPath) ? 'OUI' : 'NON') . "\n";

echo "\n=== STRUCTURE DES DOSSIERS ===\n";
echo "📁 storage/app/public/products/: " . (is_dir(storage_path('app/public/products')) ? 'EXISTE' : 'N\'EXISTE PAS') . "\n";
echo "📁 public_html/storage/: " . (is_dir(public_path('storage')) ? 'EXISTE' : 'N\'EXISTE PAS') . "\n";
echo "📁 public_html/storage/app/: " . (is_dir(public_path('storage/app')) ? 'EXISTE' : 'N\'EXISTE PAS') . "\n";
echo "📁 public_html/storage/app/public/: " . (is_dir(public_path('storage/app/public')) ? 'EXISTE' : 'N\'EXISTE PAS') . "\n";

echo "\n=== FICHIERS DANS storage/app/public/products/ ===\n";
$productsDir = storage_path('app/public/products');
if (is_dir($productsDir)) {
    $files = scandir($productsDir);
    $imageFiles = array_filter($files, function($file) {
        return !in_array($file, ['.', '..']) && preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $file);
    });
    echo "Images trouvées: " . count($imageFiles) . "\n";
    foreach (array_slice($imageFiles, 0, 5) as $file) {
        echo "  - {$file}\n";
    }
} else {
    echo "❌ Dossier n'existe pas\n";
}

echo "\n=== FICHIERS DANS public_html/storage/ ===\n";
$publicStorageDir = public_path('storage');
if (is_dir($publicStorageDir)) {
    $files = scandir($publicStorageDir);
    echo "Contenu: " . implode(', ', array_slice($files, 0, 10)) . "\n";
} else {
    echo "❌ Dossier n'existe pas\n";
}
