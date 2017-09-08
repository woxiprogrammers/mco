<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterInventoryComponentTransfersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inventory_component_transfers', function (Blueprint $table) {
            $table->dropColumn('next_maintenance_date');
            $table->string('next_maintenance_hour')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inventory_component_transfers', function (Blueprint $table) {
            $table->timestamp('next_maintenance_date')->nullable();
            $table->dropColumn('next_maintenance_hour');
        });
    }
}
