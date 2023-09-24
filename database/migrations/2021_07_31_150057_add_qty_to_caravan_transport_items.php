<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddQtyToCaravanTransportItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('caravan_transport_items')) {
            Schema::table('caravan_transport_items', function (Blueprint $table) {
                $table->integer('qty');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('caravan_transport_items')) {
            Schema::table('caravan_transport_items', function (Blueprint $table) {
                $table->dropColumn('qty');
            });
        }
    }
}
