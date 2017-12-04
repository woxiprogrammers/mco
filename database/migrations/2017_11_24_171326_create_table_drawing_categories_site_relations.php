<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableDrawingCategoriesSiteRelations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drawing_category_site_relations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('project_site_id');
            $table->foreign('project_site_id')->references('id')->on('project_sites')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedInteger('drawing_category_id');
            $table->foreign('drawing_category_id')->references('id')->on('drawing_categories')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('drawing_category_site_relations');
    }
}
