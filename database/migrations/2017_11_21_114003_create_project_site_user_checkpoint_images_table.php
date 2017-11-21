<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectSiteUserCheckpointImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_site_user_checkpoint_images', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('project_site_user_checkpoint_id');
            $table->foreign('project_site_user_checkpoint_id')->references('id')->on('project_site_user_checkpoints')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedInteger('project_site_checklist_checkpoint_image_id');
            $table->foreign('project_site_checklist_checkpoint_image_id')->references('id')->on('project_site_checklist_checkpoint_images')->onUpdate('cascade')->onDelete('cascade');
            $table->string('image',255);
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
        Schema::dropIfExists('project_site_user_checkpoint_images');
    }
}
