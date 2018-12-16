<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDateColumnsInSubcontractorBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subcontractor_bills', function (Blueprint $table) {
            $table->date('bill_date')->nullable();
            $table->date('performa_invoice_date')->nullable();
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
            $table->dropColumn('performa_invoice_date');
            $table->dropColumn('bill_date');
        });
    }
}
