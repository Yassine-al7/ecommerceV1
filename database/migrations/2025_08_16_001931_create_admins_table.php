<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminsTable extends Migration
{
    public function up()
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nom_complet');
            $table->string('email');
            $table->string('mot_de_passe');
            $table->string('confirmation_mot_de_passe');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('admins');
    }
}
