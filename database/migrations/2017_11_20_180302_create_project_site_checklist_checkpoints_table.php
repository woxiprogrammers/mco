<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectSiteChecklistCheckpointsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_site_checklist_checkpoints', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('project_site_checklist_id');
            $table->foreign('project_site_checklist_id')->references('id')->on('project_site_checklists')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedInteger('checklist_category_id');
            $table->foreign('checklist_category_id')->references('id')->on('checklist_categories')->onDelete('cascade')->onUpdate('cascade');
            $table->text('description');
            $table->boolean('is_remark_required');
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
        Schema::dropIfExists('project_site_checklist_checkpoints');
    }
}
