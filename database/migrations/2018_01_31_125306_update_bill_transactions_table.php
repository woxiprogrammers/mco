<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateBillTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bill_transactions', function (Blueprint $table) {
            $table->dropColumn('subtotal');
            $table->dropColumn('retention');
            $table->dropColumn('tds');
            $table->dropColumn('balance_advanced_amount');
            $table->float('retention_percent')->nullable();
            $table->double('retention_amount')->nullable();
            $table->float('tds_percent')->nullable();
            $table->double('tds_amount')->nullable();
            $table->double('amount')->nullable();
            $table->double('other_recovery_value')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bill_transactions', function (Blueprint $table) {
            $table->double('subtotal')->nullable();
            $table->double('retention')->nullable();
            $table->double('tds')->nullable();
            $table->double('balance_advanced_amount')->nullable();
            $table->dropColumn('retention_percent');
            $table->dropColumn('retention_amount');
            $table->dropColumn('tds_percent');
            $table->dropColumn('tds_amount');
            $table->dropColumn('amount');
            $table->dropColumn('other_recovery_value');
        });
    }
}
