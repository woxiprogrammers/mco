<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterProjectSiteChecklistCheckpointsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_site_checklist_checkpoints', function (Blueprint $table) {
            $table->dropForeign('project_site_checklist_checkpoints_checklist_category_id_foreign');
            $table->dropColumn('checklist_category_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('project_site_checklist_checkpoints', function (Blueprint $table) {
            $table->unsignedInteger('checklist_category_id')->nullable();
            $table->foreign('checklist_category_id')->references('id')->on('checklist_categories')->onDelete('cascade')->onUpdate('cascade');
        });
    }
}
