<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductMaterialRelationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_material_relation', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('product_version_id');
            $table->foreign('product_version_id')->references('id')->on('product_versions')->onUpdate('cascade')->onUpdate('cascade');
            $table->unsignedInteger('material_version_id');
            $table->foreign('material_version_id')->references('id')->on('material_versions')->onUpdate('cascade')->onUpdate('cascade');
            $table->double('material_quantity');
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
        Schema::dropIfExists('product_material_relation');
    }
}
