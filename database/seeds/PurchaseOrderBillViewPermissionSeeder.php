<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PurchaseOrderBillViewPermissionSeeder extends Seeder
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
                'name' => 'view-purchase-order-bill-listing',
                'module_id' => DB::table('modules')->where('slug','purchase-order-bill-view')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','view')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ]
        ]);
    }
}
