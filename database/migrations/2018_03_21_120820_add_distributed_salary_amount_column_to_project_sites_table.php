<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDistributedSalaryAmountColumnToProjectSitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_sites', function (Blueprint $table) {
            $table->double('distributed_salary_amount')->nullable();
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
            $table->dropColumn('distributed_salary_amount');
        });
    }
}
