<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePoTransactionBackupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_order_transactions_backup', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('purchase_order_id');
            $table->unsignedBigInteger('purchase_order_transaction_status_id');
            $table->string('bill_number',255)->nullable();
            $table->string('vehicle_number',255)->nullable();
            $table->string('grn',255)->index();
            $table->timestamp('in_time')->nullable();
            $table->timestamp('out_time')->nullable();
            $table->text('remark')->nullable();
            $table->double('bill_amount')->nullable();
            $table->timestamps();
        });

        Schema::table('purchase_order_transactions', function (Blueprint $table) {   
            $table->index('grn');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_order_transactions_backup');

        Schema::table('purchase_order_transactions', function (Blueprint $table) {   
            $table->dropIndex(['grn']);
        });
    }
}
