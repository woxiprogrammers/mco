<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProjetcSiteIdToSubcontractorAdvancePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subcontractor_advance_payments', function (Blueprint $table) {
            $table->unsignedInteger('project_site_id')->nullable();
            $table->foreign('project_site_id')->references('id')->on('project_sites')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('subcontractor_advance_payments', function (Blueprint $table) {
            $table->dropColumn('project_site_id');
        });
    }
}
