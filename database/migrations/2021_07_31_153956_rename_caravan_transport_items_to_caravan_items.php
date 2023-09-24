<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameCaravanTransportItemsToCaravanItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        if (Schema::hasColumn('caravan_transport_items','caravan_transport_id')) {
            Schema::table('caravan_transport_items', function (Blueprint $table) {
                $table->dropForeign(['caravan_transport_id']);
            });
            Schema::table('caravan_transport_items', function (Blueprint $table) {
                $table->dropColumn('caravan_transport_id');
            });
        }

        if (Schema::hasTable('caravan_transport_items')) {
            Schema::rename('caravan_transport_items','caravan_items');
        }

        Schema::table('caravan_items', function (Blueprint $table) {
            
            if (!Schema::hasColumn('caravan_items','caravan_id')) {
                $table->foreignId('caravan_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            }
        });
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('caravan_items')) {
            Schema::rename('caravan_items','caravan_transport_items');
        }

        Schema::table('caravan_transport_items', function (Blueprint $table) {
            if (Schema::hasColumn('caravan_transport_items','caravan_id')) {
                $table->dropForeign(['caravan_id']);
                $table->dropColumn('caravan_id');
            }
            if (!Schema::hasColumn('caravan_transport_items','caravan_transport_id')) {
                $table->foreignId('caravan_transport_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            }
        });
    }
}
