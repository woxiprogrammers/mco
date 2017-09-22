<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPermissionTypesInUserHasPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_has_permissions', function (Blueprint $table) {
            $table->boolean('is_web')->default(false);
            $table->boolean('is_mobile')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_has_permissions', function (Blueprint $table) {
            $table->dropColumn('is_web');
            $table->dropColumn('is_mobile');
        });
    }
}
