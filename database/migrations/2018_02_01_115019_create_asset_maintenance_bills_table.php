<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssetMaintenanceBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('asset_maintenance_bills', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('asset_maintenance_id');
            $table->foreign('asset_maintenance_id')->references('id')->on('asset_maintenance')->onDelete('cascade')->onUpdate('cascade');
            $table->double('amount')->nullable();
            $table->float('cgst_percentage')->nullable();
            $table->double('cgst_amount')->nullable();
            $table->float('sgst_percentage')->nullable();
            $table->double('sgst_amount')->nullable();
            $table->float('igst_percentage')->nullable();
            $table->double('igst_amount')->nullable();
            $table->double('extra_amount')->nullable();
            $table->string('bill_number')->nullable();
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
        Schema::dropIfExists('asset_maintenance_bills');
    }
}
