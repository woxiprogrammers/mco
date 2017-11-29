<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableDrawingImages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drawing_images', function (Blueprint $table) {
            $table->increments('id');
            $table->text('random_string');
            $table->unsignedInteger('drawing_category_site_relation_id');
            $table->foreign('drawing_category_site_relation_id')->references('id')->on('drawing_category_site_relations')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('drawing_images');
    }
}
