<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCostToMarketOrderItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('market_order_items', function (Blueprint $table) {
            $table->float('cost',8,2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('market_order_items', function (Blueprint $table) {
            $table->dropColumn('cost');
        });
    }
}
