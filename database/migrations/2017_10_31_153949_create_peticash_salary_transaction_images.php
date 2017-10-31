<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePeticashSalaryTransactionImages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('peticash_salary_transaction_images', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('peticash_salary_transactions_id');
            $table->foreign('peticash_salary_transactions_id')->references('id')->on('peticash_salary_transactions')->onDelete('cascade')->onUpdate('cascade');
            $table->string('name');
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
        Schema::dropIfExists('peticash_salary_transaction_images');
    }
}
