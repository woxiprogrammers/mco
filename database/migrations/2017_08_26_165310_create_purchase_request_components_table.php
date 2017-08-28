<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchaseRequestComponentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_request_components', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('purchase_request_id');
            $table->unsignedInteger('material_request_component_id');
            $table->foreign('purchase_request_id')->references('id')->on('purchase_requests')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('material_request_component_id')->references('id')->on('material_request_components')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('purchase_request_components');
    }
}
