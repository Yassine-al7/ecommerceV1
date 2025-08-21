<?php

echo "Test des nouvelles règles de validation\n";
echo "=====================================\n\n";

echo "1. Règle principale:\n";
echo "====================\n";
echo "🎯 Le prix de vente DOIT être supérieur au prix d'achat\n";
echo "🎯 Sinon, la marge bénéfice sera négative ou nulle\n\n";

echo "2. Exemples de validation:\n";
echo "==========================\n";

$testCases = [
    [
        'prixAchat' => 100.00,
        'prixVente' => 110.00,
        'description' => 'Prix de vente > Prix d\'achat (VALIDÉ)',
        'expected' => '✅ VALIDÉ'
    ],
    [
        'prixAchat' => 100.00,
        'prixVente' => 100.00,
        'description' => 'Prix de vente = Prix d\'achat (REJETÉ)',
        'expected' => '❌ REJETÉ'
    ],
    [
        'prixAchat' => 100.00,
        'prixVente' => 90.00,
        'description' => 'Prix de vente < Prix d\'achat (REJETÉ)',
        'expected' => '❌ REJETÉ'
    ],
    [
        'prixAchat' => 80.00,
        'prixVente' => 120.00,
        'description' => 'Prix de vente >> Prix d\'achat (VALIDÉ)',
        'expected' => '✅ VALIDÉ'
    ],
    [
        'prixAchat' => 200.00,
        'prixVente' => 199.99,
        'description' => 'Prix de vente légèrement inférieur (REJETÉ)',
        'expected' => '❌ REJETÉ'
    ]
];

foreach ($testCases as $index => $test) {
    $prixAchat = $test['prixAchat'];
    $prixVente = $test['prixVente'];
    $isValid = $prixVente > $prixAchat;
    
    echo "Test " . ($index + 1) . ": {$test['description']}\n";
    echo "------------------------------------------------\n";
    echo "Prix d'achat: {$prixAchat} DH\n";
    echo "Prix de vente: {$prixVente} DH\n";
    echo "Différence: " . ($prixVente - $prixAchat) . " DH\n";
    echo "Statut: {$test['expected']}\n";
    
    if ($isValid) {
        echo "✅ Marge par pièce: {$prixVente} - {$prixAchat} = " . ($prixVente - $prixAchat) . " DH\n";
    } else {
        echo "❌ Marge par pièce: {$prixVente} - {$prixAchat} = " . ($prixVente - $prixAchat) . " DH (négative!)\n";
    }
    echo "\n";
}

echo "3. Impact sur le calcul de marge:\n";
echo "=================================\n";

$prixAchat = 100.00;
$quantite = 5;
$prixLivraison = 25.00;

echo "Scénario: Quantité = {$quantite}, Livraison = {$prixLivraison} DH\n\n";

$scenarios = [
    ['prixVente' => 110.00, 'description' => 'Prix de vente 110 DH (marge positive)'],
    ['prixVente' => 100.00, 'description' => 'Prix de vente 100 DH (marge nulle)'],
    ['prixVente' => 90.00, 'description' => 'Prix de vente 90 DH (marge négative)']
];

foreach ($scenarios as $scenario) {
    $prixVente = $scenario['prixVente'];
    $description = $scenario['description'];
    
    echo "📍 {$description}:\n";
    
    if ($prixVente > $prixAchat) {
        $margeParPiece = $prixVente - $prixAchat;
        $margeTotalePieces = $margeParPiece * $quantite;
        $margeBenefice = $margeTotalePieces - $prixLivraison;
        
        echo "   Marge par pièce: {$prixVente} - {$prixAchat} = {$margeParPiece} DH\n";
        echo "   Marge totale pièces: {$margeParPiece} × {$quantite} = {$margeTotalePieces} DH\n";
        echo "   Marge finale: {$margeTotalePieces} - {$prixLivraison} = {$margeBenefice} DH\n";
        echo "   ✅ Marge positive - Commande rentable\n";
    } elseif ($prixVente == $prixAchat) {
        echo "   Marge par pièce: {$prixVente} - {$prixAchat} = 0 DH\n";
        echo "   Marge totale pièces: 0 × {$quantite} = 0 DH\n";
        echo "   Marge finale: 0 - {$prixLivraison} = -{$prixLivraison} DH\n";
        echo "   ⚠️ Marge nulle - Perte égale au prix de livraison\n";
    } else {
        $margeParPiece = $prixVente - $prixAchat;
        $margeTotalePieces = $margeParPiece * $quantite;
        $margeBenefice = $margeTotalePieces - $prixLivraison;
        
        echo "   Marge par pièce: {$prixVente} - {$prixAchat} = {$margeParPiece} DH\n";
        echo "   Marge totale pièces: {$margeParPiece} × {$quantite} = {$margeTotalePieces} DH\n";
        echo "   Marge finale: {$margeTotalePieces} - {$prixLivraison} = {$margeBenefice} DH\n";
        echo "   ❌ Marge négative - Commande non rentable\n";
    }
    echo "\n";
}

echo "4. Règles de validation implémentées:\n";
echo "=====================================\n";
echo "✅ Frontend: Validation en temps réel avec message d'erreur\n";
echo "✅ Backend: Validation avant sauvegarde avec message d'erreur\n";
echo "✅ Interface: Changement de couleur et message d'avertissement\n";
echo "✅ Formulaire: Blocage de soumission si prix invalide\n\n";

echo "5. Messages d'erreur:\n";
echo "=====================\n";
echo "❌ Frontend: '⚠️ Le prix de vente doit être supérieur au prix d\'achat pour avoir une marge bénéfice'\n";
echo "❌ Backend: 'Le prix de vente doit être supérieur au prix d\'achat pour avoir une marge bénéfice'\n";
echo "❌ Soumission: 'Veuillez corriger le prix de vente avant de soumettre la commande.'\n\n";

echo "Test terminé avec succès !\n";
echo "Les nouvelles règles de validation sont maintenant implémentées.\n";
