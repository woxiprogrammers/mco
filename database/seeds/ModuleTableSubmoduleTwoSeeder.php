<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ModuleTableSubmoduleTwoSeeder extends Seeder
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
                'name' => 'Checklist Category',
                'slug' => 'checklist-category',
                'module_id' => DB::table('modules')->where('slug','checklist')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Checklist Structure',
                'slug' => 'checklist-structure',
                'module_id' => DB::table('modules')->where('slug','checklist')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Checklist Structure Site Assignment',
                'slug' => 'checklist-structure-site-assignment',
                'module_id' => DB::table('modules')->where('slug','checklist')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Checklist User Assignment',
                'slug' => 'checklist-user-assignment',
                'module_id' => DB::table('modules')->where('slug','checklist')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Checklist Management',
                'slug' => 'checklist-management',
                'module_id' => DB::table('modules')->where('slug','checklist')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Checklist Recheck',
                'slug' => 'checklist-recheck',
                'module_id' => DB::table('modules')->where('slug','checklist')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Drawing Category',
                'slug' => 'drawing-category',
                'module_id' => DB::table('modules')->where('slug','drawing')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Add Drawing',
                'slug' => 'add-drawing',
                'module_id' => DB::table('modules')->where('slug','drawing')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Manage Drawing',
                'slug' => 'manage-drawing',
                'module_id' => DB::table('modules')->where('slug','drawing')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ]
        ]);
    }
}
