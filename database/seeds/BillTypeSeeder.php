<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class BillTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();
        DB::table('bill_types')->insert([
            [
                'name' => 'R.A',
                'slug' => 'ra',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'EX',
                'slug' => 'ex',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'DEP',
                'slug' => 'dep',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'MAT',
                'slug' => 'mat',
                'created_at' => $now,
                'updated_at' => $now
            ]
        ]);
    }
}
