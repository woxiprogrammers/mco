<?php

use Illuminate\Database\Seeder;

class EmployeeTypesTableSeeder extends Seeder
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
                'name' => 'Labour',
                'slug' => 'labour',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Staff',
                'slug' => 'staff',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
