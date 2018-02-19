<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ComponentTransferPermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();
        DB::table('permissions')->insert([
            [
                'name' => 'view-component-transfer',
                'module_id' => DB::table('modules')->where('slug','component-transfer')->pluck('id')->first(),
                'is_mobile' => true,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','view')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'approve-component-transfer',
                'module_id' => DB::table('modules')->where('slug','component-transfer')->pluck('id')->first(),
                'is_mobile' => true,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','approve')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ]
        ]);
    }
}
