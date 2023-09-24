<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTargetsToArmiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('armies', function (Blueprint $table) {
            $table->foreignId('target_city_id')->nullable()->references('id')->on('cities')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('target_caravan_id')->nullable()->references('id')->on('caravans')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('target_army_id')->nullable()->references('id')->on('armies')->onUpdate('cascade')->onDelete('cascade');
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
