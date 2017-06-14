<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('first_name',40)->nullable();
            $table->string('last_name',40)->nullable();
            $table->string('email',255)->nullable();
            $table->string('mobile',15)->nullable();
            $table->string('password',255)->nullable();
            $table->string('dob')->nullable();
            $table->string('gender',1)->nullable();
            $table->boolean('is_active');
            $table->unsignedInteger('role_id');
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
