<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableAwarenessSubCategory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('awareness_sub_category', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('awareness_main_category_id');
            $table->foreign('awareness_main_category_id')->references('id')->on('awareness_main_category')->onUpdate('cascade')->onDelete('cascade');
            $table->text('name');
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
        Schema::dropIfExists('awareness_sub_category');
    }
}
