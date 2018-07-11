<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInventoryComponentOpeningStockHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory_component_opening_stock_history', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('inventory_component_id');
            $table->foreign('inventory_component_id')->references('id')->on('inventory_components')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('inventory_component_opening_stock_history');
    }
}
