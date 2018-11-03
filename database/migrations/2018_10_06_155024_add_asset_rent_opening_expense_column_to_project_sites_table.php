<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAssetRentOpeningExpenseColumnToProjectSitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_sites', function (Blueprint $table) {
            $table->double('asset_rent_opening_expense')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('project_sites', function (Blueprint $table) {
            $table->dropColumn('asset_rent_opening_expense');
        });
    }
}
