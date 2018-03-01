<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OfficeProjectSitesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();
        DB::table('project_sites')->insert([
            [
                'name' => 'Office-Kondhwa',
                'project_id' => \App\Project::where('name','ilike','Office')->pluck('id')->first(),
                'address' => 'SIDDHI TOWER ABOVE RUPEE BANK, KONDHWA, PUNE - 411048',
                'created_at' => $now,
                'updated_at' => $now,
            ]
        ]);
    }
}
