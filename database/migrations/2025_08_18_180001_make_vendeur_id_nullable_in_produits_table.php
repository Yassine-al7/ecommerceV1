<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modifier la colonne vendeur_id pour la rendre nullable
        DB::statement('ALTER TABLE produits MODIFY COLUMN vendeur_id BIGINT UNSIGNED NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restaurer la colonne vendeur_id pour la rendre non-nullable
        DB::statement('ALTER TABLE produits MODIFY COLUMN vendeur_id BIGINT UNSIGNED NOT NULL');
    }
};
