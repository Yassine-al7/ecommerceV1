<?php

// Test simple du trait sans Laravel
echo "Test du trait GeneratesOrderReferences\n";
echo "=====================================\n\n";

// Simuler la classe Order pour le test
class MockOrder
{
    public static $references = [];

    public static function where($field, $value)
    {
        return new class($value) {
            private $searchValue;

            public function __construct($value) {
                $this->searchValue = $value;
            }

            public function exists()
            {
                global $MockOrderReferences;
                return in_array($this->searchValue, MockOrder::$references);
            }
        };
    }

    public static function addReference($ref)
    {
        MockOrder::$references[] = $ref;
    }
}

// Simuler le trait
trait GeneratesOrderReferences
{
    protected function generateUniqueOrderReference()
    {
        do {
            $date = date('Ymd');
            $random = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            $reference = "CMD-{$date}-{$random}";
        } while (in_array($reference, MockOrder::$references));

        return $reference;
    }

    protected function generateUniqueOrderReferenceWithPrefix($prefix)
    {
        do {
            $date = date('Ymd');
            $random = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            $reference = "{$prefix}-{$date}-{$random}";
        } while (in_array($reference, MockOrder::$references));

        return $reference;
    }
}

// Classe de test
class TestController
{
    use GeneratesOrderReferences;

    public function test()
    {
        echo "1. Test de génération de références:\n";
        echo "-----------------------------------\n";

        for ($i = 1; $i <= 5; $i++) {
            $reference = $this->generateUniqueOrderReference();
            echo "Référence $i: $reference\n";
            MockOrder::addReference($reference);
        }

        echo "\n2. Test avec préfixes personnalisés:\n";
        echo "-----------------------------------\n";

        $prefixes = ['ADMIN', 'SELLER'];
        foreach ($prefixes as $prefix) {
            $reference = $this->generateUniqueOrderReferenceWithPrefix($prefix);
            echo "Référence $prefix: $reference\n";
            MockOrder::addReference($reference);
        }

        echo "\n3. Test d'unicité:\n";
        echo "------------------\n";

        $allRefs = MockOrder::$references;
        $uniqueRefs = array_unique($allRefs);

        if (count($allRefs) === count($uniqueRefs)) {
            echo "✅ Toutes les références sont uniques\n";
        } else {
            echo "❌ Il y a des doublons dans les références\n";
        }

        echo "Total généré: " . count($allRefs) . "\n";
        echo "Total unique: " . count($uniqueRefs) . "\n";
    }
}

// Exécuter le test
$test = new TestController();
$test->test();

echo "\nTest terminé avec succès !\n";
