<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchaseOrderTransactionImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_order_transaction_images', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',255);
            $table->unsignedBigInteger('purchase_order_transaction_id');
            $table->foreign('purchase_order_transaction_id')->references('id')->on('purchase_order_transactions')->onDelete('cascade')->onUpdate('cascade');
            $table->boolean('is_pre_grn')->default('false');
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
        Schema::dropIfExists('purchase_order_transaction_images');
    }
}
