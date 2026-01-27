<?php

namespace App\Traits;

use App\Models\Order;

trait GeneratesOrderReferences
{
    /**
     * Génère une référence unique pour une commande
     * Format: CMD-YYYYMMDD-XXXX (où XXXX est un nombre aléatoire à 4 chiffres)
     */
    protected function generateUniqueOrderReference(): string
    {
        do {
            $date = now()->format('Ymd');
            $random = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            $reference = "CMD-{$date}-{$random}";
        } while (Order::where('reference', $reference)->exists());

        return $reference;
    }

    /**
     * Génère une référence unique avec un préfixe personnalisé
     * Format: PREFIX-YYYYMMDD-XXXX
     */
    protected function generateUniqueOrderReferenceWithPrefix(string $prefix): string
    {
        do {
            $date = now()->format('Ymd');
            $random = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            $reference = "{$prefix}-{$date}-{$random}";
        } while (Order::where('reference', $reference)->exists());

        return $reference;
    }
}
