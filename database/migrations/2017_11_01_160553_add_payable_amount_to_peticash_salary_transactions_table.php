<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPayableAmountToPeticashSalaryTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('peticash_salary_transactions', function (Blueprint $table) {
            $table->float('payable_amount')->nullable()->after('amount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('peticash_salary_transactions', function (Blueprint $table) {
            $table->dropColumn('payable_amount');
        });
    }
}
