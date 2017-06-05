<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuotationMaterialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quotation_materials', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('quotation_product_id');
            $table->foreign('quotation_product_id')->references('id')->on('quotation_products')->onUdate('cascade')->onDelete('cascade');
            $table->unsignedInteger('material_id');
            $table->foreign('material_id')->references('id')->on('materials')->onUpdate('cascade')->onDelete('cascade');
            $table->double('quantity');
            $table->double('rate_per_unit');
            $table->double('unit_id');
            $table->boolean('is_client_supplied');
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
        Schema::dropIfExists('quotation_materials');
    }
}
