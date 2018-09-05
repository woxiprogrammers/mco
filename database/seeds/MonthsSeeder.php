<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MonthsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();
        DB::table('months')->insert([
            [
                'name' => 'Jan',
                'slug' => 'january',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Feb',
                'slug' => 'February',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Mar',
                'slug' => 'march',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Apr',
                'slug' => 'april',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'May',
                'slug' => 'may',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Jun',
                'slug' => 'june',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Jul',
                'slug' => 'july',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Aug',
                'slug' => 'august',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Sep',
                'slug' => 'september',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Oct',
                'slug' => 'october',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Nov',
                'slug' => 'november',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Dec',
                'slug' => 'december',
                'created_at' => $now,
                'updated_at' => $now,
            ]
        ]);
    }
}
