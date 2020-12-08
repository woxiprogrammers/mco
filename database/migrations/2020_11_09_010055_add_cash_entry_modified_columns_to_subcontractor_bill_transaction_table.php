<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCashEntryModifiedColumnsToSubcontractorBillTransactionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subcontractor_bill_transactions', function (Blueprint $table) {
            $table->boolean('is_modified')->default(false);
            $table->dateTime('modified_at')->nullable()->default(null);
            $table->unsignedInteger('modified_by')->nullable()->default(null);
            $table->foreign('modified_by')->references('id')->on('users')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('subcontractor_bill_transactions', function (Blueprint $table) {
            $table->dropColumn('is_modified');
            $table->dropColumn('modified_at');
            $table->dropColumn('modified_by');
        });
    }
}
