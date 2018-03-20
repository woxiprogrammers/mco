<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubcontractorAdvancePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subcontractor_advance_payments', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('subcontractor_id');
            $table->foreign('subcontractor_id')->references('id')->on('subcontractor')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedInteger('payment_id');
            $table->double('amount');
            $table->string('reference_number',255)->nullable();
            $table->foreign('payment_id')->references('id')->on('payment_types')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('subcontractor_advance_payments');
    }
}
