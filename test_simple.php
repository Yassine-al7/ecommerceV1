<?php

echo "Test du système de commande\n";
echo "==========================\n\n";

// Test de la configuration
echo "1. Test de la configuration:\n";
if (file_exists('config/delivery.php')) {
    echo "✅ Fichier de configuration delivery.php existe\n";
} else {
    echo "❌ Fichier de configuration delivery.php manquant\n";
}

// Test des contrôleurs
echo "\n2. Test des contrôleurs:\n";
if (file_exists('app/Http/Controllers/Seller/OrderController.php')) {
    echo "✅ Contrôleur Seller OrderController existe\n";
} else {
    echo "❌ Contrôleur Seller OrderController manquant\n";
}

if (file_exists('app/Http/Controllers/Admin/OrderController.php')) {
    echo "✅ Contrôleur Admin OrderController existe\n";
} else {
    echo "❌ Contrôleur Admin OrderController manquant\n";
}

// Test du trait
echo "\n3. Test du trait:\n";
if (file_exists('app/Traits/GeneratesOrderReferences.php')) {
    echo "✅ Trait GeneratesOrderReferences existe\n";
} else {
    echo "❌ Trait GeneratesOrderReferences manquant\n";
}

// Test des vues
echo "\n4. Test des vues:\n";
if (file_exists('resources/views/seller/order_form.blade.php')) {
    echo "✅ Vue seller order_form.blade.php existe\n";
} else {
    echo "❌ Vue seller order_form.blade.php manquant\n";
}

if (file_exists('resources/views/admin/order_form.blade.php')) {
    echo "✅ Vue admin order_form.blade.php existe\n";
} else {
    echo "❌ Vue admin order_form.blade.php manquant\n";
}

// Test des routes
echo "\n5. Test des routes:\n";
if (file_exists('routes/web.php')) {
    echo "✅ Fichier de routes web.php existe\n";
    
    $webContent = file_get_contents('routes/web.php');
    if (strpos($webContent, 'seller.orders') !== false) {
        echo "✅ Routes seller.orders trouvées dans web.php\n";
    } else {
        echo "❌ Routes seller.orders manquantes dans web.php\n";
    }
} else {
    echo "❌ Fichier de routes web.php manquant\n";
}

echo "\nTest terminé !\n";
