<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AddPeticashSalaryDeleteInPermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();
        DB::table('permissions')->insert([
            [
                'name' => 'remove-salary-request-handler',
                'module_id' => DB::table('modules')->where('slug','salary-request-handler')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','remove')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ]
        ]);
    }
}
