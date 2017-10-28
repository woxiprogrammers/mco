<?php

use Illuminate\Database\Seeder;

class AssetTypes extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = \Carbon\Carbon::now();
        \Illuminate\Support\Facades\DB::table('asset_types')->insert([
            [
                'name' => 'Fuel Dependent',
                'slug' => 'fuel_dependent',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Electricity Dependent',
                'slug' => 'electricity_dependent',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Fuel And Electricity Dependent',
                'slug' => 'fuel_and_electricity_dependent',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Other',
                'slug' => 'other',
                'created_at' => $now,
                'updated_at' => $now,
            ]
        ]);
    }
}
