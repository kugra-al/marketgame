<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCityTroopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('city_troops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('troop_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('city_troops');
    }
}
