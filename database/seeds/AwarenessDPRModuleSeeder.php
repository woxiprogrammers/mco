<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class AwarenessDPRModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();
        DB::table('modules')->insert([
            [
                'name' => 'General Awareness',
                'slug' => 'general-awareness',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'DPR',
                'slug' => 'dpr',
                'created_at' => $now,
                'updated_at' => $now
            ]
        ]);
    }
}
