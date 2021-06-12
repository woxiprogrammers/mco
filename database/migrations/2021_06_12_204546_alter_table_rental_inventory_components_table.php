<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableRentalInventoryComponentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rental_inventory_components', function (Blueprint $table) {
            $table->float('opening_stock')->change();
            $table->float('closing_stock')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rental_inventory_components', function (Blueprint $table) {
            $table->unsignedInteger('opening_stock')->change();;
            $table->unsignedInteger('closing_stock')->change();;
        });
    }
}
