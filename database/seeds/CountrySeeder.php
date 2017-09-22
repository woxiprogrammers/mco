<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();
        DB::table('countries')->insert([
            [
                'name' => 'India',
                'slug' => 'india',
                'created_at' => $now,
                'updated_at' => $now
            ]
        ]);
    }
}