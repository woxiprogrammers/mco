<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectSiteUserCheckpointTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_site_user_checkpoints', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('project_site_checklist_checkpoint_id');
            $table->foreign('project_site_checklist_checkpoint_id')->references('id')->on('project_site_checklist_checkpoints')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedInteger('project_site_user_checkpoint_id')->nullable();
            $table->unsignedInteger('project_site_user_checklist_assignment_id');
            $table->foreign('project_site_user_checklist_assignment_id')->references('id')->on('project_site_user_checklist_assignments')->onUpdate('cascade')->onDelete('cascade');
            $table->text('remark')->nullable();
            $table->boolean('is_ok')->nullable();
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
        Schema::dropIfExists('project_site_user_checkpoints');
    }
}
