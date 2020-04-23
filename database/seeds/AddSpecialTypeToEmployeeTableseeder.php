<?php

use Illuminate\Database\Seeder;

class AddSpecialTypeToEmployeeTableseeder extends Seeder
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
                'name' => 'Special Type',
                'slug' => 'delete-employee-map-salary',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
