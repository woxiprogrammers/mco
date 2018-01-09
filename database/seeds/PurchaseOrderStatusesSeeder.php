<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PurchaseOrderStatusesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();
        DB::table('purchase_order_statuses')->insert([
            [
                'name' => 'Open',
                'slug' => 'open',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Re-open',
                'slug' => 're-open',
                'created_at' => $now,
                'updated_at' => $now
            ],
        ]);
    }
}
