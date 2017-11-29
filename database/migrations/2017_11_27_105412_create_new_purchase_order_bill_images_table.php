<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewPurchaseOrderBillImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_order_bill_images', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',255);
            $table->unsignedBigInteger('purchase_order_bill_id');
            $table->foreign('purchase_order_bill_id')->references('id')->on('purchase_order_bills')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('purchase_order_bill_images');
    }
}
