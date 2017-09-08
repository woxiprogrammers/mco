<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InventoryComponentTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();
        DB::table('inventory_components')->insert([
            [
                'name' => DB::table('materials')->where('slug','cement-ppc')->pluck('name')->first(),
                'project_site_id' => DB::table('project_sites')->where('name','Rose Wood')->pluck('id')->first(),
                'is_material' => true,
                'reference_id' => DB::table('materials')->where('slug','cement-ppc')->pluck('id')->first(),
                'opening_stock' => '300',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Tiles Cutter',
                'project_site_id' => DB::table('project_sites')->where('name','Rose Wood')->pluck('id')->first(),
                'is_material' => false,
                'reference_id' => null,
                'opening_stock' => '1',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Wood Cutter',
                'project_site_id' => DB::table('project_sites')->where('name','Rose Wood')->pluck('id')->first(),
                'is_material' => false,
                'reference_id' => DB::table('assets')->where('name','Wood Cutter')->pluck('id')->first(),
                'opening_stock' => '1',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
