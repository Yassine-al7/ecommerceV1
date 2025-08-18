<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;

class CleanupBrokenImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:cleanup {--dry-run : Afficher seulement les images cassées sans les supprimer}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Nettoyer les références d\'images cassées dans la base de données';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Recherche des images cassées...');

        $products = Product::whereNotNull('image')->get();
        $brokenImages = [];
        $fixedCount = 0;

        foreach ($products as $product) {
            $imagePath = $product->image;

            if (empty($imagePath)) {
                continue;
            }

            // Déterminer le chemin complet du fichier
            $relativePath = str_replace('/storage/', '', $imagePath);
            $fullPath = storage_path('app/public/' . $relativePath);

            if (!file_exists($fullPath)) {
                $brokenImages[] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'image' => $imagePath,
                    'full_path' => $fullPath
                ];

                if (!$this->option('dry-run')) {
                    // Mettre l'image à null
                    $product->update(['image' => null]);
                    $fixedCount++;
                }
            }
        }

        if (empty($brokenImages)) {
            $this->info('✅ Aucune image cassée trouvée !');
            return;
        }

        $this->warn('⚠️  ' . count($brokenImages) . ' image(s) cassée(s) trouvée(s) :');

        foreach ($brokenImages as $broken) {
            $this->line("  • Produit ID {$broken['id']} ({$broken['name']}): {$broken['image']}");
        }

        if ($this->option('dry-run')) {
            $this->info('🔍 Mode dry-run : Aucune modification effectuée.');
            $this->info('Exécutez sans --dry-run pour corriger automatiquement.');
        } else {
            $this->info("✅ {$fixedCount} référence(s) d'image(s) nettoyée(s) !");
        }
    }
}
