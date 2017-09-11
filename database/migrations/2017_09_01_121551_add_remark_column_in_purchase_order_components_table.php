<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRemarkColumnInPurchaseOrderComponentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_order_components', function (Blueprint $table) {
            $table->text('remark')->nullable();
            $table->dropColumn('credited_date');
            $table->float('credited_days')->nullable();
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
            $table->dropColumn('remark');
            $table->dropColumn('credited_days');
            $table->timestamp('credited_date')->nullable();
        });
    }
}
