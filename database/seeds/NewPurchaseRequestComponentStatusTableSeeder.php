<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class NewPurchaseRequestComponentStatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();
        DB::table('purchase_request_component_statuses')->insert([
            [
                'name' => 'Purchase Requested',
                'slug' => 'purchase-requested',
                'created_at' => $now,
                'updated_at' => $now
            ],
        ]);
    }
}
