<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddShowPRDetailMaterialRequestComponentVersionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('material_request_component_versions', function (Blueprint $table) {
            $table->boolean('show_p_r_detail')->default(false)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('material_request_component_versions', function (Blueprint $table) {
            $table->dropColumn('show_p_r_detail');
        });
    }
}
