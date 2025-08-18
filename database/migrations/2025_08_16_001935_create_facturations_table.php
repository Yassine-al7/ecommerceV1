<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFacturationsTable extends Migration
{
    public function up()
    {
        Schema::create('facturations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nom_vendeur');
            $table->decimal('somme_prix_produits_vendus');
            $table->decimal('somme_prix_benefices');
            $table->decimal('difference');
            $table->enum('status', ['payé', 'non payé']);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('facturations');
    }
}
