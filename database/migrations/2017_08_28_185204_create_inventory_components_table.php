<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInventoryComponentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory_components', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',255);
            $table->unsignedInteger('project_site_id');
            $table->unsignedInteger('purchase_order_component_id')->nullable();
            $table->foreign('project_site_id')->references('id')->on('project_sites')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('purchase_order_component_id')->references('id')->on('purchase_order_components')->onDelete('cascade')->onUpdate('cascade');
            $table->boolean('is_material');
            $table->unsignedInteger('reference_id')->nullable();
            $table->double('opening_stock');
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
        Schema::dropIfExists('inventory_components');
    }
}
