<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyTaxColumnsInPurchaseOrderBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_order_bills', function (Blueprint $table) {
            $table->dropColumn('cgst_percentage');
            $table->dropColumn('cgst_amount');
            $table->dropColumn('sgst_percentage');
            $table->dropColumn('sgst_amount');
            $table->dropColumn('igst_percentage');
            $table->dropColumn('igst_amount');
            $table->double('tax_amount')->nullable();
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
            $table->double('cgst_percentage')->nullable();
            $table->double('cgst_amount')->nullable();
            $table->double('sgst_percentage')->nullable();
            $table->double('sgst_amount')->nullable();
            $table->double('igst_percentage')->nullable();
            $table->double('igst_amount')->nullable();
            $table->dropColumn('tax_amount');
        });
    }
}
