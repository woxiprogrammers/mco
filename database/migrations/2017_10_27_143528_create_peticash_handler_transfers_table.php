<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePeticashHandlerTransfersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('peticash_site_transfers', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedInteger('received_from_user_id');
            $table->foreign('received_from_user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->double('amount');
            $table->timestamp('date');
            $table->text('remark')->nullable();
            $table->unsignedInteger('project_site_id');
            $table->foreign('project_site_id')->references('id')->on('project_sites')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedInteger('payment_id');
            $table->foreign('payment_id')->references('id')->on('payment_types')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('peticash_site_transfers');
    }
}
