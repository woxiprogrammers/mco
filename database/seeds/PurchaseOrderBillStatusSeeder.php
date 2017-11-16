<?php

use Illuminate\Database\Seeder;

class PurchaseOrderBillStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = \Carbon\Carbon::now();
        \Illuminate\Support\Facades\DB::table('purchase_order_bill_statuses')->insert([
            [
                'name' => 'Bill Paid',
                'slug' => 'bill-paid',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Bill Pending',
                'slug' => 'bill-pending',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Amendment Pending',
                'slug' => 'amendment-pending',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'GRN Generated',
                'slug' => 'grn-generated',
                'created_at' => $now,
                'updated_at' => $now
            ]
        ]);
    }
}
