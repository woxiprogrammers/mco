<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVendorMaterialCityRelationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vendor_material_city_relation', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('vendor_city_relation_id');
            $table->unsignedInteger('vendor_material_relation_id');
            $table->foreign('vendor_city_relation_id')->references('id')->on('vendor_city_relation')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('vendor_material_relation_id')->references('id')->on('vendor_material_relation')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('vendor_material_city_relation');
    }
}
