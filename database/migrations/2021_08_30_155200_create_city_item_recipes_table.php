<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCityItemRecipesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('city_item_recipes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id')->references('id')->on('cities')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('recipe_id')->references('id')->on('item_recipes')->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('city_item_recipes');
    }
}
