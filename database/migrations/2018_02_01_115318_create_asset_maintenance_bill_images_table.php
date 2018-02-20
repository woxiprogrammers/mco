<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssetMaintenanceBillImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('asset_maintenance_bill_images', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',255);
            $table->unsignedBigInteger('asset_maintenance_bill_id');
            $table->foreign('asset_maintenance_bill_id')->references('id')->on('asset_maintenance_bills')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('asset_maintenance_bill_images');
    }
}
