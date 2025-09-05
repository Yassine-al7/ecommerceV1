<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;

class FixDoubleEncoding extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:double-encoding';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix double JSON encoding in product data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== Correction du double encodage JSON ===');

        $products = Product::all();
        $fixed = 0;

        foreach ($products as $product) {
            $this->info("Traitement du produit: {$product->name} (ID: {$product->id})");

            $updated = false;

            // Corriger stock_couleurs si c'est une chaîne
            if (is_string($product->getRawOriginal('stock_couleurs'))) {
                $rawStock = $product->getRawOriginal('stock_couleurs');
                $this->line("  - stock_couleurs est une chaîne: {$rawStock}");

                $decoded = $this->cleanJsonString($rawStock);
                if ($decoded !== null) {
                    $product->stock_couleurs = $decoded;
                    $updated = true;
                    $this->line("  - stock_couleurs corrigé: " . json_encode($decoded));
                } else {
                    $this->error("  - Erreur de décodage stock_couleurs: " . json_last_error_msg());
                }
            }

            // Corriger couleur si c'est une chaîne
            if (is_string($product->getRawOriginal('couleur'))) {
                $rawCouleur = $product->getRawOriginal('couleur');
                $this->line("  - couleur est une chaîne: {$rawCouleur}");

                $decoded = $this->cleanJsonString($rawCouleur);
                if ($decoded !== null) {
                    $product->couleur = $decoded;
                    $updated = true;
                    $this->line("  - couleur corrigé: " . json_encode($decoded));
                } else {
                    $this->error("  - Erreur de décodage couleur: " . json_last_error_msg());
                }
            }

            // Corriger hidden_colors si c'est une chaîne
            if (is_string($product->getRawOriginal('hidden_colors'))) {
                $rawHidden = $product->getRawOriginal('hidden_colors');
                $this->line("  - hidden_colors est une chaîne: {$rawHidden}");

                $decoded = $this->cleanJsonString($rawHidden);
                if ($decoded !== null) {
                    $product->hidden_colors = $decoded;
                    $updated = true;
                    $this->line("  - hidden_colors corrigé: " . json_encode($decoded));
                } else {
                    $this->error("  - Erreur de décodage hidden_colors: " . json_last_error_msg());
                }
            }

            // Corriger tailles si c'est une chaîne
            if (is_string($product->getRawOriginal('tailles'))) {
                $rawTailles = $product->getRawOriginal('tailles');
                $this->line("  - tailles est une chaîne: {$rawTailles}");

                $decoded = $this->cleanJsonString($rawTailles);
                if ($decoded !== null) {
                    $product->tailles = $decoded;
                    $updated = true;
                    $this->line("  - tailles corrigé: " . json_encode($decoded));
                } else {
                    $this->error("  - Erreur de décodage tailles: " . json_last_error_msg());
                }
            }

            if ($updated) {
                $product->save();
                $this->info("  ✅ Produit sauvegardé");
                $fixed++;
            } else {
                $this->line("  - Aucune correction nécessaire");
            }

            $this->line("");
        }

        $this->info("=== Correction terminée - {$fixed} produits corrigés ===");
    }

    private function cleanJsonString($rawString)
    {
        // Nettoyer la chaîne JSON
        $cleaned = str_replace('\\"', '"', $rawString);
        $cleaned = str_replace('\\\\', '\\', $cleaned);

        // Si la chaîne commence et se termine par des guillemets, les supprimer
        if (strlen($cleaned) >= 2 && $cleaned[0] === '"' && $cleaned[-1] === '"') {
            $cleaned = substr($cleaned, 1, -1);
        }

        return json_decode($cleaned, true);
    }
}
