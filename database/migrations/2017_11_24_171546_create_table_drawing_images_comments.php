<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableDrawingImagesComments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drawing_image_comments', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('drawing_image_version_id')->nullable();
            $table->foreign('drawing_image_version_id')->references('id')->on('drawing_image_versions')->onUpdate('cascade')->onDelete('cascade');
            $table->text('comment');
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
        Schema::dropIfExists('drawing_image_comments');
    }
}
