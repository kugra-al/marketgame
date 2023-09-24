<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemRecipeItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_recipe_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_recipe_id')->references('id')->on('item_recipes')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('item_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->decimal('qty',8,2)->default(0);
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
        Schema::dropIfExists('item_recipe_items');
    }
}
