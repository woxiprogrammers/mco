<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuotationProfitMarginVersionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quotation_profit_margin_versions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('quotation_product_id');
            $table->foreign('quotation_product_id')->references('id')->on('quotation_products')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedInteger('profit_margin_id');
            $table->foreign('profit_margin_id')->references('id')->on('profit_margins')->onUpdate('cascade')->onDelete('cascade');
            $table->double('percentage');
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
        Schema::dropIfExists('quotation_profit_margin_versions');
    }
}
