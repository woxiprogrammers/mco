<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddExtraTaxAmountToPurchaseOrderBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_order_bills', function (Blueprint $table) {
            $table->double('extra_tax_amount')->nullable();
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
            $table->dropColumn('extra_tax_amount');
        });
    }
}
