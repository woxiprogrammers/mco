<?php

use Illuminate\Database\Seeder;

class PeticashStatusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = \Carbon\Carbon::now();
        \Illuminate\Support\Facades\DB::table('peticash_statuses')->insert([
            [
                'name' => 'GRN Generated',
                'slug' => 'grn-generated',
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
            ],
            [
                'name' => 'Pending',
                'slug' => 'pending',
                'created_at' => $now,
                'updated_at' => $now
            ]
        ]);
    }
}
