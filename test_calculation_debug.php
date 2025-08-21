<?php

echo "Test de debug des calculs\n";
echo "=========================\n\n";

echo "1. Test de la logique de base:\n";
echo "==============================\n";

// Test avec des valeurs simples
$prixAchat = 100.00;
$prixVente = 110.00;
$quantite = 10;
$prixLivraison = 30.00;

echo "Valeurs de test:\n";
echo "- Prix d'achat: {$prixAchat} DH\n";
echo "- Prix de vente: {$prixVente} DH\n";
echo "- Quantité: {$quantite}\n";
echo "- Prix livraison: {$prixLivraison} DH\n\n";

// Calculs
$margeParPiece = $prixVente - $prixAchat;
$margeTotalePieces = $margeParPiece * $quantite;
$margeBeneficeFinale = $margeTotalePieces - $prixLivraison;
$prixTotalCommande = $prixVente * $quantite;

echo "Calculs:\n";
echo "1. Marge par pièce: {$prixVente} - {$prixAchat} = {$margeParPiece} DH\n";
echo "2. Marge totale pièces: {$margeParPiece} × {$quantite} = {$margeTotalePieces} DH\n";
echo "3. Prix total commande: {$prixVente} × {$quantite} = {$prixTotalCommande} DH\n";
echo "4. Marge bénéfice finale: {$margeTotalePieces} - {$prixLivraison} = {$margeBeneficeFinale} DH\n\n";

echo "✅ Résultats attendus:\n";
echo "- Marge par produit: {$margeTotalePieces} DH\n";
echo "- Prix total commande: {$prixTotalCommande} DH\n";
echo "- Marge bénéfice totale: {$margeBeneficeFinale} DH\n\n";

echo "2. Test avec des valeurs négatives:\n";
echo "==================================\n";

// Test avec prix de vente < prix d'achat
$prixAchat2 = 100.00;
$prixVente2 = 90.00;
$quantite2 = 5;

$margeParPiece2 = $prixVente2 - $prixAchat2;
$margeTotalePieces2 = $margeParPiece2 * $quantite2;

echo "Test avec prix de vente < prix d'achat:\n";
echo "- Prix d'achat: {$prixAchat2} DH\n";
echo "- Prix de vente: {$prixVente2} DH\n";
echo "- Quantité: {$quantite2}\n";
echo "- Marge par pièce: {$prixVente2} - {$prixAchat2} = {$margeParPiece2} DH\n";
echo "- Marge totale pièces: {$margeParPiece2} × {$quantite2} = {$margeTotalePieces2} DH\n";
echo "- Résultat: Marge négative (perte) = {$margeTotalePieces2} DH\n\n";

echo "3. Vérification des formules:\n";
echo "=============================\n";

// Vérifier que les formules sont cohérentes
$test1 = ($prixVente - $prixAchat) * $quantite;
$test2 = $margeParPiece * $quantite;

echo "Vérification de cohérence:\n";
echo "- Formule 1: ({$prixVente} - {$prixAchat}) × {$quantite} = {$test1} DH\n";
echo "- Formule 2: {$margeParPiece} × {$quantite} = {$test2} DH\n";
echo "- Cohérence: " . ($test1 == $test2 ? "✅ OK" : "❌ ERREUR") . "\n\n";

echo "Test terminé !\n";
echo "Si vous voyez des valeurs négatives, c'est normal pour les tests de validation.\n";
