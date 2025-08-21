<?php

require_once 'vendor/autoload.php';

use App\Traits\GeneratesOrderReferences;

// Classe de test pour utiliser le trait
class TestOrderReferenceGenerator
{
    use GeneratesOrderReferences;
    
    public function test()
    {
        echo "Test de génération de références de commande:\n";
        echo "============================================\n\n";
        
        for ($i = 1; $i <= 5; $i++) {
            $reference = $this->generateUniqueOrderReference();
            echo "Référence $i: $reference\n";
        }
        
        echo "\nTest avec préfixe personnalisé:\n";
        echo "===============================\n\n";
        
        $prefixes = ['ADMIN', 'SELLER', 'SPECIAL'];
        foreach ($prefixes as $prefix) {
            $reference = $this->generateUniqueOrderReferenceWithPrefix($prefix);
            echo "Référence $prefix: $reference\n";
        }
    }
}

// Exécuter le test
$test = new TestOrderReferenceGenerator();
$test->test();
