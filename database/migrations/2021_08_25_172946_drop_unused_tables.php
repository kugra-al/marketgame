<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropUnusedTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('armies');
        Schema::dropIfExists('army_items');
        Schema::dropIfExists('army_transports');
        Schema::dropIfExists('army_troops');
        Schema::dropIfExists('caravans');
        Schema::dropIfExists('caravan_items');
        Schema::dropIfExists('caravan_transports');
        Schema::dropIfExists('city_transport_items');
        Schema::enableForeignKeyConstraints();

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
