<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SubcontractorSubModuleSeeder extends Seeder
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
                'name' => 'Subcontractor Structure',
                'slug' => 'subcontractor-structure',
                'module_id' => DB::table('modules')->where('slug','subcontractor')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Subcontractor Billing',
                'slug' => 'subcontractor-billing',
                'module_id' =>  DB::table('modules')->where('slug','subcontractor')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
        ]);
    }
}
