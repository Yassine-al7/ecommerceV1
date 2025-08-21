<?php

echo "Test du système de prix de livraison par ville\n";
echo "=============================================\n\n";

// Simuler la configuration des villes
$cities = [
    'Casablanca' => [
        'price' => 15.00,
        'zone' => 'local',
        'delivery_time' => '1-2 jours'
    ],
    'Rabat' => [
        'price' => 20.00,
        'zone' => 'local',
        'delivery_time' => '1-2 jours'
    ],
    'Fès' => [
        'price' => 25.00,
        'zone' => 'regional',
        'delivery_time' => '2-3 jours'
    ],
    'Marrakech' => [
        'price' => 25.00,
        'zone' => 'regional',
        'delivery_time' => '2-3 jours'
    ],
    'Agadir' => [
        'price' => 30.00,
        'zone' => 'regional',
        'delivery_time' => '2-3 jours'
    ],
    'Autre' => [
        'price' => 40.00,
        'zone' => 'national',
        'delivery_time' => '3-5 jours'
    ]
];

echo "1. Test des prix de livraison par ville:\n";
echo "----------------------------------------\n";

foreach ($cities as $city => $config) {
    $zoneColor = '';
    switch ($config['zone']) {
        case 'local':
            $zoneColor = '🟢';
            break;
        case 'regional':
            $zoneColor = '🟡';
            break;
        case 'national':
            $zoneColor = '🔴';
            break;
    }

    echo "{$zoneColor} {$city}: {$config['price']} DH - {$config['delivery_time']} (Zone: {$config['zone']})\n";
}

echo "\n2. Test de calcul de marge avec livraison:\n";
echo "-------------------------------------------\n";

$prixAchat = 100.00;
$prixVenteClient = 150.00;
$quantite = 2;

echo "Prix d'achat: {$prixAchat} DH\n";
echo "Prix de vente: {$prixVenteClient} DH\n";
echo "Quantité: {$quantite}\n\n";

foreach ($cities as $city => $config) {
    $prixLivraison = $config['price'];
    $margeUnitaire = $prixVenteClient - ($prixAchat + $prixLivraison);
    $margeTotale = $margeUnitaire * $quantite;
    $prixTotal = $prixVenteClient * $quantite;

    echo "📍 {$city}:\n";
    echo "   Prix livraison: {$prixLivraison} DH\n";
    echo "   Marge unitaire: {$margeUnitaire} DH\n";
    echo "   Marge totale: {$margeTotale} DH\n";
    echo "   Prix total: {$prixTotal} DH\n";
    echo "   Délai: {$config['delivery_time']}\n";
    echo "\n";
}

echo "3. Test de validation des prix:\n";
echo "-------------------------------\n";

$testCases = [
    ['city' => 'Casablanca', 'prixVente' => 120.00],
    ['city' => 'Fès', 'prixVente' => 130.00],
    ['city' => 'Autre', 'prixVente' => 140.00],
];

foreach ($testCases as $test) {
    $city = $test['city'];
    $prixVente = $test['prixVente'];
    $prixLivraison = $cities[$city]['price'];
    $marge = $prixVente - ($prixAchat + $prixLivraison);

    if ($marge > 0) {
        echo "✅ {$city}: Prix de vente {$prixVente} DH → Marge positive {$marge} DH\n";
    } else {
        echo "❌ {$city}: Prix de vente {$prixVente} DH → Marge négative {$marge} DH\n";
    }
}

echo "\n4. Résumé des zones:\n";
echo "--------------------\n";

$zones = [];
foreach ($cities as $city => $config) {
    $zone = $config['zone'];
    if (!isset($zones[$zone])) {
        $zones[$zone] = [];
    }
    $zones[$zone][] = $city;
}

foreach ($zones as $zone => $cityList) {
    $emoji = $zone === 'local' ? '🟢' : ($zone === 'regional' ? '🟡' : '🔴');
    echo "{$emoji} Zone {$zone}: " . implode(', ', $cityList) . "\n";
}

echo "\nTest terminé avec succès !\n";
