<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class AssetsSeederTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();
        DB::table('assets')->insert([
            [
                'name' => 'Wood Cutter',
                'model_number' => 'ABCDXYZ1234',
                'expiry_date' => '2030-09-06 15:23:51',
                'price' => 100000,
                'is_fuel_dependent' => true,
                'created_at' => $now,
                'updated_at' => $now,
                'litre_per_unit' => 0.3,
            ]
        ]);
    }
}
