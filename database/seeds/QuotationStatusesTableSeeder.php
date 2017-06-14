<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class QuotationStatusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();
        DB::table('quotation_statuses')->insert([
            [
                'name' => 'Draft',
                'slug' => 'draft',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Approved',
                'slug' => 'approved',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Disapproved',
                'slug' => 'disapproved',
                'created_at' => $now,
                'updated_at' => $now
            ]
        ]);
    }
}
