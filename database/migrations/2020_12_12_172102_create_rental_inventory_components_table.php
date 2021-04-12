<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRentalInventoryComponentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rental_inventory_components', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('inventory_component_id')->nullable();
            $table->foreign('inventory_component_id')->references('id')->on('inventory_components')->onDelete('cascade')->onUpdate('cascade');
            $table->string('month');
            $table->string('year');
            $table->unsignedInteger('opening_stock');
            $table->unsignedInteger('closing_stock');
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
        Schema::dropIfExists('rental_inventory_components');
    }
}
