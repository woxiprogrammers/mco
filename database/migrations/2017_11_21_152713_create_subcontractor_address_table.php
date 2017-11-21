<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubcontractorAddressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subcontractor_address', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('subcontractor_id');
            $table->foreign('subcontractor_id')->references('id')->on('subcontractor')->onDelete('cascade')->onUpdate('cascade');
            $table->text('address1')->nullable();
            $table->text('address2')->nullable();
            $table->string('city',255)->nullable();
            $table->string('state',255)->nullable();
            $table->string('country',512)->nullable();
            $table->string('pincode',255)->nullable();
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
        Schema::dropIfExists('subcontractor_address');
    }
}
