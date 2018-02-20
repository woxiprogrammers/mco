<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssetMaintenanceBillPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('asset_maintenance_bill_payments', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('asset_maintenance_bill_id');
            $table->foreign('asset_maintenance_bill_id')->references('id')->on('asset_maintenance_bills')->onUpdate('cascade')->onDelete('cascade');
            $table->double('amount');
            $table->unsignedInteger('payment_id')->nullable();
            $table->foreign('payment_id')->references('id')->on('payment_types')->onDelete('cascade')->onUpdate('cascade');
            $table->string('reference_number',255)->nullable();
            $table->boolean('is_advance')->nullable();
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
        Schema::dropIfExists('asset_maintenance_bill_payments');
    }
}
