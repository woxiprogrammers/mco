<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyDprDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dpr_details', function (Blueprint $table) {
            $table->unsignedBigInteger('subcontractor_dpr_category_relation_id');
            $table->foreign('subcontractor_dpr_category_relation_id')->references('id')->on('subcontractor_dpr_category_relations')->onDelete('cascade')->onUpdate('cascade');
            $table->dropForeign('dpr_details_dpr_main_category_id_foreign');
            $table->dropForeign('dpr_details_subcontractor_id_foreign');
            $table->dropColumn('dpr_main_category_id');
            $table->dropColumn('subcontractor_id');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dpr_details', function (Blueprint $table) {
            $table->unsignedInteger('subcontractor_id');
            $table->foreign('subcontractor_id')->references('id')->on('subcontractor')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedInteger('dpr_main_category_id');
            $table->foreign('dpr_main_category_id')->references('id')->on('dpr_main_categories')->onDelete('cascade')->onUpdate('cascade');
            $table->dropForeign('dpr_details_subcontractor_dpr_category_relation_id_foreign');
            $table->dropColumn('subcontractor_dpr_category_relation_id');
        });
    }
}
