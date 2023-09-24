<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTicksToArmiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('armies', function (Blueprint $table) {
            $table->integer('ticks');
            $table->integer('ticks_remaining');   
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
            $table->dropColumn('ticks');
            $table->dropColumn('ticks_remaining');
        });
    }
}
