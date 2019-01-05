<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToBillQuotationProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bill_quotation_products', function (Blueprint $table) {
            $table->double('rate_per_unit')->nullable();
            $table->boolean('is_deleted')->default('false');
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
            $table->dropColumn('rate_per_unit');
            $table->dropColumn('is_deleted');
        });
    }
}
