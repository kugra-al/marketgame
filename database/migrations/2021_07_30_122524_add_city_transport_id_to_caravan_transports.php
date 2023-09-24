<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCityTransportIdToCaravanTransports extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('caravan_transports', function (Blueprint $table) {
            Schema::disableForeignKeyConstraints();
            if (Schema::hasColumn('caravan_transports','transport_id')) {
                $table->dropForeign(['transport_id']);
                $table->dropColumn('transport_id');
            }
            if (!Schema::hasColumn('caravan_transports','city_transport_id')) {
                $table->foreignId('city_transport_id')->references('id')->on('city_transports')->onUpdate('cascade')->onDelete('cascade');
            }
            Schema::enableForeignKeyConstraints();
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
            Schema::disableForeignKeyConstraints();
            if (Schema::hasColumn('caravan_transports','city_transport_id')) {
                $table->dropForeign(['city_transport_id']);
                $table->dropColumn('city_transport_id');
            }
            Schema::enableForeignKeyConstraints();
            if (!Schema::hasColumn('caravan_transports','transport_id')) {
                $table->foreignId('transport_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            }
        });
    }
}
