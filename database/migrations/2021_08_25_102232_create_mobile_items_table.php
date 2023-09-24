<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\MobileItem;

class CreateMobileItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mobile_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mobile_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('item_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->integer('qty')->default(0);
            $table->integer('type')->default(MobileItem::TYPE_SEND);
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
        Schema::dropIfExists('mobile_items');
    }
}
