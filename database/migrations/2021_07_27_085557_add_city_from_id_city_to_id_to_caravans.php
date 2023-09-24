<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCityFromIdCityToIdToCaravans extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('caravans', function (Blueprint $table) {
            $table->foreignId('city_id_from')->reference('id')->on('cities')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('city_id_to')->reference('id')->on('cities')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('caravans', function (Blueprint $table) {
            $table->dropColumn('city_id_from');
            $table->dropColumn('city_id_to');
            //
        });
    }
}
