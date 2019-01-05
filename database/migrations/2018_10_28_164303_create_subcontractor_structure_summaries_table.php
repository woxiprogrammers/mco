<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubcontractorStructureSummariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subcontractor_structure_summaries', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('subcontractor_structure_id');
            $table->foreign('subcontractor_structure_id')->references('id')->on('subcontractor_structure')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedInteger('summary_id');
            $table->foreign('summary_id')->references('id')->on('summaries')->onDelete('cascade')->onUpdate('cascade');
            $table->double('rate')->default(0);
            $table->double('total_work_area')->default(0);
            $table->text('description')->nullable();
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
        Schema::dropIfExists('subcontractor_structure_summaries');
    }
}
