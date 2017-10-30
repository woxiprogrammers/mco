<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',255);
            $table->unsignedInteger('employee_type_id')->nullable();
            $table->foreign('employee_type_id')->references('id')->on('employee_types')->onDelete('cascade')->onUpdate('cascade');
            $table->string('gender',1)->nullable();
            $table->text('address')->nullable();
            $table->string('mobile')->nullable();
            $table->string('pan_card', 255)->nullable();
            $table->string('aadhaar_card', 255)->nullable();
            $table->string('employee_id');
            $table->string('designation')->nullable();
            $table->timestamp('joining_date')->nullable();
            $table->timestamp('termination_date')->nullable();
            $table->string('email',255)->nullable();
            $table->float('per_day_wages');
            $table->string('bank_account_number')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('branch_id')->nullable();
            $table->string('account_holder_name')->nullable();
            $table->string('branch_name')->nullable();
            $table->string('ifs_code')->nullable();
            $table->unsignedInteger('project_site_id')->nullable();
            $table->foreign('project_site_id')->references('id')->on('project_sites')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('employees');
    }
}
