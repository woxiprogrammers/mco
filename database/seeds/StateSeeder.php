<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class StateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();
        DB::table('states')->insert([
            [
                'name' => 'Maharashtra',
                'slug' => 'maharashtra',
                'country_id' => '1',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Gujarat',
                'slug' => 'gujarat',
                'country_id' => '1',
                'created_at' => $now,
                'updated_at' => $now
            ]
        ]);
    }
}
