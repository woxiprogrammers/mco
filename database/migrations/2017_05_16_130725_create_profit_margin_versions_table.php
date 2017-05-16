<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProfitMarginVersionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profit_margin_versions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('profit_margin_id');
            $table->foreign('profit_margin_id')->references('id')->on('profit_margins')->onUpdate('cascade')->onUpdate('cascade');
            $table->double('percentage');
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
        Schema::dropIfExists('profit_margin_versions');
    }
}
