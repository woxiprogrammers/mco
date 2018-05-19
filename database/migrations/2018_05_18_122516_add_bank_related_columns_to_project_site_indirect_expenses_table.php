<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBankRelatedColumnsToProjectSiteIndirectExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_site_indirect_expenses', function (Blueprint $table) {
            $table->string('paid_from_slug',255)->nullable();
            $table->unsignedInteger('bank_id')->nullable();
            $table->foreign('bank_id')->references('id')->on('bank_info')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedInteger('payment_type_id')->nullable();
            $table->foreign('payment_type_id')->references('id')->on('payment_types')->onDelete('cascade')->onUpdate('cascade');
            $table->string('reference_number',255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('project_site_indirect_expenses', function (Blueprint $table) {
            $table->dropColumn('paid_from_slug');
            $table->dropColumn('bank_id');
            $table->dropColumn('payment_type_id');
            $table->dropColumn('reference_number');
        });
    }
}
