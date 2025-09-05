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
        // Utiliser la syntaxe Laravel compatible avec SQLite
        Schema::table('produits', function (Blueprint $table) {
            $table->unsignedBigInteger('vendeur_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restaurer la colonne vendeur_id pour la rendre non-nullable
        Schema::table('produits', function (Blueprint $table) {
            $table->unsignedBigInteger('vendeur_id')->nullable(false)->change();
        });
    }
};
