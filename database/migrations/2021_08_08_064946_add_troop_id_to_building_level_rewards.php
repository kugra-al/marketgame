<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTroopIdToBuildingLevelRewards extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('building_level_rewards', function (Blueprint $table) {
            if (!Schema::hasColumn('building_level_rewards','troop_id')) {
                $table->foreignId('troop_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('building_level_rewards', function (Blueprint $table) {
            //$table->dropForeign(['troop_id']);
            $table->dropColumn('troop_id');
        });
    }
}
