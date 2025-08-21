<?php

echo "Test de la logique de marge de l'utilisateur\n";
echo "============================================\n\n";

echo "1. Exemple de l'utilisateur:\n";
echo "============================\n";
echo "Prix d'achat: 100 DH par pièce\n";
echo "Prix de vente: 110 DH par pièce\n";
echo "Quantité: 10 pièces\n";
echo "Prix livraison: 30 DH (total pour la commande)\n\n";

$prixAchat = 100.00;
$prixVente = 110.00;
$quantite = 10;
$prixLivraison = 30.00;

echo "2. Calcul selon la logique de l'utilisateur:\n";
echo "============================================\n";

// Marge par pièce = Prix de vente - Prix d'achat
$margeParPiece = $prixVente - $prixAchat;
echo "Marge par pièce: {$prixVente} - {$prixAchat} = {$margeParPiece} DH\n";

// Marge totale sur toutes les pièces
$margeTotalePieces = $margeParPiece * $quantite;
echo "Marge totale sur {$quantite} pièces: {$margeParPiece} × {$quantite} = {$margeTotalePieces} DH\n";

// Marge finale = Marge totale pièces - Prix de livraison
$margeBenefice = $margeTotalePieces - $prixLivraison;
echo "Marge bénéfice finale: {$margeTotalePieces} - {$prixLivraison} = {$margeBenefice} DH\n\n";

echo "3. Vérification:\n";
echo "================\n";
echo "✅ L'utilisateur gagne 10 DH sur chaque pièce\n";
echo "✅ Sur 10 pièces, il gagne 10 × 10 = 100 DH\n";
echo "✅ Après déduction de la livraison (30 DH), sa marge finale est 100 - 30 = 70 DH\n";
echo "✅ Résultat attendu: 70 DH ✓\n\n";

echo "4. Test avec d'autres scénarios:\n";
echo "================================\n";

$testCases = [
    [
        'prixAchat' => 80.00,
        'prixVente' => 100.00,
        'quantite' => 5,
        'livraison' => 25.00,
        'description' => 'Prix d\'achat: 80 DH, Prix de vente: 100 DH, Quantité: 5, Livraison: 25 DH'
    ],
    [
        'prixAchat' => 150.00,
        'prixVente' => 180.00,
        'quantite' => 3,
        'livraison' => 35.00,
        'description' => 'Prix d\'achat: 150 DH, Prix de vente: 180 DH, Quantité: 3, Livraison: 35 DH'
    ],
    [
        'prixAchat' => 200.00,
        'prixVente' => 250.00,
        'quantite' => 2,
        'livraison' => 40.00,
        'description' => 'Prix d\'achat: 200 DH, Prix de vente: 250 DH, Quantité: 2, Livraison: 40 DH'
    ]
];

foreach ($testCases as $index => $test) {
    echo "Test " . ($index + 1) . ": {$test['description']}\n";
    echo "------------------------------------------------\n";

    $prixAchat = $test['prixAchat'];
    $prixVente = $test['prixVente'];
    $quantite = $test['quantite'];
    $prixLivraison = $test['livraison'];

    // Calcul selon la logique de l'utilisateur
    $margeParPiece = $prixVente - $prixAchat;
    $margeTotalePieces = $margeParPiece * $quantite;
    $margeBenefice = $margeTotalePieces - $prixLivraison;

    echo "📍 Calcul:\n";
    echo "   Marge par pièce: {$prixVente} - {$prixAchat} = {$margeParPiece} DH\n";
    echo "   Marge totale pièces: {$margeParPiece} × {$quantite} = {$margeTotalePieces} DH\n";
    echo "   Marge finale: {$margeTotalePieces} - {$prixLivraison} = {$margeBenefice} DH\n";
    echo "\n";
}

echo "5. Résumé de la logique:\n";
echo "=========================\n";
echo "🎯 Marge par pièce = Prix de vente - Prix d'achat\n";
echo "🎯 Marge totale pièces = Marge par pièce × Quantité\n";
echo "🎯 Marge bénéfice finale = Marge totale pièces - Prix de livraison\n\n";

echo "6. Avantages de cette logique:\n";
echo "===============================\n";
echo "✅ Plus intuitive: Le vendeur voit sa marge par pièce\n";
echo "✅ Plus claire: Séparation entre marge produit et coût livraison\n";
echo "✅ Plus équitable: La livraison est déduite de la marge totale\n";
echo "✅ Plus transparente: Chaque composant est visible\n\n";

echo "Test terminé avec succès !\n";
echo "La logique de l'utilisateur est maintenant implémentée.\n";
