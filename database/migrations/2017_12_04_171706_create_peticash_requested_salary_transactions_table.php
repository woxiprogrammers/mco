<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePeticashRequestedSalaryTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('peticash_requested_salary_transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('reference_user_id');
            $table->foreign('reference_user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedInteger('employee_id');
            $table->unsignedInteger('project_site_id');
            $table->foreign('project_site_id')->references('id')->on('project_sites')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedInteger('peticash_transaction_type_id');
            $table->foreign('peticash_transaction_type_id')->references('id')->on('peticash_transaction_types')->onDelete('cascade')->onUpdate('cascade');
            $table->float('amount')->nullable();
            $table->float('days')->nullable();
            $table->float('per_day_wages')->nullable();
            $table->unsignedInteger('peticash_status_id');
            $table->foreign('peticash_status_id')->references('id')->on('peticash_statuses')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('peticash_requested_salary_transactions');
    }
}
