<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableDprDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dpr_details', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('project_site_id');
            $table->foreign('project_site_id')->references('id')->on('project_sites')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedInteger('subcontractor_id');
            $table->foreign('subcontractor_id')->references('id')->on('subcontractor')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedInteger('dpr_main_category_id');
            $table->foreign('dpr_main_category_id')->references('id')->on('dpr_main_categories')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedInteger('number_of_users');
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
        Schema::dropIfExists('dpr_details');
    }
}
