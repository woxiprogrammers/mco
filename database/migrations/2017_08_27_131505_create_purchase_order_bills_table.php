<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchaseOrderBillsTable extends Migration
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
            $table->unsignedInteger('purchase_order_component_id');
            $table->foreign('purchase_order_component_id')->references('id')->on('purchase_order_components')->onDelete('cascade')->onUpdate('cascade');
            $table->string('bill_number',255);
            $table->string('vehicle_number',255);
            $table->string('grn',255);
            $table->timestamp('in_time');
            $table->timestamp('out_time');
            $table->double('quantity');
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
