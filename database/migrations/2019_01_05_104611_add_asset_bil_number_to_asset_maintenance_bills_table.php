<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAssetBilNumberToAssetMaintenanceBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('asset_maintenance_bills', function (Blueprint $table) {
            $table->string('assets_bill_number')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('asset_maintenance_bills', function (Blueprint $table) {
            $table->dropColumn('assets_bill_number');
        });
    }
}
