<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPaidFromSlugColumnToAssetMaintenanceBillPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('asset_maintenance_bill_payments', function (Blueprint $table) {
            $table->string('paid_from_slug',255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('asset_maintenance_bill_payments', function (Blueprint $table) {
            $table->dropColumn('paid_from_slug');
        });
    }
}
