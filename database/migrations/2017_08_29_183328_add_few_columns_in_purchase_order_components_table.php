<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFewColumnsInPurchaseOrderComponentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_order_components', function (Blueprint $table) {
            $table->float('gst')->nullable();
            $table->string('hsn_code')->nullable();
            $table->timestamp('expected_delivery_date')->nullable();
            $table->timestamp('credited_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_order_components', function (Blueprint $table) {
            $table->dropColumn('gst');
            $table->dropColumn('hsn_code');
            $table->dropColumn('expected_delivery_date');
            $table->dropColumn('credited_date');
        });
    }
}
