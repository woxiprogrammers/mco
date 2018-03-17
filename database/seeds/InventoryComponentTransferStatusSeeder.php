<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class InventoryComponentTransferStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();
        DB::table('inventory_component_transfer_statuses')->insert([
            [
                'name' => 'GRN Generated',
                'slug' => 'grn-generated',
                'created_at' => $now,
                'updated_at' => $now
            ]
        ]);
    }
}
