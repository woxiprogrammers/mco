<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyPurchaseOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->unsignedInteger('client_id')->nullable();
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade')->onUpdate('cascade');
            $table->boolean('is_client_order')->nullable();
            $table->unsignedInteger('purchase_order_request_id')->nullable();
            $table->foreign('purchase_order_request_id')->references('id')->on('purchase_order_requests')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedInteger('vendor_id')->nullable()->change();
            $table->double('total_advance_amount')->nullable();
            $table->double('balance_advance_amount')->nullable();
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
            $table->unsignedInteger('vendor_id')->change();
            $table->dropForeign('purchase_orders_client_id_foreign');
            $table->dropForeign('purchase_orders_purchase_order_request_id_foreign');
            $table->dropColumn('client_id');
            $table->dropColumn('purchase_order_request_id');
            $table->dropColumn('is_client_order');
            $table->dropColumn('total_advance_amount');
            $table->dropColumn('balance_advance_amount');
        });
    }
}
