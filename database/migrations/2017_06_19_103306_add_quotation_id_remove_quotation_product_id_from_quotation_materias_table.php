<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddQuotationIdRemoveQuotationProductIdFromQuotationMateriasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('quotation_materials', function (Blueprint $table) {
            $table->dropColumn('quantity');
            $table->dropForeign('quotation_materials_quotation_product_id_foreign');
            $table->dropColumn('quotation_product_id');
            $table->unsignedInteger('quotation_id')->nullable();
            $table->foreign('quotation_id')->references('id')->on('quotations')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('quotation_materials', function (Blueprint $table) {
            $table->double('quantity')->nullable();
            $table->dropForeign('quotation_materials_quotation_id_foreign');
            $table->dropColumn('quotation_id');
            $table->unsignedInteger('quotation_product_id')->nullable();
            $table->foreign('quotation_product_id')->references('id')->on('quotation_products')->onUpdate('cascade')->onDelete('cascade');
        });
    }
}
