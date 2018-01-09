<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchaseOrderRequestComponentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_order_request_components', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('purchase_order_request_id');
            $table->foreign('purchase_order_request_id')->references('id')->on('purchase_order_requests')->onDelete('cascade')->onUpdate('cascade');
            $table->boolean('is_approved')->nullable();
            $table->unsignedInteger('purchase_request_component_id');
            $table->foreign('purchase_request_component_id')->references('id')->on('purchase_request_components')->onDelete('cascade')->onUpdate('cascade');
            $table->double('rate_per_unit')->nullable();
            $table->double('quantity')->nullable();
            $table->unsignedInteger('unit_id')->nullable();
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade')->onUpdate('cascade');
            $table->float('gst')->nullable();
            $table->string('hsn_code')->nullable();
            $table->timestamp('expected_delivery_date')->nullable();
            $table->text('remark')->nullable();
            $table->float('credited_days')->nullable();
            $table->double('cgst_percentage')->nullable();
            $table->double('sgst_percentage')->nullable();
            $table->double('igst_percentage')->nullable();
            $table->double('cgst_amount')->nullable();
            $table->double('sgst_amount')->nullable();
            $table->double('igst_amount')->nullable();
            $table->double('total')->nullable();
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
        Schema::dropIfExists('purchase_order_request_components');
    }
}
