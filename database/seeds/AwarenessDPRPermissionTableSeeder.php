<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AwarenessDPRPermissionTableSeeder extends Seeder
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
                'name' => 'create-manage-general-awareness',
                'module_id' => DB::table('modules')->where('slug','manage-general-awareness')->pluck('id')->first(),
                'is_mobile' => true,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','create')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'view-manage-general-awareness',
                'module_id' => DB::table('modules')->where('slug','manage-general-awareness')->pluck('id')->first(),
                'is_mobile' => true,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','view')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'create-manage-dpr',
                'module_id' => DB::table('modules')->where('slug','manage-dpr')->pluck('id')->first(),
                'is_mobile' => true,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','create')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'view-manage-dpr',
                'module_id' => DB::table('modules')->where('slug','manage-dpr')->pluck('id')->first(),
                'is_mobile' => true,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','view')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'approve-inventory-in-out-transfer',
                'module_id' => DB::table('modules')->where('slug','inventory-in-out-transfer')->pluck('id')->first(),
                'is_mobile' => true,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','approve')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'create-salary-request-handler',
                'module_id' => DB::table('modules')->where('slug','salary-request-handler')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','create')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'view-salary-request-handler',
                'module_id' => DB::table('modules')->where('slug','salary-request-handler')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','view')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'edit-salary-request-handler',
                'module_id' => DB::table('modules')->where('slug','salary-request-handler')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','edit')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'approve-salary-request-handler',
                'module_id' => DB::table('modules')->where('slug','salary-request-handler')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','approve')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'create-purchase-bill-entry',
                'module_id' => DB::table('modules')->where('slug','purchase-bill-entry')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','create')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'view-purchase-bill-entry',
                'module_id' => DB::table('modules')->where('slug','purchase-bill-entry')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','view')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'edit-purchase-bill-entry',
                'module_id' => DB::table('modules')->where('slug','purchase-bill-entry')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','edit')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ]
        ]);
    }
}
