<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTraderoutesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('traderoutes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('city_id_from')->reference('id')->on('cities')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('city_id_to')->reference('id')->on('cities')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('traderoutes');
    }
}
