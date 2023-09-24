<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddQtyToCityTransports extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('caravan_transports', function (Blueprint $table) {
            $table->integer('qty');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('caravan_transports', function (Blueprint $table) {
            $table->dropColumn('qty');
        });
    }
}
