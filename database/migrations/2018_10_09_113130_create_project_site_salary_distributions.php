<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectSiteSalaryDistributions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_site_salary_distributions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('project_site_id');
            $table->foreign('project_site_id')->references('id')->on('project_sites')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedInteger('month_id');
            $table->foreign('month_id')->references('id')->on('months')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedInteger('year_id');
            $table->foreign('year_id')->references('id')->on('years')->onUpdate('cascade')->onDelete('cascade');
            $table->double('distributed_amount');
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
        Schema::dropIfExists('project_site_salary_distributions');
    }
}
