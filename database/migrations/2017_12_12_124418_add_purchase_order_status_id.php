<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPurchaseOrderStatusId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropColumn('is_closed');
            $table->unsignedInteger('purchase_order_status_id')->nullable();
            $table->foreign('purchase_order_status_id')->references('id')->on('purchase_order_statuses')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropForeign('purchase_orders_purchase_order_status_id_foreign');
            $table->dropColumn('purchase_order_status_id');
            $table->boolean('is_closed')->nullable();
        });
    }
}
