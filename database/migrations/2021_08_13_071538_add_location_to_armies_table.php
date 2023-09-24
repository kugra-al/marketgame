<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLocationToArmiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('armies', function (Blueprint $table) {
            $table->dropForeign(['city_id_to']);
            $table->dropColumn('city_id_to');
            $table->dropForeign(['city_id_from']);
            $table->dropColumn('city_id_from');
            $table->foreignId('location_from')->references('id')->on('locations')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('location_to')->references('id')->on('locations')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('armies', function (Blueprint $table) {
            //
        });
    }
}
