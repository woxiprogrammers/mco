<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorkOrderImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('work_order_images', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('quotation_work_order_id');
            $table->foreign('quotation_work_order_id')->references('id')->on('quotation_work_orders')->onUpdate('cascade')->onDelete('cascade');
            $table->string('image',255);
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
        Schema::dropIfExists('work_order_images');
    }
}
