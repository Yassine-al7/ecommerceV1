<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pending_registrations', function (Blueprint $table) {
            // Supprimer les anciennes colonnes
            $table->dropColumn(['name', 'password_hash', 'code']);

            // Ajouter la nouvelle colonne verification_code
            $table->string('verification_code', 6)->after('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pending_registrations', function (Blueprint $table) {
            // Restaurer les anciennes colonnes
            $table->string('name')->after('id');
            $table->text('password_hash')->after('email');
            $table->string('code')->after('password_hash');

            // Supprimer verification_code
            $table->dropColumn('verification_code');
        });
    }
};
