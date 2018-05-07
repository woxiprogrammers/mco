<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBankIdPaymentTypeIdToBillTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bill_transactions', function (Blueprint $table) {
            $table->unsignedInteger('bank_id')->nullable();
            $table->foreign('bank_id')->references('id')->on('bank_info')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedInteger('payment_type_id')->nullable();
            $table->foreign('payment_type_id')->references('id')->on('payment_types')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bill_transactions', function (Blueprint $table) {
            $table->dropColumn('bank_id');
            $table->dropColumn('payment_type_id');
        });
    }
}
