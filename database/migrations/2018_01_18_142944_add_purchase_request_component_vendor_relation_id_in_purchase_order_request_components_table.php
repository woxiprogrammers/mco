<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPurchaseRequestComponentVendorRelationIdInPurchaseOrderRequestComponentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_order_request_components', function (Blueprint $table) {
            $table->dropForeign('purchase_order_request_components_purchase_request_component_id_foreign');
            $table->dropColumn('purchase_request_component_id');
            $table->unsignedBigInteger('purchase_request_component_vendor_relation_id')->nullable();
            $table->foreign('purchase_request_component_vendor_relation_id')->references('id')->on('purchase_request_component_vendor_relation')->onDelete('cascade')->onUpdate('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_order_request_components', function (Blueprint $table) {
            $table->dropForeign('purchase_order_request_components_purchase_request_component_vendor_relation_id_foreign');
            $table->dropColumn('purchase_request_component_vendor_relation_id');
            $table->unsignedBigInteger('purchase_request_component_id')->nullable();
            $table->foreign('purchase_request_component_id')->references('id')->on('purchase_request_components')->onDelete('cascade')->onUpdate('cascade');
        });
    }
}
