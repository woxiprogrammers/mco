<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ModuleTableSeeder extends Seeder
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
                'name' => 'Structure',
                'slug' => 'structure',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Quotation',
                'slug' => 'quotation',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Bill',
                'slug' => 'bill',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Purchase',
                'slug' => 'purchase',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Inventory',
                'slug' => 'inventory',
                'created_at' => $now,
                'updated_at' => $now
            ],

            [
                'name' =>'Users',
                'slug' => 'users',
                'created_at' => $now,
                'updated_at' => $now

            ]
        ]);
    }
}
