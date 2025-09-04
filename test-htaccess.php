<?php
echo "<h1>Test .htaccess</h1>";

echo "<h2>1. Vérification du fichier .htaccess</h2>";
if (file_exists('.htaccess')) {
    echo "✅ Fichier .htaccess trouvé<br>";
    $content = file_get_contents('.htaccess');
    echo "<h3>Contenu actuel :</h3>";
    echo "<pre>" . htmlspecialchars($content) . "</pre>";
} else {
    echo "❌ Fichier .htaccess manquant<br>";
}

echo "<h2>2. Test de mod_rewrite</h2>";
if (function_exists('apache_get_modules')) {
    $modules = apache_get_modules();
    if (in_array('mod_rewrite', $modules)) {
        echo "✅ mod_rewrite activé<br>";
    } else {
        echo "❌ mod_rewrite non activé<br>";
    }
} else {
    echo "⚠️ Impossible de vérifier mod_rewrite<br>";
}

echo "<h2>3. Test de réécriture d'URL</h2>";
echo "URL actuelle : " . $_SERVER['REQUEST_URI'] . "<br>";
echo "Script : " . $_SERVER['SCRIPT_NAME'] . "<br>";
echo "Query String : " . $_SERVER['QUERY_STRING'] . "<br>";
?>