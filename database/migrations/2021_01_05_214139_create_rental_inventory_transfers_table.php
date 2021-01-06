<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRentalInventoryTransfersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rental_inventory_transfers', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('inventory_component_transfer_id')->nullable();
            $table->foreign('inventory_component_transfer_id')->references('id')->on('inventory_component_transfers')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedInteger('quantity');
            $table->double('rent_per_day');
            $table->timestamp('rent_start_date');
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
        Schema::dropIfExists('rental_inventory_transfers');
    }
}
