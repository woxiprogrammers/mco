<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropPurchaseOrderTransactionTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('purchase_order_bill_images');
        Schema::dropIfExists('purchase_order_bill_payments');
        Schema::dropIfExists('purchase_order_bills');
        Schema::dropIfExists('purchase_order_bill_statuses');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('purchase_order_bill_statuses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',255);
            $table->string('slug',255);
            $table->timestamps();
        });
        Schema::create('purchase_order_bills', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('purchase_order_bill_status_id')->nullable();
            $table->foreign('purchase_order_bill_status_id')->references('id')->on('purchase_order_bill_statuses')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedInteger('purchase_order_component_id');
            $table->foreign('purchase_order_component_id')->references('id')->on('purchase_order_components')->onDelete('cascade')->onUpdate('cascade');
            $table->string('bill_number',255);
            $table->string('vehicle_number',255)->nullable();
            $table->string('grn',255);
            $table->timestamp('in_time')->nullable();
            $table->timestamp('out_time')->nullable();
            $table->double('quantity')->nullable();
            $table->timestamps();
        });
        Schema::create('purchase_order_bill_images', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('purchase_order_bill_id');
            $table->string('name',255);
            $table->boolean('is_payment_image')->default(false);
            $table->foreign('purchase_order_bill_id')->references('id')->on('purchase_order_bills')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
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
}
