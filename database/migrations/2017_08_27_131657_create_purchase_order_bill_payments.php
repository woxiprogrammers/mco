<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchaseOrderBillPayments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_order_bill_payments', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('purchase_order_bill_id');
            $table->unsignedInteger('payment_id');
            $table->double('amount');
            $table->string('reference_number',255);
            $table->foreign('purchase_order_bill_id')->references('id')->on('purchase_order_bills')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('payment_id')->references('id')->on('payment_types')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('purchase_order_bill_payments');
    }
}
