<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchasePeticashTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_peticash_transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',255);
            $table->unsignedInteger('project_site_id');
            $table->foreign('project_site_id')->references('id')->on('project_sites')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedInteger('component_type_id');
            $table->foreign('component_type_id')->references('id')->on('material_request_component_types')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedInteger('reference_id')->nullable();
            $table->unsignedInteger('payment_type_id')->nullable();
            $table->foreign('payment_type_id')->references('id')->on('payment_types')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedInteger('peticash_transaction_type_id');
            $table->foreign('peticash_transaction_type_id')->references('id')->on('peticash_transaction_types')->onDelete('cascade')->onUpdate('cascade');
            $table->string('source_name',255)->nullable();
            $table->double('quantity')->nullable();
            $table->unsignedInteger('unit_id')->nullable();
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade')->onUpdate('cascade');
            $table->string('bill_number',255)->nullable();
            $table->double('bill_amount')->nullable();
            $table->string('vehicle_number',255)->nullable();
            $table->timestamp('in_time')->nullable();
            $table->timestamp('out_time')->nullable();
            $table->text('remark')->nullable();
            $table->timestamp('date')->nullable();
            $table->unsignedInteger('reference_user_id');
            $table->foreign('reference_user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->string('grn');
            $table->unsignedInteger('peticash_status_id');
            $table->foreign('peticash_status_id')->references('id')->on('peticash_statuses')->onDelete('cascade')->onUpdate('cascade');
            $table->string('reference_number')->nullable();
            $table->text('admin_remark')->nullable();
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
        Schema::dropIfExists('purchase_peticash_transactions');
    }
}
