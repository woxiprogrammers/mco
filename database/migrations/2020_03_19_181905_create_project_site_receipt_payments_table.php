<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectSiteReceiptPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_site_receipt_payments', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('project_site_id');
            $table->foreign('project_site_id')->references('id')->on('project_sites')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedInteger('payment_id')->nullable();
            $table->double('amount')->nullable();
            $table->unsignedInteger('bank_id')->nullable();
            $table->string('reference_number',255)->nullable();
            $table->date('adv_receipt_date')->nullable();
            $table->string('paid_from_slug',255)->nullable();
            $table->foreign('bank_id')->references('id')->on('bank_info')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('payment_id')->references('id')->on('payment_types')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('project_site_receipt_payments');
    }
}
