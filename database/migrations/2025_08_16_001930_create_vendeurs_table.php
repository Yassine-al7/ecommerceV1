<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendeursTable extends Migration
{
    public function up()
    {
        Schema::create('vendeurs', function (Blueprint $table) {
            $table->id();
            $table->string('nom_complet');
            $table->string('email');
            $table->string('mot_de_passe');
            $table->string('confirmation_mot_de_passe');
            $table->string('rib');
            $table->string('store');
            $table->string('numero_telephone');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('vendeurs');
    }
}
