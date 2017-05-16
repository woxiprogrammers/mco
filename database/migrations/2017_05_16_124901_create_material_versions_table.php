<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMaterialVersionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('material_versions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('material_id');
            $table->foreign('material_id')->references('id')->on('materials')->onDelete('cascade')->onUpdate('cascade');
            $table->double('rate_per_unit');
            $table->unsignedInteger('unit_id');
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('material_versions');
    }
}
