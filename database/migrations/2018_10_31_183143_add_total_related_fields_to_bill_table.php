<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTotalRelatedFieldsToBillTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bills', function (Blueprint $table) {
            $table->double('sub_total')->nullable();
            $table->double('with_tax_amount')->nullable();
            $table->double('rounded_amount_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bills', function (Blueprint $table) {
            $table->dropColumn('sub_total');
            $table->dropColumn('with_tax_amount');
            $table->dropColumn('rounded_amount_by');
        });
    }
}
