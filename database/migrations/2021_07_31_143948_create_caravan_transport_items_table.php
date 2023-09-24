<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCaravanTransportItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('caravan_transport_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caravan_transport_id')->references('id')->on('caravan_transports')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('item_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('caravan_transport_items');
    }
}
