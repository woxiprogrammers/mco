<?php

use Illuminate\Database\Seeder;

class NewEmployeeTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = \Carbon\Carbon::now();
        \Illuminate\Support\Facades\DB::table('employee_types')->insert([
            [
                'name' => 'Partner',
                'slug' => 'partner',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Contractor Labour',
                'slug' => 'contractor-labour',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
