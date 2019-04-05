<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMaterialImagesTable extends Migration
{
    public function up()
    {
        Schema::create('material_images', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',255);
            $table->unsignedInteger('material_id');
            $table->foreign('material_id')->references('id')->on('materials')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('material_images');
    }
}
