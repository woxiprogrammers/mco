<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsPaidColumnInPurchaseOrderBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_order_bills', function (Blueprint $table) {
            $table->boolean('is_paid')->default(false);
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
            $table->dropColumn('is_paid');
        });
    }
}
