<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Services\StockService;

class TestStockSystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stock:test {--product-id=} {--couleur=} {--quantite=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test du système de gestion du stock';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Test du système de gestion du stock');
        $this->info('=====================================');

        // Récupérer les paramètres
        $productId = $this->option('product-id');
        $couleur = $this->option('couleur') ?: 'Couleur unique';
        $quantite = (int) ($this->option('quantite') ?: 5);

        if ($productId) {
            $this->testSpecificProduct($productId, $couleur, $quantite);
        } else {
            $this->testAllProducts();
        }

        $this->info('✅ Test terminé');
    }

    /**
     * Test d'un produit spécifique
     */
    private function testSpecificProduct($productId, $couleur, $quantite)
    {
        $product = Product::find($productId);
        if (!$product) {
            $this->error("❌ Produit ID {$productId} non trouvé");
            return;
        }

        $this->info("\n📦 Test du produit: {$product->name} (ID: {$product->id})");
        $this->info("🎨 Couleur: {$couleur}");
        $this->info("🔢 Quantité: {$quantite}");

        // Afficher l'état initial
        $this->displayStockState($product, $couleur, 'État initial');

        // Vérifier la disponibilité
        $availability = StockService::checkStockAvailability($productId, $couleur, $quantite);
        $this->displayAvailability($availability);

        // Tester la diminution du stock
        $this->info("\n🔄 Test de diminution du stock...");
        $success = StockService::decreaseStock($productId, $couleur, $quantite);

        if ($success) {
            $this->info("✅ Stock diminué avec succès");

            // Recharger le produit pour voir les changements
            $product->refresh();
            $this->displayStockState($product, $couleur, 'Après diminution');
        } else {
            $this->error("❌ Échec de la diminution du stock");
        }

        // Tester l'augmentation du stock
        $this->info("\n🔄 Test d'augmentation du stock...");
        $success = StockService::increaseStock($productId, $couleur, $quantite);

        if ($success) {
            $this->info("✅ Stock augmenté avec succès");

            // Recharger le produit pour voir les changements
            $product->refresh();
            $this->displayStockState($product, $couleur, 'Après augmentation');
        } else {
            $this->error("❌ Échec de l'augmentation du stock");
        }
    }

    /**
     * Test de tous les produits
     */
    private function testAllProducts()
    {
        $products = Product::all();

        $this->info("\n📊 Test de tous les produits ({$products->count()} trouvés)");

        foreach ($products as $product) {
            $this->info("\n--- {$product->name} (ID: {$product->id}) ---");

            // Afficher l'état du stock
            $this->displayStockState($product, 'Couleur unique', 'État actuel');

            // Vérifier la cohérence des données
            $this->checkDataConsistency($product);
        }
    }

    /**
     * Afficher l'état du stock
     */
    private function displayStockState($product, $couleur, $label)
    {
        $this->info("\n📋 {$label}:");
        $this->info("   Stock total: {$product->quantite_stock}");

        $stockCouleurs = json_decode($product->stock_couleurs, true) ?: [];
        if (!empty($stockCouleurs)) {
            $this->info("   Stock par couleur:");
            foreach ($stockCouleurs as $stockColor) {
                $marker = ($stockColor['name'] === $couleur) ? '🎯' : '  ';
                $this->info("     {$marker} {$stockColor['name']}: {$stockColor['quantity']}");
            }
        } else {
            $this->warn("   ⚠️ Aucun stock par couleur défini");
        }
    }

    /**
     * Afficher la disponibilité
     */
    private function displayAvailability($availability)
    {
        $this->info("\n🔍 Disponibilité:");
        $this->info("   Disponible: " . ($availability['available'] ? '✅ Oui' : '❌ Non'));
        $this->info("   Message: {$availability['message']}");
        $this->info("   Stock total: {$availability['stock_total']}");
        $this->info("   Stock couleur: {$availability['stock_couleur']}");
        $this->info("   Quantité demandée: {$availability['requested']}");

        if ($availability['deficit'] > 0) {
            $this->warn("   ⚠️ Déficit: {$availability['deficit']}");
        }
    }

    /**
     * Vérifier la cohérence des données
     */
    private function checkDataConsistency($product)
    {
        $this->info("🔍 Vérification de cohérence:");

        // Vérifier que stock_couleurs est un JSON valide
        $stockCouleurs = json_decode($product->stock_couleurs, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error("   ❌ stock_couleurs: JSON invalide");
            return;
        }

        if (empty($stockCouleurs)) {
            $this->warn("   ⚠️ stock_couleurs: Vide");
            return;
        }

        // Vérifier que les couleurs existent
        $couleurs = json_decode($product->couleur, true) ?: [];
        $stockColorNames = array_column($stockCouleurs, 'name');
        $couleurNames = is_array($couleurs) ? array_column($couleurs, 'name') : $couleurs;

        if (is_array($couleurNames)) {
            $missingColors = array_diff($stockColorNames, $couleurNames);
            if (!empty($missingColors)) {
                $this->warn("   ⚠️ Couleurs dans stock_couleurs mais pas dans couleur: " . implode(', ', $missingColors));
            }
        }

        $this->info("   ✅ Données cohérentes");
    }
}
