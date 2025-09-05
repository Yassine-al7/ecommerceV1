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
        // Rendre quantite_stock nullable car il sera calculé automatiquement
        Schema::table('produits', function (Blueprint $table) {
            $table->integer('quantite_stock')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Revenir à la version précédente (non nullable)
        Schema::table('produits', function (Blueprint $table) {
            $table->integer('quantite_stock')->nullable(false)->change();
        });
    }
};
