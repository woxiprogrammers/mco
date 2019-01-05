<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeFieldsNullableSubcontractorStructureTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subcontractor_structure', function (Blueprint $table) {
            $table->float('rate')->default(0)->nullable()->change();
            $table->float('total_work_area')->default(0)->nullable()->change();
            $table->unsignedInteger('summary_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('subcontractor_structure', function (Blueprint $table) {
            $table->float('rate')->default(0)->nullable(false)->change();
            $table->float('total_work_area')->default(0)->nullable(false)->change();
            $table->unsignedInteger('summary_id')->nullable(false)->change();
        });
    }
}
