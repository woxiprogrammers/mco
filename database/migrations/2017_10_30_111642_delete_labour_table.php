<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeleteLabourTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('labours', function (Blueprint $table) {
            Schema::dropIfExists('labours');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('labours', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',255);
            $table->string('mobile')->nullable();
            $table->float('per_day_wages');
            $table->unsignedInteger('project_site_id')->nullable();
            $table->foreign('project_site_id')->references('id')->on('project_sites')->onDelete('cascade')->onUpdate('cascade');
            $table->string('labour_id');
            $table->boolean('is_active');
            $table->timestamps();
        });
    }
}
