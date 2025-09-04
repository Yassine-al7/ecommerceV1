<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Test Laravel<br>";

try {
    require_once 'vendor/autoload.php';
    echo "✅ Autoloader OK<br>";
    
    $app = require_once 'bootstrap/app.php';
    echo "✅ Laravel OK<br>";
    
    echo "✅ Tout fonctionne !<br>";
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "<br>";
    echo "Fichier: " . $e->getFile() . "<br>";
    echo "Ligne: " . $e->getLine() . "<br>";
}
?>