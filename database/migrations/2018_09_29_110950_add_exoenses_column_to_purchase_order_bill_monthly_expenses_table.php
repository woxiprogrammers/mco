<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddExoensesColumnToPurchaseOrderBillMonthlyExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_order_bill_monthly_expenses', function (Blueprint $table) {
            $table->double('purchase_expense')->nullable();
            $table->double('site_transfer_expense')->nullable();
            $table->double('asset_maintenance_expense')->nullable();
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
            $table->dropColumn('purchase_expense');
            $table->dropColumn('site_transfer_expense');
            $table->dropColumn('asset_maintenance_expense');
        });
    }
}
