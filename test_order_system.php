<?php

require_once 'vendor/autoload.php';

use App\Traits\GeneratesOrderReferences;

// Classe de test pour utiliser le trait
class TestOrderSystem
{
    use GeneratesOrderReferences;

    public function test()
    {
        echo "Test du système de commande complet:\n";
        echo "====================================\n\n";

        echo "1. Test de génération de références:\n";
        echo "-----------------------------------\n";
        for ($i = 1; $i <= 3; $i++) {
            $reference = $this->generateUniqueOrderReference();
            echo "Référence $i: $reference\n";
        }

        echo "\n2. Test avec préfixes personnalisés:\n";
        echo "-----------------------------------\n";
        $prefixes = ['ADMIN', 'SELLER'];
        foreach ($prefixes as $prefix) {
            $reference = $this->generateUniqueOrderReferenceWithPrefix($prefix);
            echo "Référence $prefix: $reference\n";
        }

        echo "\n3. Test de calcul de marge:\n";
        echo "---------------------------\n";
        $this->testMarginCalculation();

        echo "\n4. Test de validation des prix:\n";
        echo "-------------------------------\n";
        $this->testPriceValidation();
    }

    private function testMarginCalculation()
    {
        $prixVenteClient = 150.00;
        $prixAchat = 100.00;
        $prixLivraison = 10.00;
        $quantite = 2;

        $margeBenefice = ($prixVenteClient - ($prixAchat + $prixLivraison)) * $quantite;
        $prixTotal = $prixVenteClient * $quantite;

        echo "Prix de vente au client: {$prixVenteClient} DH\n";
        echo "Prix d'achat: {$prixAchat} DH\n";
        echo "Prix de livraison: {$prixLivraison} DH\n";
        echo "Quantité: {$quantite}\n";
        echo "Prix total: {$prixTotal} DH\n";
        echo "Marge bénéfice: {$margeBenefice} DH\n";
    }

    private function testPriceValidation()
    {
        $prixVenteClient = 80.00;
        $prixAchat = 100.00;

        if ($prixVenteClient < $prixAchat) {
            echo "❌ Prix de vente ({$prixVenteClient} DH) inférieur au prix d'achat ({$prixAchat} DH)\n";
            echo "   La commande ne peut pas être créée\n";
        } else {
            echo "✅ Prix de vente ({$prixVenteClient} DH) supérieur au prix d'achat ({$prixAchat} DH)\n";
            echo "   La commande peut être créée\n";
        }

        $prixVenteClient2 = 120.00;
        if ($prixVenteClient2 >= $prixAchat) {
            echo "✅ Prix de vente ({$prixVenteClient2} DH) supérieur au prix d'achat ({$prixAchat} DH)\n";
            echo "   La commande peut être créée\n";
        }
    }
}

// Exécuter le test
$test = new TestOrderSystem();
$test->test();
