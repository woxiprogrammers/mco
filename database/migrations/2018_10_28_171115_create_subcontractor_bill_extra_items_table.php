<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubcontractorBillExtraItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subcontractor_bill_extra_items', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('subcontractor_bill_id');
            $table->foreign('subcontractor_bill_id')->references('id')->on('subcontractor_bills')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedInteger('subcontractor_structure_extra_item_id');
            $table->foreign('subcontractor_structure_extra_item_id')->references('id')->on('subcontractor_structure_extra_items')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('subcontractor_bill_extra_items');
    }
}
