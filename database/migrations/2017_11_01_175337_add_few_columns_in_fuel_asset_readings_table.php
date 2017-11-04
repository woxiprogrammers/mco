<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFewColumnsInFuelAssetReadingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fuel_asset_readings', function (Blueprint $table) {
            $table->string('electricity_per_unit',255)->nullable();
            $table->string('fuel_per_unit',255)->nullable();
            $table->string('top_up',255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fuel_asset_readings', function (Blueprint $table) {
            $table->dropColumn('electricity_per_unit');
            $table->dropColumn('fuel_per_unit');
            $table->dropColumn('top_up');
        });
    }
}
