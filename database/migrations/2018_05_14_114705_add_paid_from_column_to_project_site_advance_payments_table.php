<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPaidFromColumnToProjectSiteAdvancePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_site_advance_payments', function (Blueprint $table) {
            $table->string('paid_from_slug',255)->nullable();
            $table->unsignedInteger('payment_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('project_site_advance_payments', function (Blueprint $table) {
            $table->dropColumn('paid_from_slug');
        });
    }
}
