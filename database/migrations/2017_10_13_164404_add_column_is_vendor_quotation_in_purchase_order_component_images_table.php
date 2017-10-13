<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnIsVendorQuotationInPurchaseOrderComponentImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_order_component_images', function (Blueprint $table) {
            $table->boolean('is_vendor_approval')->default(true)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_order_component_images', function (Blueprint $table) {
            $table->dropColumn('is_vendor_approval');
        });
    }
}
