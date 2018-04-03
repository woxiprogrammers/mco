<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SubcontractorPermissionsTableSeeder extends Seeder
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
                'name' => 'create-subcontractor-structure',
                'module_id' => DB::table('modules')->where('slug','subcontractor-structure')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','create')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'view-subcontractor-structure',
                'module_id' => DB::table('modules')->where('slug','subcontractor-structure')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','view')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'create-subcontractor-billing',
                'module_id' => DB::table('modules')->where('slug','subcontractor-billing')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','create')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'edit-subcontractor-billing',
                'module_id' => DB::table('modules')->where('slug','subcontractor-billing')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','edit')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'view-subcontractor-billing',
                'module_id' => DB::table('modules')->where('slug','subcontractor-billing')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','view')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'approve-subcontractor-billing',
                'module_id' => DB::table('modules')->where('slug','subcontractor-billing')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','approve')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ]
        ]);
    }
}
