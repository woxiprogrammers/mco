<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPurchaseOrderRequestComponentIdInPurchaseOrderComponentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_order_components', function (Blueprint $table) {
            $table->unsignedBigInteger('purchase_order_request_component_id')->nullable();
            $table->foreign('purchase_order_request_component_id')->references('id')->on('purchase_order_request_components')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_order_components', function (Blueprint $table) {
            $table->dropForeign('purchase_order_components_purchase_order_request_component_id_foreign');
            $table->dropColumn('purchase_order_request_component_id');
        });
    }
}
