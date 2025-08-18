<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProduitsTable extends Migration
{
    public function up()
    {
        Schema::create('produits', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('couleur');
            $table->json('tailles');
            $table->string('image');
            $table->integer('quantite_stock');
            $table->unsignedBigInteger('categorie_id');
            $table->unsignedBigInteger('vendeur_id');
            $table->timestamps();
            $table->foreign('categorie_id')->references('id')->on('categories')->onDelete('cascade');
            $table->foreign('vendeur_id')->references('id')->on('vendeurs')->onDelete('cascade');

        });
    }

    public function down()
    {
        Schema::dropIfExists('produits');
    }
}
