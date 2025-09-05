<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class FixOrderStatusEnum extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // D'abord, convertir la colonne enum en string temporairement
        Schema::table('commandes', function (Blueprint $table) {
            $table->string('status_temp')->nullable();
        });

        // Copier les données existantes
        DB::statement('UPDATE commandes SET status_temp = status');

        // Supprimer l'ancienne colonne enum
        Schema::table('commandes', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        // Recréer la colonne avec le bon enum
        Schema::table('commandes', function (Blueprint $table) {
            $table->enum('status', [
                'en attente',
                'confirme',
                'en livraison',
                'livre',
                'problematique',
                'pas de réponse',
                'annulé',
                'retourné',
                'non confirmé'
            ])->default('en attente');
        });

        // Mettre à jour les anciennes valeurs pour qu'elles correspondent au nouvel enum
        DB::statement("
            UPDATE commandes
            SET status_temp = CASE
                WHEN status_temp = 'livré' THEN 'livre'
                WHEN status_temp = 'refusé confirmé' THEN 'confirme'
                ELSE status_temp
            END
        ");

        // Copier les données mises à jour
        DB::statement('UPDATE commandes SET status = status_temp');

        // Supprimer la colonne temporaire
        Schema::table('commandes', function (Blueprint $table) {
            $table->dropColumn('status_temp');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Reconvertir en string temporairement
        Schema::table('commandes', function (Blueprint $table) {
            $table->string('status_temp')->nullable();
        });

        // Copier les données
        DB::statement('UPDATE commandes SET status_temp = status');

        // Supprimer la nouvelle colonne enum
        Schema::table('commandes', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        // Recréer l'ancienne colonne enum
        Schema::table('commandes', function (Blueprint $table) {
            $table->enum('status', [
                'livré',
                'retourné',
                'pas de réponse',
                'en attente',
                'en livraison',
                'refusé confirmé',
                'non confirmé',
            ]);
        });

        // Restaurer les anciennes valeurs
        DB::statement("
            UPDATE commandes
            SET status_temp = CASE
                WHEN status_temp = 'livre' THEN 'livré'
                WHEN status_temp = 'confirme' THEN 'refusé confirmé'
                ELSE status_temp
            END
        ");

        // Copier les données restaurées
        DB::statement('UPDATE commandes SET status = status_temp');

        // Supprimer la colonne temporaire
        Schema::table('commandes', function (Blueprint $table) {
            $table->dropColumn('status_temp');
        });
    }
}
