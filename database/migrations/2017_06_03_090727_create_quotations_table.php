<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuotationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quotations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('project_site_id');
            $table->foreign('project_site_id')->references('id')->on('project_sites')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedInteger('quotation_status_id');
            $table->foreign('quotation_status_id')->references('id')->on('quotation_statuses')->onUpdate('cascade')->onDelete('cascade');
            $table->text('remark')->nullable();
            $table->boolean('is_tax_applied')->default(false);
            $table->boolean('is_summary_applied')->default(false);
            $table->double('discount')->default(0);
            $table->double('built_up_area')->nullable();
            $table->double('carpet_area')->nullable();
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
        Schema::dropIfExists('quotations');
    }
}
