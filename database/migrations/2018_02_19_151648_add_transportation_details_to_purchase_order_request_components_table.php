<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTransportationDetailsToPurchaseOrderRequestComponentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_order_request_components', function (Blueprint $table) {
            $table->double('transportation_amount')->nullable();
            $table->double('transportation_cgst_percentage')->nullable();
            $table->double('transportation_sgst_percentage')->nullable();
            $table->double('transportation_igst_percentage')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_order_request_components', function (Blueprint $table) {
            $table->dropColumn('transportation_amount');
            $table->dropColumn('transportation_cgst_percentage');
            $table->dropColumn('transportation_sgst_percentage');
            $table->dropColumn('transportation_igst_percentage');
        });
    }
}
