<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMarketOrderTransportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('market_order_transports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('market_order_id')->references('id')->on('market_orders')->onUpdate('cascade')->onDelete('cascade');
            $table->float('qty',8,2);
            $table->foreignId('transport_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('market_order_transports');
    }
}
