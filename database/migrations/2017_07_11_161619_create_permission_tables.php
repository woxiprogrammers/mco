<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePermissionTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tableNames = config('laravel-permission.table_names');
        $foreignKeys = config('laravel-permission.foreign_keys');

        Schema::create($tableNames['permissions'], function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
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
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('role_has_permissions');
        /*Schema::drop($tableNames['role_has_permissions']);
        Schema::drop($tableNames['user_has_roles']);
        Schema::drop($tableNames['user_has_permissions']);
        //Schema::drop($tableNames['roles']);
        Schema::drop($tableNames['permissions']);*/
    }
}
