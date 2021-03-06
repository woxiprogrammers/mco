<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeQuotationIdNullableInQuotationProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('quotation_products', function (Blueprint $table) {
            $table->float('quantity','10','3')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('quotation_products', function (Blueprint $table) {
            $table->float('quantity','10','3')->nullable(false)->change();
        });
    }
}
