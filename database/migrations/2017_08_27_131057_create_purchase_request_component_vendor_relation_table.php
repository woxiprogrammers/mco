<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchaseRequestComponentVendorRelationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_request_component_vendor_relation', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('purchase_request_component_id');
            $table->unsignedInteger('vendor_id');
            $table->foreign('purchase_request_component_id')->references('id')->on('purchase_request_components')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('cascade')->onUpdate('cascade');
            $table->boolean('is_email_sent')->default(false);
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
        Schema::dropIfExists('purchase_request_component_vendor_relation');
    }
}
