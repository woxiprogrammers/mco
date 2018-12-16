<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeDatatypeQuantityColumnScBillSummariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subcontractor_bill_summaries', function (Blueprint $table) {
            $table->float('quantity')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('subcontractor_bill_summaries', function (Blueprint $table) {
            $table->unsignedInteger('quantity')->change();
        });
    }
}
