<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAdvancedAmountAndAdvancedBalanceColumnsToProjectSitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_sites', function (Blueprint $table) {
            $table->double('advanced_amount')->nullable();
            $table->double('advanced_balance')->nullable();
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
            $table->dropColumn('advanced_amount');
            $table->dropColumn('advanced_balance');
        });
    }
}
