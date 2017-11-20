<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableChecklistCheckpointImages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('checklist_checkpoint_images', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('checklist_checkpoint_id');
            $table->foreign('checklist_checkpoint_id')->references('id')->on('checklist_checkpoints')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('checklist_checkpoint_images');
    }
}
