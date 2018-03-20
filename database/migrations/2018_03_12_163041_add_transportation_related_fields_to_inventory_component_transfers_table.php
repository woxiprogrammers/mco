<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTransportationRelatedFieldsToInventoryComponentTransfersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inventory_component_transfers', function (Blueprint $table) {
            $table->unsignedInteger('vendor_id')->nullable();
            $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('cascade')->onUpdate('cascade');
            $table->double('transportation_amount')->nullable();
            $table->double('transportation_cgst_percent')->nullable();
            $table->double('transportation_sgst_percent')->nullable();
            $table->double('transportation_igst_percent')->nullable();
            $table->string('driver_name',255)->nullable();
            $table->string('mobile',15)->nullable();
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
            $table->dropColumn('vendor_id');
            $table->dropColumn('transportation_amount');
            $table->dropColumn('transportation_cgst_percent');
            $table->dropColumn('transportation_sgst_percent');
            $table->dropColumn('transportation_igst_percent');
            $table->dropColumn('driver_name');
            $table->dropColumn('mobile');
        });
    }
}
