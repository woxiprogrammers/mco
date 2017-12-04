<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableDrawingImagesVersions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drawing_image_versions', function (Blueprint $table) {
            $table->increments('id');
            $table->text('title');
            $table->text('name');
            $table->unsignedInteger('drawing_image_id');
            $table->foreign('drawing_image_id')->references('id')->on('drawing_images')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('drawing_image_versions');
    }
}
