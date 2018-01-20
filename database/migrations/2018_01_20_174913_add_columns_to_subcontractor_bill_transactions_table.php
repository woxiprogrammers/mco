<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToSubcontractorBillTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subcontractor_bill_transactions', function (Blueprint $table) {
            $table->double('debit')->nullable();
            $table->double('hold')->nullable();
            $table->double('retention_percent')->nullable();
            $table->double('retention_amount')->nullable();
            $table->double('tds_percent')->nullable();
            $table->double('tds_amount')->nullable();
            $table->double('other_recovery')->nullable();
            $table->text('remark')->nullable();
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
            $table->dropColumn('debit');
            $table->dropColumn('hold');
            $table->dropColumn('retention_percent');
            $table->dropColumn('retention_amount');
            $table->dropColumn('tds_percent');
            $table->dropColumn('tds_amount');
            $table->dropColumn('other_recovery');
            $table->dropColumn('remark');
        });
    }
}
