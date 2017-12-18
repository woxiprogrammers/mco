<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMaterialRequestComponentVersionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('material_request_component_versions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('material_request_component_id');
            $table->foreign('material_request_component_id')->references('id')->on('material_request_components')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedInteger('component_status_id');
            $table->foreign('component_status_id')->references('id')->on('purchase_request_component_statuses')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->double('quantity');
            $table->unsignedInteger('unit_id');
            $table->foreign('unit_id')->references('id')->on('units')->onUpdate('cascade')->onDelete('cascade');
            $table->text('remark');
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
        Schema::dropIfExists('material_request_component_versions');
    }
}
