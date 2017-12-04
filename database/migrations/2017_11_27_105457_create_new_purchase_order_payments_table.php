<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewPurchaseOrderPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_order_payments', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('purchase_order_bill_id');
            $table->foreign('purchase_order_bill_id')->references('id')->on('purchase_order_bills')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedInteger('payment_id');
            $table->double('amount');
            $table->string('reference_number',255)->nullable();
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
        Schema::dropIfExists('purchase_order_payments');
    }
}
