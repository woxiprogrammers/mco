<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class QuotationProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();
        DB::table('quotation_products')->insert([
            [
                'quotation_id' => 1,
                'description' => 'This quotation contains 2 products, this product contains 2 materials',
                'product_version_id' => 14,
                'rate_per_unit' => 49.28,
                'quantity' => 10,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'quotation_id' => 1,
                'description' => 'This quotation contains 2 products, this product contains 1 material',
                'product_version_id' => 15,
                'rate_per_unit' => 22.4,
                'quantity' => 15,
                'created_at' => $now,
                'updated_at' => $now
            ]

        ]);
    }
}
