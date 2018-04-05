<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SubcontractorModuleSeeder extends Seeder
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
                'name' => 'Subcontractor',
                'slug' => 'subcontractor',
                'created_at' => $now,
                'updated_at' => $now
            ]
        ]);
    }
}
