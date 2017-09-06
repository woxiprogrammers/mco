<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchaseOrderComponentImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_order_component_images', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('purchase_order_component_id');
            $table->string('name',255);
            $table->foreign('purchase_order_component_id')->references('id')->on('purchase_order_components')->onDelete('cascade')->onUpdate('cascade');
            $table->string('caption',255);
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
        Schema::dropIfExists('purchase_order_component_images');
    }
}
