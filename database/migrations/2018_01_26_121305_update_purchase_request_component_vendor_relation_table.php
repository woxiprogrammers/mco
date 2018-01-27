<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePurchaseRequestComponentVendorRelationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_request_component_vendor_relation', function (Blueprint $table) {
            $table->unsignedInteger('vendor_id')->nullable()->change();
            $table->unsignedInteger('client_id')->nullable();
            $table->foreign('client_id')->references('id')->on('clients')->onUpdate('cascade')->onDelete('cascade');
            $table->boolean('is_client')->default(false)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_request_component_vendor_relation', function (Blueprint $table) {
            $table->dropForeign('purchase_request_component_vendor_relation_client_id_foreign');
            $table->dropColumn('client_id');
            $table->dropColumn('is_client');
        });
    }
}
