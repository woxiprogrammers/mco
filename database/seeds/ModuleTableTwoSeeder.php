<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ModuleTableTwoSeeder extends Seeder
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
                'name' => 'Checklist',
                'slug' => 'checklist',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Drawing',
                'slug' => 'drawing',
                'created_at' => $now,
                'updated_at' => $now
            ]
        ]);
    }
}
