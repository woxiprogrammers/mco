<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Module;

class AwarenessDPRPurchaseSubmoduleSeeder extends Seeder
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
                'name' => 'Manage General Awareness',
                'slug' => 'manage-general-awareness',
                'module_id' => Module::where('slug','general-awareness')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Manage DPR',
                'slug' => 'manage-dpr',
                'module_id' => Module::where('slug','dpr')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Salary Request Handler',
                'slug' => 'salary-request-handler',
                'module_id' => Module::where('slug','peticash')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Purchase Bill Entry',
                'slug' => 'purchase-bill-entry',
                'module_id' => Module::where('slug','purchase')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ]

        ]);
    }
}
