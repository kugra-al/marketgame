<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMarketOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('market_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id_from')->reference('id')->on('cities')->onUpdate('cascade')->onDelete('cascade')->nullable();
            $table->foreignId('city_id_to')->reference('id')->on('cities')->onUpdate('cascade')->onDelete('cascade')->nullable();
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
        Schema::dropIfExists('market_orders');
    }
}
