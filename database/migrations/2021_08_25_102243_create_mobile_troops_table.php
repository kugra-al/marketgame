<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMobileTroopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mobile_troops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mobile_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('troop_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->integer('qty')->default(0);
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
        Schema::dropIfExists('mobile_troops');
    }
}
