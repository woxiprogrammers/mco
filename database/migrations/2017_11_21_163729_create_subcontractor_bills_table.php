<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubcontractorBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subcontractor_bills', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('sc_structure_id');
            $table->foreign('sc_structure_id')->references('id')->on('subcontractor_structure')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedInteger('subcontractor_bill_status_id');
            $table->foreign('subcontractor_bill_status_id')->references('id')->on('subcontractor_bill_status')->onDelete('cascade')->onUpdate('cascade');
            $table->double('qty')->default(0);
            $table->text('description')->nullable();
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
        Schema::dropIfExists('subcontractor_bills');
    }
}
