<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ModuleTableSubmoduleOneSeeder extends Seeder
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
                'name' => 'Master Peticash Account',
                'slug' => 'master-peticash-account',
                'module_id' => DB::table('modules')->where('slug','peticash')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Sitewise Peticash Account',
                'slug' => 'sitewise-peticash-account',
                'module_id' => DB::table('modules')->where('slug','peticash')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Peticash Management',
                'slug' => 'peticash-management',
                'module_id' => DB::table('modules')->where('slug','peticash')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ]
        ]);
    }
}


