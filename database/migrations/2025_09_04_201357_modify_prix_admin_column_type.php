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
        Schema::table('produits', function (Blueprint $table) {
            // Changer le type de colonne de decimal à text pour stocker du JSON
            $table->text('prix_admin')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('produits', function (Blueprint $table) {
            // Revenir au type decimal (attention: peut causer des erreurs si des données JSON existent)
            $table->decimal('prix_admin', 8, 2)->change();
        });
    }
};
