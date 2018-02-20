<?php

use Illuminate\Database\Seeder;

class AssetMaintenanceStatusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = \Carbon\Carbon::now();
        \Illuminate\Support\Facades\DB::table('asset_maintenance_statuses')->insert([
            [
                'name' => 'Maintenance Requested',
                'slug' => 'maintenance-requested',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Vendor Assigned',
                'slug' => 'vendor-assigned',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Vendor Approved',
                'slug' => 'vendor-approved',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Maintenance Closed',
                'slug' => 'maintenance-closed',
                'created_at' => $now,
                'updated_at' => $now
            ]
        ]);
    }
}
