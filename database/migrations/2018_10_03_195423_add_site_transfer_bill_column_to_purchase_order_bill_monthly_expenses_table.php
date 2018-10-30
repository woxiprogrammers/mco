<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSiteTransferBillColumnToPurchaseOrderBillMonthlyExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_order_bill_monthly_expenses', function (Blueprint $table) {
            $table->double('site_transfer_bill_expense')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_order_bill_monthly_expenses', function (Blueprint $table) {
            $table->dropColumn('site_transfer_bill_expense');
        });
    }
}
