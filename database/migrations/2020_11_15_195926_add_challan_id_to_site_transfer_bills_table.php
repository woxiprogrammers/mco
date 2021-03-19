<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddChallanIdToSiteTransferBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('site_transfer_bills', function (Blueprint $table) {
            $table->unsignedInteger('inventory_component_transfer_id')->nullable()->change();
            $table->unsignedInteger('inventory_transfer_challan_id')->nullable();
            $table->foreign('inventory_transfer_challan_id')->references('id')->on('inventory_transfer_challan')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('site_transfer_bills', function (Blueprint $table) {
            $table->unsignedInteger('inventory_component_transfer_id')->nullable(false)->change();
            $table->dropForeign('site_transfer_bills_inventory_transfer_challan_id_foreign');
            $table->dropColumn('inventory_transfer_challan_id');
        });
    }
}
