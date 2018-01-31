<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssetMaintenanceTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('asset_maintenance_transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('asset_maintenance_id');
            $table->foreign('asset_maintenance_id')->references('id')->on('asset_maintenance')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedInteger('asset_maintenance_transaction_status_id');
            $table->foreign('asset_maintenance_transaction_status_id')->references('id')->on('asset_maintenance_transaction_statuses')->onDelete('cascade')->onUpdate('cascade');
            $table->string('grn',255);
            $table->string('bill_number',255)->nullable();
            $table->double('bill_amount')->nullable();
            $table->timestamp('in_time')->nullable();
            $table->timestamp('out_time')->nullable();
            $table->text('remark')->nullable();
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
        Schema::dropIfExists('asset_maintenance_transactions');
    }
}
