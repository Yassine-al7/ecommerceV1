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
            // Supprimer les anciennes colonnes si elles existent
            if (Schema::hasColumn('pending_registrations', 'name')) {
                $table->dropColumn('name');
            }
            if (Schema::hasColumn('pending_registrations', 'password_hash')) {
                $table->dropColumn('password_hash');
            }
            if (Schema::hasColumn('pending_registrations', 'code')) {
                $table->dropColumn('code');
            }

            // Ajouter la nouvelle colonne verification_code si elle n'existe pas
            if (!Schema::hasColumn('pending_registrations', 'verification_code')) {
                $table->string('verification_code', 6)->after('email');
            }
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
