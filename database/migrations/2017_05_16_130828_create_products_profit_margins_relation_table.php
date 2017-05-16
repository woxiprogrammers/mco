<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsProfitMarginsRelationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products_profit_margins_relation', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('product_version_id');
            $table->unsignedInteger('profit_margin_version_id');
            $table->foreign('product_version_id')->references('id')->on('product_versions')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('profit_margin_version_id')->references('id')->on('profit_margin_versions')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('products_profit_margins_relation');
    }
}
