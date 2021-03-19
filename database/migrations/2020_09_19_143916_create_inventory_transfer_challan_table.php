<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInventoryTransferChallanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory_transfer_challan', function (Blueprint $table) {
            $table->increments('id');
            $table->string('challan_number');
            $table->unsignedInteger('project_site_out_id');
            $table->foreign('project_site_out_id')->references('id')->on('project_sites')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedInteger('project_site_in_id')->nullable();
            $table->foreign('project_site_in_id')->references('id')->on('project_sites')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamp('project_site_out_date')->nullable();
            $table->timestamp('project_site_in_date')->nullable();
            $table->unsignedInteger('inventory_component_transfer_status_id');
            $table->foreign('inventory_component_transfer_status_id')->references('id')->on('inventory_component_transfer_statuses')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('inventory_transfer_challan');
    }
}
