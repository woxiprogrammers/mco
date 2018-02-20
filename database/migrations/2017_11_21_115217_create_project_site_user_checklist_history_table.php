<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectSiteUserChecklistHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_site_user_checklist_history_table', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('checklist_status_id');
            $table->foreign('checklist_status_id')->references('id')->on('checklist_statuses')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedInteger('project_site_user_checklist_assignment_id');
            $table->foreign('project_site_user_checklist_assignment_id')->references('id')->on('project_site_user_checklist_assignments')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('project_site_user_checklist_history_table');
    }
}
