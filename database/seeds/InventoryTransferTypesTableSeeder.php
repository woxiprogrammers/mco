<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class InventoryTransferTypesTableSeeder extends Seeder
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
                'name' => 'Client',
                'slug' => 'client',
                'type' => 'IN',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Hand',
                'slug' => 'hand',
                'type' => 'IN',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Office',
                'slug' => 'office',
                'type' => 'IN',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Supplier',
                'slug' => 'supplier',
                'type' => 'IN',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Client',
                'slug' => 'client',
                'type' => 'OUT',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Supplier',
                'slug' => 'supplier',
                'type' => 'OUT',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Site',
                'slug' => 'site',
                'type' => 'OUT',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Labour',
                'slug' => 'labour',
                'type' => 'OUT',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Sub Contractor',
                'slug' => 'sub-contractor',
                'type' => 'OUT',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Maintenance',
                'slug' => 'maintenance',
                'type' => 'IN',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Maintenance',
                'slug' => 'maintenance',
                'type' => 'OUT',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
