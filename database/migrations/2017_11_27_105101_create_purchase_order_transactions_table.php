<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchaseOrderTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_order_transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('purchase_order_id');
            $table->foreign('purchase_order_id')->references('id')->on('purchase_orders')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('purchase_order_transaction_status_id');
            $table->foreign('purchase_order_transaction_status_id')->references('id')->on('purchase_order_transaction_statuses')->onUpdate('cascade')->onDelete('cascade');
            $table->string('bill_number',255)->nullable();
            $table->string('vehicle_number',255)->nullable();
            $table->string('grn',255);
            $table->timestamp('in_time')->nullable();
            $table->timestamp('out_time')->nullable();
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
        Schema::dropIfExists('purchase_order_transactions');
    }
}
