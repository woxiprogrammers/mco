<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchaseOrderBillImagesTable extends Migration
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
            $table->unsignedInteger('purchase_order_bill_id');
            $table->string('name',255);
            $table->boolean('is_payment_image')->default(false);
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
