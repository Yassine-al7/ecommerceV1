<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('user_id');
            $table->decimal('prix_admin', 10, 2)->nullable();
            $table->decimal('prix_vente', 10, 2)->nullable();
            $table->boolean('visible')->default(true);
            $table->timestamps();
            $table->unique(['product_id','user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_user');
    }
};


