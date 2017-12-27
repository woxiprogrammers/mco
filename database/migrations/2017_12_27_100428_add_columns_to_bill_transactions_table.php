<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToBillTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bill_transactions', function (Blueprint $table) {
            $table->double('retention')->nullable();
            $table->double('tds')->nullable();
            $table->double('debit')->nullable();
            $table->double('hold')->nullable();
            $table->double('balance_advanced_amount')->nullable();
            $table->boolean('paid_from_advanced')->nullable();
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
            $table->dropColumn('retention');
            $table->dropColumn('tds');
            $table->dropColumn('debit');
            $table->dropColumn('hold');
            $table->dropColumn('balance_advanced_amount');
            $table->dropColumn('paid_from_advanced');
        });
    }
}
