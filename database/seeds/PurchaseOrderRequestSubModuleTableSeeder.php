<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PurchaseOrderRequestSubModuleTableSeeder extends Seeder
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
                'name' => 'Purchase Order Request',
                'slug' => 'purchase-order-request',
                'module_id' => DB::table('modules')->where('slug','purchase')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
        ]);
    }
}
