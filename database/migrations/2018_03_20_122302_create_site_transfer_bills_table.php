<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSiteTransferBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('site_transfer_bills', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('inventory_component_transfer_id');
            $table->foreign('inventory_component_transfer_id')->references('id')->on('inventory_component_transfers')->onDelete('cascade')->onUpdate('cascade');
            $table->string('bill_number')->nullable();
            $table->timestamp('bill_date')->nullable();
            $table->double('subtotal');
            $table->double('tax_amount');
            $table->double('extra_amount');
            $table->float('extra_amount_cgst_percentage')->nullable();
            $table->float('extra_amount_sgst_percentage')->nullable();
            $table->float('extra_amount_igst_percentage')->nullable();
            $table->float('extra_amount_cgst_amount')->nullable();
            $table->float('extra_amount_sgst_amount')->nullable();
            $table->float('extra_amount_igst_amount')->nullable();
            $table->double('total');
            $table->text('remark')->nullable();
            $table->string('format_id')->nullable();
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
        Schema::dropIfExists('site_transfer_bills');
    }
}
