<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBuildingLevelRecipesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('building_level_recipes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('building_level_id')->references('id')->on('buildings_levels')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('building_level_recipes');
    }
}
