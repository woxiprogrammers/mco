<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchasePeticashTransactionImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_peticash_transaction_images', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('purchase_peticash_transaction_id');
            $table->foreign('purchase_peticash_transaction_id')->references('id')->on('purchase_peticash_transactions')->onDelete('cascade')->onUpdate('cascade');
            $table->string('name');
            $table->string('type');
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
        Schema::dropIfExists('purchase_peticash_transaction_images');
    }
}
