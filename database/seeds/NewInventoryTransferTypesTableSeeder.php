<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class NewInventoryTransferTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();
        DB::table('inventory_transfer_types')->insert([
            [
                'name' => 'Site',
                'slug' => 'site',
                'type' => 'IN',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
