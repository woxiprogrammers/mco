<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSiteTransferBillChallansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('site_transfer_bill_challans', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('site_transfer_bill_id');
            $table->foreign('site_transfer_bill_id')->references('id')->on('site_transfer_bills')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedInteger('inventory_transfer_challan_id');
            $table->foreign('inventory_transfer_challan_id')->references('id')->on('inventory_transfer_challan')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('site_transfer_bill_challans');
    }
}
