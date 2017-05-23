<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUnitConversionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('unit_conversions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('unit_1_id');
            $table->double('unit_1_value');
            $table->unsignedInteger('unit_2_id');
            $table->double('unit_2_value');
            $table->foreign('unit_1_id')->references('id')->on('units')->onUpdate('cascade')->onUpdate('cascade');
            $table->foreign('unit_2_id')->references('id')->on('units')->onUpdate('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('unit_conversions');
    }
}
