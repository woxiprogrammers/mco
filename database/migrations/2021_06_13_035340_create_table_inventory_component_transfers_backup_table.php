<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableInventoryComponentTransfersBackupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory_component_transfers_backup', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('inventory_component_id');
            $table->unsignedInteger('transfer_type_id');
            $table->double('quantity')->nullable();
            $table->unsignedInteger('unit_id')->nullable();
            $table->text('remark')->nullable();
            $table->string('source_name',255)->nullable();
            $table->string('bill_number',255)->nullable();
            $table->double('bill_amount')->nullable();
            $table->string('vehicle_number',255)->nullable();
            $table->timestamp('in_time')->nullable();
            $table->timestamp('out_time')->nullable();
            $table->unsignedInteger('payment_type_id')->nullable();
            $table->timestamp('date')->nullable();
            $table->string('next_maintenance_hour')->nullable();
            $table->unsignedInteger('user_id')->nullable();
            $table->text('comment_data')->nullable();
            $table->string('grn')->nullable();
            $table->unsignedInteger('inventory_component_transfer_status_id')->nullable();
            $table->double('rate_per_unit')->nullable();
            $table->double('cgst_percentage')->nullable();
            $table->double('sgst_percentage')->nullable();
            $table->double('igst_percentage')->nullable();
            $table->double('cgst_amount')->nullable();
            $table->double('sgst_amount')->nullable();
            $table->double('igst_amount')->nullable();
            $table->double('total')->nullable();
            $table->unsignedInteger('vendor_id')->nullable();
            $table->double('transportation_amount')->nullable();
            $table->double('transportation_cgst_percent')->nullable();
            $table->double('transportation_sgst_percent')->nullable();
            $table->double('transportation_igst_percent')->nullable();
            $table->string('driver_name',255)->nullable();
            $table->string('mobile',15)->nullable();
            $table->unsignedInteger('related_transfer_id')->nullable();
            $table->unsignedInteger('inventory_transfer_challan_id')->nullable();
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
        Schema::dropIfExists('inventory_component_transfers_backup');
    }
}
