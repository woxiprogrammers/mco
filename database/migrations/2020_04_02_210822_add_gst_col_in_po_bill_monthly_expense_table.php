<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGstColInPoBillMonthlyExpenseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_order_bill_monthly_expenses', function (Blueprint $table) {
            $table->double('purchase_gst')->nullable();
            $table->double('site_transfer_gst')->nullable();
            $table->double('asset_maintenance_gst')->nullable();
            $table->double('site_transfer_bill_gst')->nullable();
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
            $table->dropColumn('purchase_gst');
            $table->dropColumn('site_transfer_gst');
            $table->dropColumn('asset_maintenance_gst');
            $table->dropColumn('site_transfer_bill_gst');
        });
    }
}
