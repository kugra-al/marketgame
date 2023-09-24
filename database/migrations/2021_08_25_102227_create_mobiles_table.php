<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Mobile;

class CreateMobilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mobiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->integer('returning')->default(0);
            $table->integer('ticks');
            $table->integer('ticks_remaining');
            $table->foreignId('location_id_from')->references('id')->on('locations');
            $table->foreignId('location_id_to')->references('id')->on('locations');
            $table->integer('state')->default(Mobile::STATE_MOVE);
            $table->foreignId('target_mobile_id')->nullable()->references('id')->on('mobiles');
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
        Schema::dropIfExists('mobiles');
    }
}
