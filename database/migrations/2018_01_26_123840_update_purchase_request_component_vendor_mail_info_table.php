<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePurchaseRequestComponentVendorMailInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_request_component_vendor_mail_info', function (Blueprint $table) {
            $table->boolean('is_client')->nullable();
            $table->unsignedInteger('client_id')->nullable();
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade')->onUpdate('cascade');
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
            $table->dropColumn('is_client');
            $table->dropForeign('purchase_request_component_vendor_mail_info_client_id_foreign');
            $table->dropColumn('client_id');
        });
    }
}
