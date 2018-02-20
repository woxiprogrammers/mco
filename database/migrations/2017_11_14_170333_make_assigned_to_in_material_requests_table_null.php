<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeAssignedToInMaterialRequestsTableNull extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('material_requests', function (Blueprint $table) {
            $table->unsignedInteger('assigned_to')->nullable()->change();
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
            $table->unsignedInteger('assigned_to')->change();
        });
    }
}
