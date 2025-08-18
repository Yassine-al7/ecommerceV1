<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommandesTable extends Migration
{
    public function up()
    {
        Schema::create('commandes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('reference');
            $table->string('nom_client');
            $table->string('ville');
            $table->string('adresse_client');
            $table->string('numero_telephone_client');
            $table->json('produits');
            $table->string('taille_produit');
            $table->integer('quantite_produit');
            $table->decimal('prix_produit');
            $table->decimal('prix_commande');
            $table->enum('status', [
                'livré',
                'retourné',
                'pas de réponse',
                'en attente',
                'en livraison',
                'refusé confirmé',
                'non confirmé',
            ]);
            $table->text('commentaire');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('commandes');
    }
}
