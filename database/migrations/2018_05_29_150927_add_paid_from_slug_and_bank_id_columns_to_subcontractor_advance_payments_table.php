<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPaidFromSlugAndBankIdColumnsToSubcontractorAdvancePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subcontractor_advance_payments', function (Blueprint $table) {
            $table->string('paid_from_slug',255)->nullable();
            $table->unsignedInteger('bank_id')->nullable();
            $table->foreign('bank_id')->references('id')->on('bank_info')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('subcontractor_advance_payments', function (Blueprint $table) {
            $table->dropColumn('paid_from_slug');
            $table->dropColumn('bank_id');
        });
    }
}
