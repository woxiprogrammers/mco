<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDprDetailImageRelations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dpr_detail_image_relations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('dpr_detail_id');
            $table->unsignedInteger('dpr_image_id');
            $table->foreign('dpr_detail_id')->references('id')->on('dpr_details')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('dpr_image_id')->references('id')->on('dpr_images')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('dpr_detail_image_relations');
    }
}
