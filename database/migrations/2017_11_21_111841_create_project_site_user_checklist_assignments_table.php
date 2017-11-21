<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectSiteUserChecklistAssignmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_site_user_checklist_assignments', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('project_site_checklist_id');
            $table->foreign('project_site_checklist_id')->references('id')->on('project_site_checklists')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedInteger('checklist_status_id');
            $table->foreign('checklist_status_id')->references('id')->on('checklist_statuses')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedInteger('assigned_to');
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedInteger('assigned_by');
            $table->foreign('assigned_by')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedInteger('reviewed_by')->nullable();
            $table->foreign('reviewed_by')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedInteger('project_site_user_checklist_assignment_id')->nullable();
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
        Schema::dropIfExists('project_site_user_checklist_assignments');
    }
}
