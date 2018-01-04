<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRateTaxColumnsToInventoryComponentTransfersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inventory_component_transfers', function (Blueprint $table) {
            $table->double('rate_per_unit')->nullable();
            $table->double('cgst_percentage')->nullable();
            $table->double('sgst_percentage')->nullable();
            $table->double('igst_percentage')->nullable();
            $table->double('cgst_amount')->nullable();
            $table->double('sgst_amount')->nullable();
            $table->double('igst_amount')->nullable();
            $table->double('total')->nullable();
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
            $table->dropColumn('rate_per_unit');
            $table->dropColumn('cgst_percentage');
            $table->dropColumn('sgst_percentage');
            $table->dropColumn('igst_percentage');
            $table->dropColumn('cgst_amount');
            $table->dropColumn('sgst_amount');
            $table->dropColumn('igst_amount');
            $table->dropColumn('total');
        });
    }
}
