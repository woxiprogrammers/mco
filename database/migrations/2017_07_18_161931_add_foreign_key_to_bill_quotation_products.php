<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeyToBillQuotationProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bill_quotation_products', function (Blueprint $table) {
            $table->dropColumn('description');
            $table->unsignedInteger('product_description_id')->nullable();
            $table->foreign('product_description_id')->references('id')->on('product_description')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bill_quotation_products', function (Blueprint $table) {
            $table->text('description')->nullable();
            $table->dropForeign('bill_quotation_products_product_description_id_foreign');
            $table->dropColumn('product_description_id');

        });
    }
}
