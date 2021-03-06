<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsVoucherCreatedColumnToSalaryPeticashTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('peticash_salary_transactions', function (Blueprint $table) {
            $table->boolean('is_voucher_created')->default(false);
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
            $table->dropColumn('is_voucher_created');
        });
    }
}
