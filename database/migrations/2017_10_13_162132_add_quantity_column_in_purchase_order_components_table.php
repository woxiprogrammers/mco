<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddQuantityColumnInPurchaseOrderComponentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_order_components', function (Blueprint $table) {
            $table->double('quantity')->nullable();
            $table->unsignedInteger('unit_id')->nullable();
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade')->onUpdate('cascade');
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
            $table->dropForeign('purchase_order_components_unit_id_foreign');
            $table->dropColumn('quantity');
            $table->dropColumn('unit_id');
        });
    }
}
