<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsAdvanceColumnInPurchaseOrderPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_order_payments', function (Blueprint $table) {
            $table->boolean('is_advance')->nullable();
            $table->unsignedInteger('payment_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_order_payments', function (Blueprint $table) {
            $table->dropColumn('is_advance');
            $table->unsignedInteger('payment_id')->change();
        });
    }
}
