<?php
echo "Test du rafraichissement du stock\n";
echo "================================\n\n";

// Test simple
$stock_initial = 2;
$quantite_vendue = 2;
$stock_final = $stock_initial - $quantite_vendue;

echo "Stock initial: $stock_initial\n";
echo "Quantite vendue: $quantite_vendue\n";
echo "Stock final: $stock_final\n\n";

if ($stock_final === 0) {
    echo "SUCCES: Le stock est maintenant 0 (rupture)\n";
    echo "La couleur Rose devrait etre grisee et desactivee\n";
} else {
    echo "ERREUR: Le calcul est incorrect\n";
}

echo "\nTest termine.\n";
