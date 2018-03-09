<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBillNumberAndDateInPurchaseOrderBillTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_order_bills', function (Blueprint $table) {
            $table->timestamp('bill_date')->nullable();
            $table->string('vendor_bill_number')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_order_bills', function (Blueprint $table) {
            $table->dropColumn('vendor_bill_number');
            $table->dropColumn('bill_date');
        });
    }
}
