<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubcontractorBillTaxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subcontractor_bill_taxes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('subcontractor_bills_id');
            $table->foreign('subcontractor_bills_id')->references('id')->on('subcontractor_bills')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedInteger('tax_id');
            $table->foreign('tax_id')->references('id')->on('taxes')->onUpdate('cascade')->onDelete('cascade');
            $table->double('percentage');
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
        Schema::dropIfExists('subcontractor_bill_taxes');
    }
}
