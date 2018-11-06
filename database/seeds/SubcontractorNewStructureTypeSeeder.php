<?php

use Illuminate\Database\Seeder;

class SubcontractorNewStructureTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = \Carbon\Carbon::now();
        \Illuminate\Support\Facades\DB::table('subcontractor_structure_types')->insert([
            [
                'name' => 'Itemwise',
                'slug' => 'itemwise',
                'created_at' => $now,
                'updated_at' => $now
            ]
        ]);
    }
}
