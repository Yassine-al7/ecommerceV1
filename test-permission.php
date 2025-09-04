<?php
echo "<h1>Test des Permissions</h1>";

$dirs = ['app', 'bootstrap', 'config', 'database', 'resources', 'routes', 'storage', 'vendor'];
$files = ['.env', 'composer.json', 'artisan'];

echo "<h2>Dossiers :</h2>";
foreach($dirs as $dir) {
    if (is_dir($dir)) {
        $perms = substr(sprintf('%o', fileperms($dir)), -4);
        echo "$dir : ✅ (permissions: $perms)<br>";
    } else {
        echo "$dir : ❌ Manquant<br>";
    }
}

echo "<h2>Fichiers :</h2>";
foreach($files as $file) {
    if (file_exists($file)) {
        $perms = substr(sprintf('%o', fileperms($file)), -4);
        echo "$file : ✅ (permissions: $perms)<br>";
    } else {
        echo "$file : ❌ Manquant<br>";
    }
}
?>