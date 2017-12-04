<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class EmployeeImageTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();
        DB::table('employee_image_types')->insert([
            [
                'name' => 'Profile',
                'slug' => 'profile',
                'created_at' => $now,
                'updated_at' => $now
            ],
        ]);
    }
}
