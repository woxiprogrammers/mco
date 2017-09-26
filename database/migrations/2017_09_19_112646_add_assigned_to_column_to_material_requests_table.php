<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAssignedToColumnToMaterialRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('material_requests', function (Blueprint $table) {
            $table->unsignedInteger('assigned_to');
            $table->foreign('assigned_to')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('material_requests', function (Blueprint $table) {
            $table->dropForeign('material_requests_assigned_to_foreign');
            $table->dropColumn('assigned_to');
        });
    }
}
