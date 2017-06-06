<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BillStatusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();
        DB::table('bill_statuses')->insert([
            [
                'name' => 'Paid',
                'slug' => 'paid',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Unpaid',
                'slug' => 'unpaid',
                'created_at' => $now,
                'updated_at' => $now
            ],
        ]);
    }
}
