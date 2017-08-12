<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMultipleColumnsInPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->unsignedInteger('module_id');
            $table->foreign('module_id')->references('id')->on('modules')->onUdate('cascade')->onDelete('cascade');
            $table->boolean('is_web');
            $table->boolean('is_mobile');
            $table->string('type',20);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropColumn('slug');
            $table->dropColumn('is_web');
            $table->dropColumn('is_mobile');
            $table->dropColumn('type');
            $table->dropForeign('permissions_module_id_foreign');
            $table->dropColumn('module_id');
        });
    }
}
