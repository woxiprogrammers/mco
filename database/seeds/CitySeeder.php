<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();
        DB::table('cities')->insert([
            [
                'name' => 'Pune',
                'slug' => 'pune',
                'state_id' => '1',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Mumbai',
                'slug' => 'mumbai',
                'state_id' => '1',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Surat',
                'slug' => 'surat',
                'state_id' => '2',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Ahemdabad',
                'slug' => 'ahemdabad',
                'state_id' => '2',
                'created_at' => $now,
                'updated_at' => $now
            ]
        ]);
    }
}
