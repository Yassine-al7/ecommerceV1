<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Nettoyer les lignes orphelines avant d'ajouter les clés étrangères
        DB::table('product_user')
            ->whereNotExists(function ($q) {
                $q->select(DB::raw(1))
                  ->from('produits')
                  ->whereColumn('produits.id', 'product_user.product_id');
            })
            ->delete();

        DB::table('product_user')
            ->whereNotExists(function ($q) {
                $q->select(DB::raw(1))
                  ->from('users')
                  ->whereColumn('users.id', 'product_user.user_id');
            })
            ->delete();

        Schema::table('product_user', function (Blueprint $table) {
            // Ajouter les clés étrangères avec suppression en cascade
            $table->foreign('product_id')
                ->references('id')->on('produits')
                ->onDelete('cascade');

            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('product_user', function (Blueprint $table) {
            // Supprimer les contraintes FK si présentes
            try { $table->dropForeign(['product_id']); } catch (\Throwable $e) {}
            try { $table->dropForeign(['user_id']); } catch (\Throwable $e) {}
        });
    }
};


