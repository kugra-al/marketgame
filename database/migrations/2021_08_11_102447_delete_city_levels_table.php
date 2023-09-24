<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DeleteCityLevelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        if (Schema::hasTable('city_levels')) {
            Schema::drop('city_levels');
        }
        Schema::table('city_level_costs', function (Blueprint $table) {
            $table->dropForeign(['city_level_id']);
            $table->dropColumn('city_level_id');
            if (!Schema::hasColumn('city_level_costs','level'))
                $table->integer('level');
        });
        Schema::table('cities', function (Blueprint $table) {
            $table->dropForeign(['city_level_id']);
            $table->dropColumn('city_level_id');
            if (!Schema::hasColumn('cities','level'))
                $table->integer('level');
        });
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('city_level_costs', function (Blueprint $table) {
            $table->dropColumn('level');
        });
        Schema::table('cities', function (Blueprint $table) {
            $table->dropColumn('level');
        });
    }
}
