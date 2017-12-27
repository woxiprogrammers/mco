<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTdsPfPtEsicColumnsToPeticashSalaryTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('peticash_salary_transactions', function (Blueprint $table) {
            $table->double('tds')->nullable();
            $table->double('pf')->nullable();
            $table->double('pt')->nullable();
            $table->double('esic')->nullable();
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
            $table->dropColumn('tds');
            $table->dropColumn('pf');
            $table->dropColumn('pt');
            $table->dropColumn('esic');
        });
    }
}
