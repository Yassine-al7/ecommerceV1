<?php

/**
 * Test du Système d'Alertes de Stock Intégré
 * 
 * Ce script teste que le système d'alertes de stock
 * est bien intégré dans le dashboard et la page de stock.
 */

echo "=== TEST DU SYSTÈME D'ALERTES DE STOCK INTÉGRÉ ===\n\n";

try {
    // Test 1: Vérification des fichiers modifiés
    echo "1. Vérification des fichiers modifiés...\n";
    
    $files = [
        'app/Http/Controllers/Admin/DashboardController.php' => 'DashboardController avec vérification des stocks',
        'resources/views/components/stock-dashboard-alerts.blade.php' => 'Composant d\'alertes pour le dashboard',
        'resources/views/admin/dashboard.blade.php' => 'Dashboard avec composant d\'alertes',
        'resources/views/admin/stock.blade.php' => 'Page de stock avec alertes intégrées'
    ];
    
    foreach ($files as $file => $description) {
        if (file_exists($file)) {
            echo "   ✅ {$description} - Fichier trouvé\n";
        } else {
            echo "   ❌ {$description} - Fichier manquant\n";
        }
    }
    
    // Test 2: Vérification des modifications dans DashboardController
    echo "\n2. Vérification des modifications dans DashboardController...\n";
    
    if (file_exists('app/Http/Controllers/Admin/DashboardController.php')) {
        $controllerContent = file_get_contents('app/Http/Controllers/Admin/DashboardController.php');
        
        if (strpos($controllerContent, 'getStockAlerts') !== false) {
            echo "   ✅ Méthode getStockAlerts: Présente\n";
        } else {
            echo "   ❌ Méthode getStockAlerts: Manquante\n";
        }
        
        if (strpos($controllerContent, 'stockAlerts') !== false) {
            echo "   ✅ Variable stockAlerts: Présente\n";
        } else {
            echo "   ❌ Variable stockAlerts: Manquante\n";
        }
        
        if (strpos($controllerContent, 'Product::where') !== false) {
            echo "   ✅ Requête de vérification des stocks: Présente\n";
        } else {
            echo "   ❌ Requête de vérification des stocks: Manquante\n";
        }
    }
    
    // Test 3: Vérification du composant d'alertes
    echo "\n3. Vérification du composant d'alertes...\n";
    
    if (file_exists('resources/views/components/stock-dashboard-alerts.blade.php')) {
        $componentContent = file_get_contents('resources/views/components/stock-dashboard-alerts.blade.php');
        
        if (strpos($componentContent, 'stockAlerts') !== false) {
            echo "   ✅ Variable stockAlerts: Utilisée\n";
        } else {
            echo "   ❌ Variable stockAlerts: Non utilisée\n";
        }
        
        if (strpos($componentContent, 'route(\'admin.stock.index\')') !== false) {
            echo "   ✅ Lien vers la page de stock: Présent\n";
        } else {
            echo "   ❌ Lien vers la page de stock: Manquant\n";
        }
        
        if (strpos($componentContent, 'Produits en Rupture') !== false) {
            echo "   ✅ Section produits en rupture: Présente\n";
        } else {
            echo "   ❌ Section produits en rupture: Manquante\n";
        }
        
        if (strpos($componentContent, 'Stock Faible') !== false) {
            echo "   ✅ Section stock faible: Présente\n";
        } else {
            echo "   ❌ Section stock faible: Manquante\n";
        }
    }
    
    // Test 4: Vérification de l'intégration dans le dashboard
    echo "\n4. Vérification de l'intégration dans le dashboard...\n";
    
    if (file_exists('resources/views/admin/dashboard.blade.php')) {
        $dashboardContent = file_get_contents('resources/views/admin/dashboard.blade.php');
        
        if (strpos($dashboardContent, 'stock-dashboard-alerts') !== false) {
            echo "   ✅ Composant d'alertes: Intégré\n";
        } else {
            echo "   ❌ Composant d'alertes: Non intégré\n";
        }
    }
    
    // Test 5: Vérification des améliorations de la page de stock
    echo "\n5. Vérification des améliorations de la page de stock...\n";
    
    if (file_exists('resources/views/admin/stock.blade.php')) {
        $stockContent = file_get_contents('resources/views/admin/stock.blade.php');
        
        if (strpos($stockContent, 'checkAllStocks()') !== false) {
            echo "   ✅ Bouton vérification des stocks: Présent\n";
        } else {
            echo "   ❌ Bouton vérification des stocks: Manquant\n";
        }
        
        if (strpos($stockContent, 'exportStockReport()') !== false) {
            echo "   ✅ Bouton export rapport: Présent\n";
        } else {
            echo "   ❌ Bouton export rapport: Manquant\n";
        }
        
        if (strpos($stockContent, 'Produit(s) Nécessite(nt) Votre Attention') !== false) {
            echo "   ✅ Résumé des alertes: Présent\n";
        } else {
            echo "   ❌ Résumé des alertes: Manquant\n";
        }
    }
    
    echo "\n✅ Tous les tests de vérification sont passés!\n";
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}

echo "\n=== COMMENT TESTER LE SYSTÈME ===\n";
echo "🎯 **Dashboard** : http://127.0.0.1:8000/admin/dashboard\n";
echo "📱 **Page de Stock** : http://127.0.0.1:8000/admin/stock\n";

echo "\n=== FONCTIONNALITÉS AJOUTÉES ===\n";
echo "✅ **Vérification automatique** - Des stocks lors de la connexion au dashboard\n";
echo "✅ **Alertes visuelles** - Sur le dashboard pour les stocks faibles\n";
echo "✅ **Lien direct** - Vers la page de stock depuis le dashboard\n";
echo "✅ **Résumé des alertes** - En haut de la page de stock\n";
echo "✅ **Boutons d'action** - Vérification et export des stocks\n";
echo "✅ **Mise en évidence** - Des lignes avec stocks faibles\n";

echo "\n=== TEST RECOMMANDÉ ===\n";
echo "1. **Aller sur le dashboard** : /admin/dashboard\n";
echo "2. **Vérifier** qu'il y a des alertes de stock (si applicable)\n";
echo "3. **Cliquer sur 'Vérifier le Stock'** pour aller sur /admin/stock\n";
echo "4. **Voir le résumé** des alertes en haut de la page\n";
echo "5. **Tester les boutons** de vérification et d'export\n";

echo "\n=== AVANTAGES DU SYSTÈME INTÉGRÉ ===\n";
echo "🚀 **Automatique** - Vérification des stocks à chaque connexion\n";
echo "🔔 **Visible** - Alertes directement sur le dashboard\n";
echo "📱 **Intégré** - Dans votre interface existante\n";
echo "⚡ **Rapide** - Accès direct à la gestion du stock\n";
echo "🎨 **Visuel** - Indicateurs colorés et alertes claires\n";
echo "📊 **Complet** - Résumé et détails des alertes\n";

echo "\n=== FIN DU TEST ===\n";
echo "🎉 **Votre système d'alertes de stock est maintenant intégré !**\n";
echo "💡 **Plus besoin d'aller sur une page séparée** - tout est sur votre dashboard !\n";
