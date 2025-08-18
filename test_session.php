<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Test de Session ===\n\n";

// Tester la session
session(['test_key' => 'test_value']);

echo "Session créée avec 'test_key' = 'test_value'\n";
echo "Vérification: " . (session('test_key') === 'test_value' ? 'OK' : 'ERREUR') . "\n";

// Simuler pending_user
session(['pending_user' => [
    'name' => 'Test User',
    'email' => 'test@example.com',
    'password' => 'password123',
    'role' => 'seller',
    'numero_telephone' => '0612345678',
    'store_name' => 'Test Store',
    'rib' => '123456789',
]]);

echo "\nSession pending_user créée\n";
echo "Vérification pending_user: " . (session('pending_user') ? 'OK' : 'ERREUR') . "\n";

if (session('pending_user')) {
    $pending = session('pending_user');
    echo "- Nom: {$pending['name']}\n";
    echo "- Email: {$pending['email']}\n";
    echo "- Role: {$pending['role']}\n";
}

// Nettoyer
session()->forget('test_key');
session()->forget('pending_user');

echo "\nSessions nettoyées\n";
echo "Test terminé\n";
