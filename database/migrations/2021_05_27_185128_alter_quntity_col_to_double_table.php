<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Doctrine\DBAL\Types\FloatType;
use Doctrine\DBAL\Types\Type;


class AlterQuntityColToDoubleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
	if (!Type::hasType('double')) {
		Type::addType('double', FloatType::class);
	}
        Schema::table('rental_inventory_transfers', function (Blueprint $table) {
            $table->double('quantity')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rental_inventory_transfers', function (Blueprint $table) {
            $table->unsignedInteger('quantity')->change();
        });
    }
}
