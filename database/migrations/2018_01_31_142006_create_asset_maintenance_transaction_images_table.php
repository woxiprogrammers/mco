<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssetMaintenanceTransactionImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('asset_maintenance_transaction_images', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',255);
            $table->unsignedBigInteger('asset_maintenance_transaction_id');
            $table->foreign('asset_maintenance_transaction_id')->references('id')->on('asset_maintenance_transactions')->onDelete('cascade')->onUpdate('cascade');
            $table->boolean('is_pre_grn')->default('false');
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
        Schema::dropIfExists('asset_maintenance_transaction_images');
    }
}
