<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTotalAmountBalanceAmountColumnsToBankInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bank_info', function (Blueprint $table) {
            $table->double('balance_amount')->default(0);
            $table->double('total_amount')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bank_info', function (Blueprint $table) {
            $table->dropColumn('balance_amount');
            $table->dropColumn('total_amount');
        });
    }
}
