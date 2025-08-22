<?php

/**
 * Test du système de stock via Artisan
 *
 * Ce test utilise les commandes Artisan pour vérifier le système
 * sans avoir de problèmes de connexion à la base de données
 */

echo "=== TEST DU SYSTÈME DE STOCK VIA ARTISAN ===\n\n";

try {
    // Test 1: Vérifier l'état des migrations
    echo "1. Vérification des migrations...\n";

    $migrationStatus = shell_exec('php artisan migrate:status 2>&1');
    if (strpos($migrationStatus, 'No pending migrations') !== false) {
        echo "   ✅ Toutes les migrations sont à jour\n";
    } else {
        echo "   ⚠️  Migrations en attente détectées\n";
        echo "   {$migrationStatus}\n";
    }

    // Test 2: Vérifier la configuration de la base de données
    echo "\n2. Vérification de la configuration DB...\n";

    $dbConfig = shell_exec('php artisan config:show database 2>&1');
    if (strpos($dbConfig, 'mysql') !== false || strpos($dbConfig, 'sqlite') !== false) {
        echo "   ✅ Configuration de base de données détectée\n";
    } else {
        echo "   ❌ Configuration de base de données non trouvée\n";
    }

    // Test 3: Vérifier les modèles
    echo "\n3. Vérification des modèles...\n";

    $models = ['Product', 'AdminMessage', 'Category'];
    foreach ($models as $model) {
        $modelPath = "app/Models/{$model}.php";
        if (file_exists($modelPath)) {
            echo "   ✅ Modèle {$model} trouvé\n";
        } else {
            echo "   ❌ Modèle {$model} manquant\n";
        }
    }

    // Test 4: Vérifier les vues
    echo "\n4. Vérification des vues...\n";

    $views = [
        'admin/stock.blade.php',
        'admin/products.blade.php',
        'admin/messages/index.blade.php'
    ];

    foreach ($views as $view) {
        $viewPath = "resources/views/{$view}";
        if (file_exists($viewPath)) {
            echo "   ✅ Vue {$view} trouvée\n";
        } else {
            echo "   ❌ Vue {$view} manquante\n";
        }
    }

    // Test 5: Vérifier les routes
    echo "\n5. Vérification des routes...\n";

    $routes = shell_exec('php artisan route:list --name=admin 2>&1');
    if (strpos($routes, 'admin.stock') !== false) {
        echo "   ✅ Route admin.stock trouvée\n";
    } else {
        echo "   ❌ Route admin.stock manquante\n";
    }

    if (strpos($routes, 'admin.messages') !== false) {
        echo "   ✅ Route admin.messages trouvée\n";
    } else {
        echo "   ❌ Route admin.messages manquante\n";
    }

    // Test 6: Vérifier les contrôleurs
    echo "\n6. Vérification des contrôleurs...\n";

    $controllers = [
        'app/Http/Controllers/Admin/ProductController.php',
        'app/Http/Controllers/Admin/AdminMessageController.php'
    ];

    foreach ($controllers as $controller) {
        if (file_exists($controller)) {
            echo "   ✅ Contrôleur {$controller} trouvé\n";
        } else {
            echo "   ❌ Contrôleur {$controller} manquant\n";
        }
    }

    echo "\n✅ Tests de structure terminés avec succès!\n";

} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}

echo "\n=== RECOMMANDATIONS ===\n";
echo "💡 Pour tester le système complet:\n";
echo "   1. Vérifier que votre serveur web fonctionne\n";
echo "   2. Aller sur /admin/stock pour voir les indicateurs\n";
echo "   3. Créer un message d'alerte sur /admin/messages/create\n";
echo "   4. Vérifier l'affichage sur /seller/dashboard\n";

echo "\n=== FIN DU TEST ===\n";
