<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToCitiesItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cities_items', function (Blueprint $table) {
//            $table->dropIndex('city_id');
           // $table->dropForeign(['city_id']);
            $table->dropColumn('city_id');
           // $table->foreignId('city_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
       //     $table->dropForeign(['item_id']);
            $table->dropColumn('item_id');
            //$table->foreignId('item_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cities_items', function (Blueprint $table) {
        //    $table->dropForeign(['city_id']);
        //    $table->dropColumn('city_id');
            $table->foreignId('city_id')->change();
       //     $table->dropForeign(['item_id']);
        //    $table->dropColumn('item_id');           
            $table->foreignId('item_id')->change();
        });
    }
}
