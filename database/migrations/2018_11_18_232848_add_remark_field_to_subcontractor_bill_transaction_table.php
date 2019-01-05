<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRemarkFieldToSubcontractorBillTransactionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subcontractor_bill_transactions', function (Blueprint $table) {
            $table->text('status_remark')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('subcontractor_bill_transactions', function (Blueprint $table) {
            $table->dropColumn('status_remark');
        });
    }
}
