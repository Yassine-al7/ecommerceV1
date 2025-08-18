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
        // Drop foreign key constraints first if they exist
        if (Schema::hasTable('produits')) {
            Schema::table('produits', function (Blueprint $table) {
                // Check if the foreign key exists before trying to drop it
                $foreignKeys = $this->getForeignKeyConstraints('produits');
                if (in_array('produits_categorie_id_foreign', $foreignKeys)) {
                    $table->dropForeign(['categorie_id']);
                }
            });
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
            Schema::table('produits', function (Blueprint $table) {
                $table->foreign('categorie_id')->references('id')->on('categories');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('produits')) {
            Schema::table('produits', function (Blueprint $table) {
                $foreignKeys = $this->getForeignKeyConstraints('produits');
                if (in_array('produits_categorie_id_foreign', $foreignKeys)) {
                    $table->dropForeign(['categorie_id']);
                }
            });
        }

        Schema::dropIfExists('categories');
    }

    /**
     * Get foreign key constraints for a table
     */
    private function getForeignKeyConstraints($tableName)
    {
        $foreignKeys = [];
        $constraints = DB::select("
            SELECT CONSTRAINT_NAME
            FROM information_schema.TABLE_CONSTRAINTS
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = ?
            AND CONSTRAINT_TYPE = 'FOREIGN KEY'
        ", [$tableName]);

        foreach ($constraints as $constraint) {
            $foreignKeys[] = $constraint->CONSTRAINT_NAME;
        }

        return $foreignKeys;
    }
};
