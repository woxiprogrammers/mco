<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSiteTransferBillImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('site_transfer_bill_images', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('site_transfer_bill_id');
            $table->string('name',255);
            $table->foreign('site_transfer_bill_id')->references('id')->on('site_transfer_bills')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('site_transfer_bill_images');
    }
}
