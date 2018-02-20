<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddReferenceIdColumnInPurchaseRequestComponentVendorMailInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_request_component_vendor_mail_info', function (Blueprint $table) {
            $table->dropForeign('purchase_request_component_vendor_mail_info_purchase_request_component_vendor_relation_id_foreign');
            $table->dropColumn('purchase_request_component_vendor_relation_id');
            $table->unsignedInteger('vendor_id')->nullable();
            $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedInteger('reference_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_request_component_vendor_mail_info', function (Blueprint $table) {
            $table->dropForeign('purchase_request_component_vendor_mail_info_vendor_id_foreign');
            $table->dropColumn('vendor_id');
            $table->dropColumn('reference_id');
            $table->unsignedInteger('purchase_request_component_vendor_relation_id')->nullable();
            $table->foreign('purchase_request_component_vendor_relation_id')->references('id')->on('purchase_request_component_vendor_relation')->onDelete('cascade')->onUpdate('cascade');
        });
    }
}
