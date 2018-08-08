<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableMaterialRequestComponentVersions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('material_request_component_versions', function (Blueprint $table) {
            $table->unsignedInteger('purchase_order_transaction_status_id')->nullable();
            $table->foreign('purchase_order_transaction_status_id')->references('id')->on('purchase_order_transaction_statuses')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedInteger('component_status_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('material_request_component_versions', function (Blueprint $table) {
            $table->dropForeign('material_request_component_versions_purchase_order_transaction_status_id_foreign');
            $table->dropColumn('purchase_order_transaction_status_id');
        });
    }
}
