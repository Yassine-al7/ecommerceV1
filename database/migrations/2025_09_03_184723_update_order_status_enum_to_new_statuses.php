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
        // Supprimer l'ancienne colonne status
        Schema::table('commandes', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        // Recréer la colonne avec les nouveaux statuts
        Schema::table('commandes', function (Blueprint $table) {
            $table->enum('status', [
                'en attente',
                'confirmé',
                'pas de réponse',
                'expédition',
                'livré',
                'annulé',
                'reporté',
                'retourné'
            ])->default('en attente')->after('seller_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Supprimer la nouvelle colonne status
        Schema::table('commandes', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        // Recréer l'ancienne colonne avec les anciens statuts
        Schema::table('commandes', function (Blueprint $table) {
            $table->enum('status', [
                'en attente',
                'confirme',
                'en livraison',
                'livre',
                'pas de réponse',
                'annulé',
                'retourné'
            ])->default('en attente')->after('seller_id');
        });
    }
};
