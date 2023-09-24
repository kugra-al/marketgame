<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTicksToBuildingsLevels extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('buildings_levels_costs', function (Blueprint $table) {
            $table->dropColumn('ticks');
            $table->dropColumn('workers');
        });

        Schema::table('buildings_levels', function (Blueprint $table) {
            $table->integer('ticks');
            $table->integer('workers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('buildings_levels', function (Blueprint $table) {
            $table->dropColumn('ticks');
            $table->dropColumn('workers');
        });
    }
}
