<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTransactionStatusPaymentModeColumnsIntoSubcontractorBillTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subcontractor_bill_transactions', function (Blueprint $table) {
            $table->unsignedInteger('transaction_status_id')->nullable();
            $table->foreign('transaction_status_id')->references('id')->on('transaction_statuses')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('subcontractor_bill_transactions', function (Blueprint $table) {
            $table->dropForeign('subcontractor_bill_transactions_transaction_status_id_foreign');
            $table->dropColumn('transaction_status_id');
        });
    }
}
