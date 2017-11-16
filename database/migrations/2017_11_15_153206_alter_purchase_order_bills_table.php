<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPurchaseOrderBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_order_bills', function (Blueprint $table) {
            $table->unsignedInteger('purchase_order_bill_status_id')->nullable();
            $table->foreign('purchase_order_bill_status_id')->references('id')->on('purchase_order_bill_statuses')->onUpdate('cascade')->onDelete('cascade');
            $table->dropColumn('is_paid');
            $table->dropColumn('is_amendment');
            $table->datetime('out_time')->nullable()->change();
            $table->datetime('in_time')->nullable()->change();
            $table->text('vehicle_number')->nullable()->change();
            $table->text('bill_number')->nullable()->change();
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
            $table->dropForeign('purchase_order_bills_purchase_order_bill_status_id_foreign');
            $table->dropColumn('purchase_order_bill_status_id');
            $table->boolean('is_paid')->default(false);
            $table->boolean('is_amendment')->nullable();
            $table->datetime('out_time')->change();
            $table->datetime('in_time')->change();
            $table->text('vehicle_number')->change();
            $table->text('bill_number')->change();
        });
    }
}
