<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAdditonalFieldsToSubcontractorStructureTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subcontractor_structure', function (Blueprint $table) {
            $table->double('cancelled_bill_transaction_total_amount')->default(0);
            $table->double('cancelled_bill_transaction_balance_amount')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('subcontractor_structure', function (Blueprint $table) {
            $table->dropColumn('cancelled_bill_transaction_total_amount');
            $table->dropColumn('cancelled_bill_transaction_balance_amount');
        });
    }
}
