<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('produits', function (Blueprint $table) {
            $table->decimal('prix_admin', 10, 2)->nullable()->after('image');
            $table->decimal('prix_vente', 10, 2)->nullable()->after('prix_admin');
        });
    }

    public function down(): void
    {
        Schema::table('produits', function (Blueprint $table) {
            $table->dropColumn(['prix_admin', 'prix_vente']);
        });
    }
};


