<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('commandes', function (Blueprint $table) {
            // Supprimer les anciens champs de produit qui ne sont plus utilisés
            $table->dropColumn([
                'taille_produit',
                'quantite_produit',
                'prix_produit'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('commandes', function (Blueprint $table) {
            // Restaurer les anciens champs si on annule la migration
            $table->string('taille_produit')->nullable();
            $table->integer('quantite_produit')->nullable();
            $table->decimal('prix_produit')->nullable();
        });
    }
};
