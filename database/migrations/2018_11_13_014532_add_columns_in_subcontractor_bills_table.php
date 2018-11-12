<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsInSubcontractorBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subcontractor_bills', function (Blueprint $table) {
            $table->float('discount')->nullable()->default(0);
            $table->text('discount_description')->nullable();
            $table->float('subtotal')->nullable();
            $table->float('round_off_amount')->nullable();
            $table->float('grand_total')->nullable();
            $table->float('qty')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('subcontractor_bills', function (Blueprint $table) {
            $table->dropColumn('discount');
            $table->dropColumn('discount_description');
            $table->dropColumn('subtotal');
            $table->dropColumn('round_off_amount');
            $table->dropColumn('grand_total');
            $table->float('qty')->nullable(false)->change();
        });
    }
}
