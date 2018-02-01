<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubcontractorBillReconcilesTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subcontractor_bill_reconcile_transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('subcontractor_bill_id');
            $table->foreign('subcontractor_bill_id')->references('id')->on('subcontractor_bills')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedInteger('payment_type_id');
            $table->foreign('payment_type_id')->references('id')->on('payment_types')->onDelete('cascade')->onUpdate('cascade');
            $table->double('amount');
            $table->string('transaction_slug');
            $table->string('reference_number')->nullable();
            $table->text('remark')->nullable();
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
        Schema::dropIfExists('subcontractor_bill_reconcile_transactions');
    }
}
