<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTicksToCityLevels extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('city_level_costs', function (Blueprint $table) {
            $table->dropColumn('ticks');
        });

        Schema::table('city_levels', function (Blueprint $table) {
            $table->integer('ticks');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('city_levels', function (Blueprint $table) {
            $table->dropColumn('ticks');
        });
    }
}
