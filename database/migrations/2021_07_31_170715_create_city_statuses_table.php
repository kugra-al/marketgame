<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCityStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('city_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->text('msg');
            $table->text('type');
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
        Schema::dropIfExists('city_statuses');
    }
}
