<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddChecklistCategoryIdToProjectSiteChecklistsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_site_checklists', function (Blueprint $table) {
            $table->unsignedInteger('checklist_category_id')->nullable();
            $table->foreign('checklist_category_id')->references('id')->on('checklist_categories')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('project_site_checklists', function (Blueprint $table) {
            $table->dropForeign('project_site_checklists_checklist_category_id_foreign');
            $table->dropColumn('checklist_category_id');
        });
    }
}
