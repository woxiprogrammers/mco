<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubcontractorDprCategoryRelationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subcontractor_dpr_category_relations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('subcontractor_id');
            $table->unsignedBigInteger('dpr_main_category_id');
            $table->foreign('subcontractor_id')->references('id')->on('subcontractor')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('dpr_main_category_id')->references('id')->on('dpr_main_categories')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('subcontractor_dpr_category_relations');
    }
}
