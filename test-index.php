<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Test Index.php<br>";

try {
    // Simule l'environnement
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_SERVER['REQUEST_URI'] = '/';
    $_SERVER['HTTP_HOST'] = 'affilook.com';
    $_SERVER['SERVER_NAME'] = 'affilook.com';
    $_SERVER['HTTPS'] = 'on';
    
    echo "✅ Environnement simulé<br>";
    
    // Inclut index.php
    ob_start();
    include 'index.php';
    $output = ob_get_clean();
    
    echo "✅ Index.php exécuté<br>";
    echo "Longueur sortie: " . strlen($output) . " caractères<br>";
    
    if (strlen($output) > 0) {
        echo "✅ Sortie générée<br>";
    } else {
        echo "⚠️ Aucune sortie<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "<br>";
    echo "Fichier: " . $e->getFile() . "<br>";
    echo "Ligne: " . $e->getLine() . "<br>";
}
?>