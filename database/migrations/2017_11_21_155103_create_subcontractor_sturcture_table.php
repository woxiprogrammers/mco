<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubcontractorSturctureTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subcontractor_structure', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('project_site_id');
            $table->foreign('project_site_id')->references('id')->on('project_sites')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedInteger('subcontractor_id');
            $table->foreign('subcontractor_id')->references('id')->on('subcontractor')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedInteger('summary_id');
            $table->foreign('summary_id')->references('id')->on('summaries')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedInteger('sc_structure_type_id');
            $table->foreign('sc_structure_type_id')->references('id')->on('subcontractor_structure_types')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('subcontractor_structure');
    }
}
