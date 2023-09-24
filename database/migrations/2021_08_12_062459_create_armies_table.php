<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArmiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('armies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('city_id_from')->references('id')->on('cities')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('city_id_to')->references('id')->on('cities')->onUpdate('cascade')->onDelete('cascade');
            $table->boolean('returning')->default(false);         
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
        Schema::dropIfExists('armies');
    }
}
