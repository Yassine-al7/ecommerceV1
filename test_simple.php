<?php
echo "Test du systeme de stock\n";
echo "=======================\n\n";

// Test simple
$stock_initial = 100;
$quantite_vendue = 10;
$stock_final = $stock_initial - $quantite_vendue;

echo "Stock initial: $stock_initial\n";
echo "Quantite vendue: $quantite_vendue\n";
echo "Stock final calcule: $stock_final\n\n";

if ($stock_final === 90) {
    echo "SUCCES: Le calcul est correct!\n";
} else {
    echo "ERREUR: Le calcul est incorrect!\n";
}

echo "\nTest termine.\n";
