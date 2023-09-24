<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTicksToCaravans extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('caravans', function (Blueprint $table) {
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
        Schema::table('caravans', function (Blueprint $table) {
            $table->dropColumn('ticks');
            $table->dropColumn('ticks_remaining');
        });
    }
}
