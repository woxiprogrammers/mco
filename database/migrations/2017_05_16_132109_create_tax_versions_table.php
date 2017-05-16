<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTaxVersionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tax_versions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('tax_id');
            $table->float('percentage');
            $table->foreign('tax_id')->references('id')->on('taxes')->onUpdate('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('tax_versions');
    }
}
