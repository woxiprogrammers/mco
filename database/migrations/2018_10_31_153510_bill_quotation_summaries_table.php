<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class BillQuotationSummariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bill_quotation_summaries', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('bill_id');
            $table->foreign('bill_id')->references('id')->on('bills')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedInteger('quotation_summary_id');
            $table->foreign('quotation_summary_id')->references('id')->on('quotation_summaries')->onUpdate('cascade')->onDelete('cascade');
            $table->double('rate_per_sqft');
            $table->double('built_up_area');
            $table->double('quantity');
            $table->boolean('is_deleted');
            $table->unsignedInteger('product_description_id');
            $table->foreign('product_description_id')->references('id')->on('product_description')->onUpdate('cascade')->onDelete('cascade');
            $table->double('sub_total');
            $table->double('with_tax_amount');
            $table->double('rounded_amount_by');
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
        Schema::dropIfExists('bill_quotation_summaries');
    }
}
