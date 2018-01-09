<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInventoryComponentTransferStatusIdToInventoryComponentTransfersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inventory_component_transfers', function (Blueprint $table) {
            $table->unsignedInteger('inventory_component_transfer_status_id')->nullable();
            $table->foreign('inventory_component_transfer_status_id')->references('id')->on('inventory_component_transfer_statuses')->onUpdate('cascade')->onDelete('cascade');
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
            $table->dropForeign('inventory_component_transfers_inventory_component_transfer_status_id_foreign');
            $table->dropColumn('inventory_component_transfer_status_id');
        });
    }
}
