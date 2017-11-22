<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectSiteChecklistCheckpointImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_site_checklist_checkpoint_images', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('project_site_checklist_checkpoint_id');
            $table->foreign('project_site_checklist_checkpoint_id')->references('id')->on('project_site_checklist_checkpoints')->onDelete('cascade')->onUpdate('cascade');
            $table->string('caption',255);
            $table->boolean('is_required');
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
        Schema::dropIfExists('project_site_checklist_checkpoint_images');
    }
}
