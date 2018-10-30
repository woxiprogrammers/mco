<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class YearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();
        DB::table('years')->insert([
            [
                'name' => '16',
                'slug' => '2016',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => '17',
                'slug' => '2017',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => '18',
                'slug' => '2018',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => '19',
                'slug' => '2019',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => '20',
                'slug' => '2020',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => '21',
                'slug' => '2021',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => '22',
                'slug' => '2022',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => '23',
                'slug' => '2023',
                'created_at' => $now,
                'updated_at' => $now,
            ]
        ]);
    }
}
