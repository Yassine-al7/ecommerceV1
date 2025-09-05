<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Added missing import for DB facade

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For SQLite, we can't easily check foreign keys, so we'll try to drop them safely
        if (Schema::hasTable('produits')) {
            try {
                Schema::table('produits', function (Blueprint $table) {
                    $table->dropForeign(['categorie_id']);
                });
            } catch (Exception $e) {
                // Foreign key might not exist, continue
            }
        }

        Schema::dropIfExists('categories');

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->string('slug')->nullable();
            $table->timestamps();
        });

        // Recreate foreign key constraint
        if (Schema::hasTable('produits')) {
            try {
                Schema::table('produits', function (Blueprint $table) {
                    $table->foreign('categorie_id')->references('id')->on('categories');
                });
            } catch (Exception $e) {
                // Foreign key creation might fail, continue
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('produits')) {
            try {
                Schema::table('produits', function (Blueprint $table) {
                    $table->dropForeign(['categorie_id']);
                });
            } catch (Exception $e) {
                // Foreign key might not exist, continue
            }
        }

        Schema::dropIfExists('categories');
    }
};
