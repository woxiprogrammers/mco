<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PurchaseOrderBillViewSeeder extends Seeder
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
                'name' => 'Purchase Order Bill View',
                'slug' => 'purchase-order-bill-view',
                'module_id' => DB::table('modules')->where('slug','purchase')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ]
        ]);
    }
}
