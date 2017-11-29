<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewPurchaseOrderBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_order_bills', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('purchase_order_id');
            $table->foreign('purchase_order_id')->references('id')->on('purchase_orders')->onDelete('cascade')->onUpdate('cascade');
            $table->double('amount')->nullable();
            $table->float('cgst_percentage')->nullable();
            $table->double('cgst_amount')->nullable();
            $table->float('sgst_percentage')->nullable();
            $table->double('sgst_amount')->nullable();
            $table->float('igst_percentage')->nullable();
            $table->double('igst_amount')->nullable();
            $table->double('extra_amount')->nullable();
            $table->string('bill_number')->nullable();
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
        Schema::dropIfExists('purchase_order_bills');
    }
}
