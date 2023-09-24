<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTransportIdToBuildingLevelsRewards extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::table('building_level_rewards', function (Blueprint $table) {
            if (Schema::hasColumn('building_level_rewards','item_id')) {
                $table->dropForeign('buildings_levels_items_item_id_foreign');   
            }
            $table->dropColumn('item_id');         
        });
        Schema::table('building_level_rewards', function (Blueprint $table) {
            $table->foreignId('item_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('transport_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
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
        Schema::table('building_level_rewards', function (Blueprint $table) {
            $table->dropColumn('transport_id');
        });
    }
}
