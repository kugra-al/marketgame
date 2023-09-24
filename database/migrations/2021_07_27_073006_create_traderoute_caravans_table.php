<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTraderouteCaravansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('traderoute_caravans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('traderoute_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('caravan_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('traderoute_caravans');
    }
}
