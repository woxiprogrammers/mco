<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class PurchaseOrderStatuses extends Seeder
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
                'name' => 'Close',
                'slug' => 'close',
                'created_at' => $now,
                'updated_at' => $now
            ],
        ]);
    }
}
