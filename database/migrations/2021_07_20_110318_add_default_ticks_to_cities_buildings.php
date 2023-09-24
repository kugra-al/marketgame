<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDefaultTicksToCitiesBuildings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cities_buildings', function (Blueprint $table) {
            $table->integer('upgrade_ticks_remaining')->change()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cities_buildings', function (Blueprint $table) {
            $table->integer('upgrade_ticks_remaining')->change();
        });
    }
}
