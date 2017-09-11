<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MaterialRequestComponentTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();
        DB::table('material_request_component_types')->insert([
            [
                'name' => 'Fuel',
                'slug' => 'fuel',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Quotation Material',
                'slug' => 'quotation-material',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Structure Material',
                'slug' => 'structure-material',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'New Material',
                'slug' => 'new-material',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'System Asset',
                'slug' => 'system-asset',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'New Asset',
                'slug' => 'new-asset',
                'created_at' => $now,
                'updated_at' => $now
            ]
        ]);
    }
}
