<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubcontractorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subcontractor', function (Blueprint $table) {
            $table->increments('id');
            $table->string('company_name',512);
            $table->string('category',255)->nullable();
            $table->string('subcategory',255)->nullable();
            $table->text('desc_prod_service')->nullable();
            $table->string('nature_of_work',512)->nullable();
            $table->string('sc_turnover_pre_yr',255)->nullable();
            $table->string('sc_turnover_two_fy_ago',255)->nullable();
            $table->string('primary_cont_person_name',512)->nullable();
            $table->string('primary_cont_person_mob_number',512)->nullable();
            $table->string('primary_cont_person_email',512)->nullable();
            $table->string('escalation_cont_person_name',512)->nullable();
            $table->string('escalation_cont_person_mob_number',512)->nullable();
            $table->string('sc_pancard_no',255)->nullable();
            $table->string('sc_service_no',255)->nullable();
            $table->string('sc_vat_no',255)->nullable();
            $table->boolean('is_active');
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
        Schema::dropIfExists('subcontractor');
    }
}
