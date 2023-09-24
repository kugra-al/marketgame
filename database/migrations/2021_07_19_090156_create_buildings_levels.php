<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBuildingsLevels extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('buildings_levels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('building_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->integer('level');
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
        Schema::dropIfExists('buildings_levels');
    }
}
