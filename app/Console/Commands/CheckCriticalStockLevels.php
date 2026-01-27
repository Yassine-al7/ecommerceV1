<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ColorStockNotificationService;
use App\Models\Product;
use Illuminate\Support\Facades\Log;

class CheckCriticalStockLevels extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stock:check-critical {--force : Forcer la vérification même si déjà effectuée récemment}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Vérifier et notifier les stocks critiques par couleur';

    /**
     * Execute the console command.
     */
    public function handle(ColorStockNotificationService $notificationService)
    {
        $this->info('🔍 Vérification des stocks critiques...');

        try {
            $force = $this->option('force');

            if ($force) {
                $this->warn('Mode forcé activé - Vérification complète de tous les stocks');
            }

            $products = Product::whereNotNull('stock_couleurs')->get();
            $this->info("📦 {$products->count()} produits à vérifier");

            $criticalCount = 0;
            $lowStockCount = 0;
            $outOfStockCount = 0;

            $progressBar = $this->output->createProgressBar($products->count());
            $progressBar->start();

            foreach ($products as $product) {
                if (is_array($product->stock_couleurs)) {
                    foreach ($product->stock_couleurs as $colorStock) {
                        if (is_array($colorStock) && isset($colorStock['name']) && isset($colorStock['quantity'])) {
                            $quantity = $colorStock['quantity'] ?? 0;
                            $colorName = $colorStock['name'];

                            if ($quantity <= 0) {
                                $outOfStockCount++;
                                $this->newLine();
                                $this->error("🚨 RUPTURE: {$product->name} - {$colorName}");

                                if ($force || $this->shouldNotify($product, $colorName)) {
                                    $notificationService->notifyCriticalStockOut($product, $colorName);
                                    $this->info("   ✅ Notification envoyée");
                                } else {
                                    $this->warn("   ⏭️  Notification déjà envoyée récemment");
                                }
                            } elseif ($quantity <= 5) {
                                $lowStockCount++;
                                $this->newLine();
                                $this->warn("⚠️  STOCK FAIBLE: {$product->name} - {$colorName} ({$quantity} unités)");

                                if ($force || $this->shouldNotify($product, $colorName)) {
                                    $notificationService->notifyStockChange($product, $colorName, $quantity + 1, $quantity);
                                    $this->info("   ✅ Notification envoyée");
                                } else {
                                    $this->warn("   ⏭️  Notification déjà envoyée récemment");
                                }
                            }
                        }
                    }
                }
                $progressBar->advance();
            }

            $progressBar->finish();
            $this->newLine(2);

            // Résumé
            $this->info('📊 RÉSUMÉ DE LA VÉRIFICATION:');
            $this->table(
                ['Statut', 'Nombre'],
                [
                    ['🟢 Stock Normal', $products->count() - $outOfStockCount - $lowStockCount],
                    ['🟡 Stock Faible', $lowStockCount],
                    ['🔴 Rupture de Stock', $outOfStockCount],
                ]
            );

            if ($outOfStockCount > 0) {
                $this->error("🚨 ATTENTION: {$outOfStockCount} couleurs en rupture de stock !");
                $this->error("   Action immédiate requise pour ces produits.");
            }

            if ($lowStockCount > 0) {
                $this->warn("⚠️  ALERTE: {$lowStockCount} couleurs avec stock faible !");
                $this->warn("   Commande en urgence recommandée.");
            }

            if ($outOfStockCount === 0 && $lowStockCount === 0) {
                $this->info("✅ Tous les stocks sont dans des niveaux acceptables.");
            }

            // Log de la vérification
            Log::info('Vérification des stocks critiques terminée', [
                'total_products' => $products->count(),
                'out_of_stock' => $outOfStockCount,
                'low_stock' => $lowStockCount,
                'force_mode' => $force,
                'timestamp' => now()->toISOString()
            ]);

            $this->info('✅ Vérification terminée avec succès !');

        } catch (\Exception $e) {
            $this->error('❌ Erreur lors de la vérification: ' . $e->getMessage());
            Log::error('Erreur lors de la vérification des stocks critiques: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }

    /**
     * Déterminer si une notification doit être envoyée
     */
    private function shouldNotify(Product $product, string $colorName): bool
    {
        // Vérifier si une notification a été envoyée récemment (dans les dernières 24h)
        $recentNotification = \App\Models\AdminMessage::where('title', 'like', '%' . $product->name . '%')
            ->where('title', 'like', '%' . $colorName . '%')
            ->where('created_at', '>', now()->subHours(24))
            ->first();

        return !$recentNotification;
    }
}
