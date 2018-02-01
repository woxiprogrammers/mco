<?php

use Illuminate\Database\Seeder;

class AssetMaintenanceTransactionStatusesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = \Carbon\Carbon::now();
        \Illuminate\Support\Facades\DB::table('asset_maintenance_transaction_statuses')->insert([
            [
                'name' => 'GRN Generated',
                'slug' => 'grn-generated',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Bill Pending',
                'slug' => 'bill-pending',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Bill Generated',
                'slug' => 'bill-generated',
                'created_at' => $now,
                'updated_at' => $now
            ]
        ]);
    }
}
