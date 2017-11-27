<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchaseOrderTransactionComponentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_order_transaction_components', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('purchase_order_component_id');
            $table->foreign('purchase_order_component_id')->references('id')->on('purchase_order_components')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedBigInteger('purchase_order_transaction_id');
            $table->foreign('purchase_order_transaction_id')->references('id')->on('purchase_order_transactions')->onDelete('cascade')->onUpdate('cascade');
            $table->double('quantity');
            $table->unsignedBigInteger('unit_id');
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('purchase_order_transaction_components');
    }
}
