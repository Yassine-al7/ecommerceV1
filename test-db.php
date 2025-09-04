<?php
echo "<h1>Test Base de Données Corrigé</h1>";

// Configuration correcte de la base de données
$host = 'localhost';
$dbname = 'u550997999_ecommerce';
$username = 'u550997999_ecommerce';
$password = 'T@oufik7';

echo "<h2>1. Test de connexion directe</h2>";
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    echo "✅ Connexion à la base de données réussie<br>";
    
    // Test de requête
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "✅ " . count($tables) . " tables trouvées<br>";
    
    if (count($tables) > 0) {
        echo "<h3>Tables présentes :</h3>";
        foreach($tables as $table) {
            echo "- $table<br>";
        }
    }
    
} catch(PDOException $e) {
    echo "❌ Erreur de connexion : " . $e->getMessage() . "<br>";
}

echo "<h2>2. Test de Laravel avec la bonne configuration</h2>";
try {
    require_once 'vendor/autoload.php';
    $app = require_once 'bootstrap/app.php';
    
    echo "✅ Laravel chargé avec succès<br>";
    
    // Test de la configuration de la base de données
    $config = $app['config']['database.connections.mysql'];
    echo "Host DB : " . $config['host'] . "<br>";
    echo "Database : " . $config['database'] . "<br>";
    echo "Username : " . $config['username'] . "<br>";
    
    // Test de connexion via Laravel
    $pdo = new PDO(
        "mysql:host={$config['host']};dbname={$config['database']}", 
        $config['username'], 
        $config['password']
    );
    echo "✅ Connexion Laravel à la base réussie<br>";
    
} catch (Exception $e) {
    echo "❌ Erreur Laravel : " . $e->getMessage() . "<br>";
}

echo "<h1> Test terminé !</h1>";
?>