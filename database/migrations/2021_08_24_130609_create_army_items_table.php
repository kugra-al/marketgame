<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArmyItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('army_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('army_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('item_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->integer('qty');
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
        Schema::dropIfExists('army_items');
    }
}
