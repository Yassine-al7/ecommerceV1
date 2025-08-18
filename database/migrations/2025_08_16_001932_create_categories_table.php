<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriesTable extends Migration
{
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $enum_values = [
            'tshirt_homme', 'pantalon_homme', 'chemise_homme',
            'tshirt_femme', 'pantalon_femme', 'chemise_femme',
            'robe_femme', 'jupe_femme', 'manteau_homme', 'manteau_femme',
            'chaussures_homme', 'chaussures_femme', 'accessoires_homme', 'accessoires_femme',
            'montres_homme', 'montres_femme', 'sacs_homme', 'sacs_femme',
            'lunettes_homme', 'lunettes_femme',
        ];
            $table->id();
            $table->enum('name', $enum_values);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('categories');
    }
}
