<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FuelAssetReadingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $inventoryComponentId = DB::table('inventory_components')->where('name','ilike','Wood Cutter')->pluck('id')->first();
        $superAdminRoleId = DB::table('roles')->where('slug','superadmin')->pluck('id')->first();
        $userId = DB::table('user_has_roles')->where('role_id',$superAdminRoleId)->pluck('user_id')->first();

        DB::table('fuel_asset_readings')->insert([
            [
                'inventory_component_id' => $inventoryComponentId,
                'start_reading' => 100,
                'stop_reading' => 200,
                'top_up_time' => Carbon::now()->subDays(1),
                'start_time' => Carbon::now()->subHours(16),
                'stop_time' => Carbon::now()->subHours(12),
                'user_id' => $userId,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'inventory_component_id' => $inventoryComponentId,
                'start_reading' => 200,
                'stop_reading' => 300,
                'top_up_time' => Carbon::now()->subDays(1),
                'start_time' => Carbon::now()->subHours(10),
                'stop_time' => Carbon::now()->subHours(6),
                'user_id' => $userId,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
