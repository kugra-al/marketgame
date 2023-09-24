<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBuildingLevelTroopCostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('building_level_troop_costs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('building_level_troop_id')->references('id')->on('building_level_troops')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('item_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->integer('qty');
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
        Schema::dropIfExists('building_level_troop_costs');
    }
}
