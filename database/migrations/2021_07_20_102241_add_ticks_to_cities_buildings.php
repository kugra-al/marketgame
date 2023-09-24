<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTicksToCitiesBuildings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cities_buildings', function (Blueprint $table) {
            $table->integer('upgrade_ticks_remaining');
            $table->integer('current_workers');
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
            $table->dropColumns(['upgrade_ticks_remaining','current_workers']);
        });
    }
}
