<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSiteTransferBillPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('site_transfer_bill_payments', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('site_transfer_bill_id');
            $table->foreign('site_transfer_bill_id')->references('id')->on('site_transfer_bills')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedInteger('payment_type_id');
            $table->foreign('payment_type_id')->references('id')->on('payment_types')->onDelete('cascade')->onUpdate('cascade');
            $table->double('amount');
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
        Schema::dropIfExists('site_transfer_bill_payments');
    }
}
