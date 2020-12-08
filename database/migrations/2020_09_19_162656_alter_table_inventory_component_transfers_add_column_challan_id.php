<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableInventoryComponentTransfersAddColumnChallanId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inventory_component_transfers', function (Blueprint $table) {
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
        Schema::table('inventory_component_transfers', function (Blueprint $table) {
            $table->dropForeign('inventory_component_transfers_inventory_transfer_challan_id_foreign');
            $table->dropColumn('inventory_transfer_challan_id');
        });
    }
}
