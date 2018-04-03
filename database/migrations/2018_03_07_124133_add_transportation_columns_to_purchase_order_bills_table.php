<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTransportationColumnsToPurchaseOrderBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_order_bills', function (Blueprint $table) {
            $table->double('transportation_tax_amount')->nullable();
            $table->double('transportation_total_amount')->nullable();
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
            $table->dropColumn('transportation_tax_amount');
            $table->dropColumn('transportation_total_amount');
        });
    }
}
