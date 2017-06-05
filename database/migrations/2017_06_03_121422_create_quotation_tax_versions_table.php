<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuotationTaxVersionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('tax_versions');
        Schema::create('quotation_tax_versions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('quotation_id');
            $table->foreign('quotation_id')->references('id')->on('quotations')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedInteger('tax_id');
            $table->foreign('tax_id')->references('id')->on('taxes')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('quotation_tax_versions');
        Schema::create('tax_versions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('tax_id');
            $table->unsignedInteger('quotation_version_id');
            $table->float('percentage');
            $table->foreign('tax_id')->references('id')->on('taxes')->onUpdate('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }
}
