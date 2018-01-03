<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTypeSlugColumnIntoVendorMailInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_request_component_vendor_mail_info', function (Blueprint $table) {
            $table->string('type_slug')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_request_component_vendor_mail_info', function (Blueprint $table) {
            $table->dropColumn('type_slug');
        });
    }
}
