<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPaidFromSlugColumnToPeticashSiteTransfersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('peticash_site_transfers', function (Blueprint $table) {
            $table->unsignedInteger('bank_id')->nullable();
            $table->foreign('bank_id')->references('id')->on('bank_info')->onDelete('cascade')->onUpdate('cascade');
            $table->string('paid_from_slug',255)->nullable();
            $table->unsignedInteger('payment_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('peticash_site_transfers', function (Blueprint $table) {
            $table->dropColumn('bank_id');
            $table->dropColumn('paid_from_slug');
        });
    }
}
