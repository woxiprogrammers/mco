<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubcontractorStructureExtraItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subcontractor_structure_extra_items', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('subcontractor_structure_id');
            $table->foreign('subcontractor_structure_id')->references('id')->on('subcontractor_structure')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedInteger('extra_item_id');
            $table->foreign('extra_item_id')->references('id')->on('extra_items')->onUpdate('cascade')->onDelete('cascade');
            $table->double('rate');
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
        Schema::dropIfExists('subcontractor_structure_extra_items');
    }
}
