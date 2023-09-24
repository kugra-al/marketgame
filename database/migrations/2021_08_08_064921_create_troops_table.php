<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTroopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('troops', function (Blueprint $table) {
            $table->id();
            $table->string('slug');
            $table->string('name');
            $table->float('attack',0,8);
            $table->float('defense',0,8);
            $table->float('ranged',0,8);
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
        Schema::dropIfExists('troops');
    }
}
