<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InventoryCart extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory_cart', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('project_site_id');
            $table->foreign('project_site_id')
                ->references('id')
                ->on('project_sites')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->unsignedBigInteger('inventory_component_id');
            $table->foreign('inventory_component_id')
                ->references('id')
                ->on('inventory_components')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->unsignedInteger('unit_id')->nullable();
            $table->foreign('unit_id')
                ->references('id')
                ->on('units')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->unsignedInteger('quantity')->nullable();
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
        Schema::dropIfExists('inventory_cart');
    }
}
