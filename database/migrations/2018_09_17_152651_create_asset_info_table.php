<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssetInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('asset_info', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamp('in_date')->nullable();
            $table->timestamp('out_date')->nullable();
            $table->double('in_quantity')->nullable();
            $table->double('out_quantity')->nullable();
            $table->unsignedInteger('in_reference_id')->nullable();
            $table->foreign('in_reference_id')->references('id')->on('inventory_component_transfers')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedInteger('out_reference_id')->nullable();
            $table->foreign('out_reference_id')->references('id')->on('inventory_component_transfers')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('asset_info');
    }
}
