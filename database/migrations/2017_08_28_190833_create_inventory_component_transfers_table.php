<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInventoryComponentTransfersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory_component_transfers', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('inventory_component_id');
            $table->foreign('inventory_component_id')->references('id')->on('inventory_components')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedInteger('transfer_type_id');
            $table->foreign('transfer_type_id')->references('id')->on('inventory_transfer_types')->onDelete('cascade')->onUpdate('cascade');
            $table->double('quantity')->nullable();
            $table->unsignedInteger('unit_id')->nullable();
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade')->onUpdate('cascade');
            $table->text('remark')->nullable();
            $table->string('source_name',255)->nullable();
            $table->string('bill_number',255)->nullable();
            $table->double('bill_amount')->nullable();
            $table->string('vehicle_number',255)->nullable();
            $table->timestamp('in_time')->nullable();
            $table->timestamp('out_time')->nullable();
            $table->unsignedInteger('payment_type_id')->nullable();
            $table->foreign('payment_type_id')->references('id')->on('payment_types')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamp('date')->nullable();
            $table->timestamp('next_maintenance_date')->nullable();
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
        Schema::dropIfExists('inventory_component_transfers');
    }
}
