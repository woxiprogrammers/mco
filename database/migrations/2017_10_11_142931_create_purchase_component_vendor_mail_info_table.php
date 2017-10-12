<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchaseComponentVendorMailInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_request_component_vendor_mail_info', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('purchase_request_component_vendor_relation_id');
            $table->foreign('purchase_request_component_vendor_relation_id')->references('id')->on('purchase_request_component_vendor_relation')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('purchase_request_component_vendor_mail_info');
    }
}
