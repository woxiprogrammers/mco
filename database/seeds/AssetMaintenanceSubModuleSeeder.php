<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AssetMaintenanceSubModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();
        DB::table('modules')->insert([
            [
                'name' => 'Asset Maintenance Approval',
                'slug' => 'asset-maintenance-approval',
                'module_id' => DB::table('modules')->where('slug','inventory')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Asset Maintenance Billing',
                'slug' => 'asset-maintenance-billing',
                'module_id' => DB::table('modules')->where('slug','inventory')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ]
        ]);
    }
}
