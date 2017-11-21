<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubcontractorBillTransactionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subcontractor_bill_transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('subcontractor_bills_id');
            $table->foreign('subcontractor_bills_id')->references('id')->on('subcontractor_bills')->onUdate('cascade')->onDelete('cascade');
            $table->double('subtotal');
            $table->double('total');
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
        Schema::dropIfExists('subcontractor_bill_transactions');
    }
}
