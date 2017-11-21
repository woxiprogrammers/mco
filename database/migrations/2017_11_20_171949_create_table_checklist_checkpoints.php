<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableChecklistCheckpoints extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('checklist_checkpoints', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('checklist_category_id');
            $table->foreign('checklist_category_id')->references('id')->on('checklist_categories')->onUpdate('cascade')->onDelete('cascade');
            $table->text('description');
            $table->boolean('is_remark_required');
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
        Schema::dropIfExists('checklist_checkpoints');
    }
}
