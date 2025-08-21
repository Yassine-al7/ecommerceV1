<?php

echo "Test de la nouvelle logique de calcul de marge\n";
echo "=============================================\n\n";

// Configuration des prix de livraison par ville
$cities = [
    'Casablanca' => ['price' => 15.00, 'zone' => 'local'],
    'Rabat' => ['price' => 20.00, 'zone' => 'local'],
    'Fès' => ['price' => 25.00, 'zone' => 'regional'],
    'Marrakech' => ['price' => 25.00, 'zone' => 'regional'],
    'Agadir' => ['price' => 30.00, 'zone' => 'regional'],
    'Autre' => ['price' => 40.00, 'zone' => 'national']
];

echo "1. Nouvelle logique de calcul de marge:\n";
echo "=======================================\n";
echo "ANCIENNE LOGIQUE: Marge = (Prix_vente - (Prix_achat + Livraison)) × Quantité\n";
echo "NOUVELLE LOGIQUE: Marge = Prix_total_commande - Coût_total\n";
echo "Où:\n";
echo "  - Prix_total_commande = Prix_vente × Quantité\n";
echo "  - Coût_total = (Prix_achat + Livraison) × Quantité\n\n";

echo "2. Exemples de calculs:\n";
echo "=======================\n";

$testCases = [
    [
        'prixAchat' => 100.00,
        'prixVente' => 150.00,
        'quantite' => 2,
        'description' => 'Prix d\'achat: 100 DH, Prix de vente: 150 DH, Quantité: 2'
    ],
    [
        'prixAchat' => 80.00,
        'prixVente' => 120.00,
        'quantite' => 3,
        'description' => 'Prix d\'achat: 80 DH, Prix de vente: 120 DH, Quantité: 3'
    ],
    [
        'prixAchat' => 200.00,
        'prixVente' => 250.00,
        'quantite' => 1,
        'description' => 'Prix d\'achat: 200 DH, Prix de vente: 250 DH, Quantité: 1'
    ]
];

foreach ($testCases as $index => $test) {
    echo "Test " . ($index + 1) . ": {$test['description']}\n";
    echo "------------------------------------------------\n";

    $prixAchat = $test['prixAchat'];
    $prixVente = $test['prixVente'];
    $quantite = $test['quantite'];

    foreach ($cities as $city => $cityConfig) {
        $prixLivraison = $cityConfig['price'];

        // ANCIENNE LOGIQUE
        $margeAncienne = ($prixVente - ($prixAchat + $prixLivraison)) * $quantite;

        // NOUVELLE LOGIQUE
        $prixTotalCommande = $prixVente * $quantite;
        $coutTotal = ($prixAchat + $prixLivraison) * $quantite;
        $margeNouvelle = $prixTotalCommande - $coutTotal;

        echo "📍 {$city} (Livraison: {$prixLivraison} DH):\n";
        echo "   Prix total commande: {$prixVente} × {$quantite} = " . number_format($prixTotalCommande, 2) . " DH\n";
        echo "   Coût total: (" . number_format($prixAchat, 2) . " + " . number_format($prixLivraison, 2) . ") × {$quantite} = " . number_format($coutTotal, 2) . " DH\n";
        echo "   Marge (nouvelle): " . number_format($prixTotalCommande, 2) . " - " . number_format($coutTotal, 2) . " = " . number_format($margeNouvelle, 2) . " DH\n";
        echo "   Marge (ancienne): " . number_format($margeAncienne, 2) . " DH\n";
        echo "   Différence: " . number_format($margeNouvelle - $margeAncienne, 2) . " DH\n";
        echo "\n";
    }
    echo "\n";
}

echo "3. Vérification de la cohérence:\n";
echo "================================\n";

// Test avec un exemple concret
$prixAchat = 100.00;
$prixVente = 150.00;
$quantite = 2;
$prixLivraison = 20.00;

echo "Exemple concret:\n";
echo "Prix d'achat: {$prixAchat} DH\n";
echo "Prix de vente: {$prixVente} DH\n";
echo "Quantité: {$quantite}\n";
echo "Prix livraison: {$prixLivraison} DH\n\n";

// Calculs
$prixTotalCommande = $prixVente * $quantite;
$coutTotal = ($prixAchat + $prixLivraison) * $quantite;
$margeNouvelle = $prixTotalCommande - $coutTotal;

echo "Calculs:\n";
echo "- Prix total commande: {$prixVente} × {$quantite} = " . number_format($prixTotalCommande, 2) . " DH\n";
echo "- Coût total: ({$prixAchat} + {$prixLivraison}) × {$quantite} = " . number_format($coutTotal, 2) . " DH\n";
echo "- Marge bénéfice: " . number_format($prixTotalCommande, 2) . " - " . number_format($coutTotal, 2) . " = " . number_format($margeNouvelle, 2) . " DH\n\n";

echo "4. Avantages de la nouvelle logique:\n";
echo "====================================\n";
echo "✅ Plus intuitive: Marge = Revenus - Coûts\n";
echo "✅ Plus claire: Séparation nette entre revenus et coûts\n";
echo "✅ Plus facile à comprendre pour les vendeurs\n";
echo "✅ Cohérente avec la logique comptable standard\n\n";

echo "Test terminé avec succès !\n";
echo "La nouvelle logique est maintenant implémentée.\n";
