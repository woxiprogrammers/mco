<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAdvanceRelatedColumnsInSubcontractorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subcontractor', function (Blueprint $table) {
            $table->double('total_advance_amount')->nullable();
            $table->double('balance_advance_amount')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('subcontractor', function (Blueprint $table) {
            $table->dropColumn('total_advance_amount');
            $table->dropColumn('balance_advance_amount');
        });
    }
}
