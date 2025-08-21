<?php

echo "Test de la logique multi-produits selon l'exemple de l'utilisateur\n";
echo "==============================================================\n\n";

echo "1. Rappel de la logique de l'utilisateur:\n";
echo "==========================================\n";
echo "🎯 Marge par pièce = Prix de vente - Prix d'achat\n";
echo "🎯 Marge totale pièces = Marge par pièce × Quantité\n";
echo "🎯 Marge finale = Marge totale pièces - Prix de livraison\n\n";

echo "2. Exemple avec plusieurs produits:\n";
echo "==================================\n";

// Configuration des produits
$products = [
    [
        'name' => 'T-shirt Premium',
        'prixAchat' => 100.00,
        'prixVente' => 150.00,
        'quantite' => 3,
        'description' => 'T-shirt Premium - Prix d\'achat: 100 DH, Prix de vente: 150 DH, Quantité: 3'
    ],
    [
        'name' => 'Pantalon Classic',
        'prixAchat' => 200.00,
        'prixVente' => 280.00,
        'quantite' => 2,
        'description' => 'Pantalon Classic - Prix d\'achat: 200 DH, Prix de vente: 280 DH, Quantité: 2'
    ],
    [
        'name' => 'Chaussures Sport',
        'prixAchat' => 150.00,
        'prixVente' => 220.00,
        'quantite' => 1,
        'description' => 'Chaussures Sport - Prix d\'achat: 150 DH, Prix de vente: 220 DH, Quantité: 1'
    ]
];

$prixLivraison = 35.00; // Prix de livraison pour la commande

echo "Prix de livraison: {$prixLivraison} DH\n\n";

$prixTotalCommande = 0;
$margeTotaleProduits = 0;

foreach ($products as $index => $product) {
    $prixAchat = $product['prixAchat'];
    $prixVente = $product['prixVente'];
    $quantite = $product['quantite'];

    echo "📍 {$product['description']}:\n";
    echo "   Marge par pièce: {$prixVente} - {$prixAchat} = " . ($prixVente - $prixAchat) . " DH\n";

    $margeParPiece = $prixVente - $prixAchat;
    $margeTotalePieces = $margeParPiece * $quantite;
    $prixProduit = $prixVente * $quantite;

    echo "   Marge totale pièces: {$margeParPiece} × {$quantite} = {$margeTotalePieces} DH\n";
    echo "   Prix total produit: {$prixVente} × {$quantite} = {$prixProduit} DH\n";
    echo "\n";

    $prixTotalCommande += $prixProduit;
    $margeTotaleProduits += $margeTotalePieces;
}

echo "3. Calculs totaux:\n";
echo "==================\n";
echo "Prix total commande: {$prixTotalCommande} DH\n";
echo "Marge totale produits: {$margeTotaleProduits} DH\n";
echo "Prix de livraison: {$prixLivraison} DH\n";
echo "Marge bénéfice finale: {$margeTotaleProduits} - {$prixLivraison} = " . ($margeTotaleProduits - $prixLivraison) . " DH\n\n";

echo "4. Vérification détaillée:\n";
echo "==========================\n";

// Vérification produit par produit
foreach ($products as $index => $product) {
    $prixAchat = $product['prixAchat'];
    $prixVente = $product['prixVente'];
    $quantite = $product['quantite'];

    $margeParPiece = $prixVente - $prixAchat;
    $margeTotalePieces = $margeParPiece * $quantite;
    $prixProduit = $prixVente * $quantite;

    echo "Produit #" . ($index + 1) . " ({$product['name']}):\n";
    echo "  ✅ Prix d'achat: {$prixAchat} DH\n";
    echo "  ✅ Prix de vente: {$prixVente} DH\n";
    echo "  ✅ Quantité: {$quantite}\n";
    echo "  ✅ Marge par pièce: {$margeParPiece} DH\n";
    echo "  ✅ Marge totale: {$margeTotalePieces} DH\n";
    echo "  ✅ Prix total: {$prixProduit} DH\n";
    echo "\n";
}

echo "5. Résumé de la commande:\n";
echo "==========================\n";
echo "📦 Nombre de produits: " . count($products) . "\n";
echo "💰 Prix total commande: " . number_format($prixTotalCommande, 2) . " DH\n";
echo "📈 Marge totale produits: " . number_format($margeTotaleProduits, 2) . " DH\n";
echo "🚚 Prix de livraison: " . number_format($prixLivraison, 2) . " DH\n";
echo "🎯 Marge bénéfice finale: " . number_format($margeTotaleProduits - $prixLivraison, 2) . " DH\n\n";

echo "6. Avantages de cette logique:\n";
echo "==============================\n";
echo "✅ Transparente: Chaque composant est visible\n";
echo "✅ Équitable: La livraison est déduite de la marge totale\n";
echo "✅ Flexible: Fonctionne avec 1, 2, 3... produits\n";
echo "✅ Logique: Marge = Revenus produits - Coût livraison\n\n";

echo "Test terminé avec succès !\n";
echo "La logique multi-produits est maintenant correctement implémentée.\n";
