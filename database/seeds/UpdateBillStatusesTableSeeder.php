<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UpdateBillStatusesTableSeeder extends Seeder
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
                'name' => 'Draft',
                'slug' => 'draft',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Approve',
                'slug' => 'approved',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Cancel',
                'slug' => 'cancelled',
                'created_at' => $now,
                'updated_at' => $now
            ],
        ]);
    }
}
