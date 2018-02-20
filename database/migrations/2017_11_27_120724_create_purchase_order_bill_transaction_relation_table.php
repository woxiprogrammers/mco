<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchaseOrderBillTransactionRelationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_order_bill_transaction_relation', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('purchase_order_transaction_id');
            $table->foreign('purchase_order_transaction_id')->references('id')->on('purchase_order_transactions')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('purchase_order_bill_id');
            $table->foreign('purchase_order_bill_id')->references('id')->on('purchase_order_bills')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('purchase_order_bill_transaction_relation');
    }
}
