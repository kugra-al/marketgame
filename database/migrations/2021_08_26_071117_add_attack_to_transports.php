<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAttackToTransports extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mobile_transports', function (Blueprint $table) {
            $table->dropColumn('attack');
        });

        Schema::table('transports', function (Blueprint $table) {
            $table->integer('attack')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transports', function (Blueprint $table) {
            $table->dropColumn('attack');
        });
                
        Schema::table('mobile_transports', function (Blueprint $table) {
            $table->integer('attack')->default(1);
        });
    }
}
