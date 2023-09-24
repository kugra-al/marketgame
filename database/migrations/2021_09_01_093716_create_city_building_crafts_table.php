<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCityBuildingCraftsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('city_building_crafts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_building_id')->references('id')->on('cities_buildings')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('item_recipe_id')->references('id')->on('item_recipes')->onUpdate('cascade')->onDelete('cascade');
            $table->float('qty',8,2);
            $table->integer('ticks');
            $table->integer('ticks_remaining');
            $table->integer('workers');
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
        Schema::dropIfExists('city_building_crafts');
    }
}
