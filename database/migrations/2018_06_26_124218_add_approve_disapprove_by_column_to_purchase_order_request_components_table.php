<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddApproveDisapproveByColumnToPurchaseOrderRequestComponentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_order_request_components', function (Blueprint $table) {
            $table->unsignedInteger('approve_disapprove_by_user')->nullable();
            $table->foreign('approve_disapprove_by_user')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_order_request_components', function (Blueprint $table) {
            $table->dropColumn('approve_disapprove_by_user');
        });
    }
}
