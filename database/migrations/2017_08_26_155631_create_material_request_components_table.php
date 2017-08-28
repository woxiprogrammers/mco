<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMaterialRequestComponentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('material_request_components', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('material_request_id');
            $table->string('name');
            $table->double('quantity');
            $table->unsignedInteger('unit_id');
            $table->unsignedInteger('component_type_id');
            $table->unsignedInteger('component_status_id');
            $table->foreign('material_request_id')->references('id')->on('material_requests')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('component_type_id')->references('id')->on('material_request_component_types')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('component_status_id')->references('id')->on('purchase_request_component_statuses')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('material_request_components');
    }
}
