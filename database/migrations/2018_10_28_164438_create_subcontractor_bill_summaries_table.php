<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubcontractorBillSummariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subcontractor_bill_summaries', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('subcontractor_bill_id');
            $table->foreign('subcontractor_bill_id')->references('id')->on('subcontractor_bills')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedInteger('subcontractor_structure_summary_id');
            $table->foreign('subcontractor_structure_summary_id')->references('id')->on('subcontractor_structure_summaries')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedInteger('quantity');
            $table->unsignedBigInteger('total_work_area');
            $table->text('description')->nullable();
            $table->float('number_of_floors')->nullable();
            $table->boolean('is_deleted')->default(false);
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
        Schema::dropIfExists('subcontractor_bill_summaries');
    }
}
