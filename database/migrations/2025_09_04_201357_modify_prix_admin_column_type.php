<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Changer le type de colonne de decimal à text pour stocker du JSON
        Schema::table('produits', function (Blueprint $table) {
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
        // Revenir au type decimal (attention: peut causer des erreurs si des données JSON existent)
        Schema::table('produits', function (Blueprint $table) {
            $table->decimal('prix_admin', 8, 2)->change();
        });
    }
};
