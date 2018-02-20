<?php

use Illuminate\Database\Seeder;

class SubcontractorStructureTypeTableSeeder extends Seeder
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
                'name' => 'SQFT',
                'slug' => 'sqft',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Amountwise',
                'slug' => 'amountwise',
                'created_at' => $now,
                'updated_at' => $now
            ]
        ]);
    }
}
