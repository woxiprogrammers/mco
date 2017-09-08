<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InventoryComponentTransfersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();
        DB::table('inventory_component_transfers')->insert([
            [
                'inventory_component_id' => DB::table('inventory_components')->where('name','Cement Ppc')->pluck('id')->first(),
                'transfer_type_id' => DB::table('inventory_transfer_types')->where('slug','client')->where('type','IN')->pluck('id')->first(),
                'quantity' => 50,
                'unit_id' => DB::table('units')->where('slug','nos')->pluck('id')->first(),
                'source_name' => 'Dwarkadhish',
                'bill_number' => '121',
                'bill_amount' => 100,
                'vehicle_number' => 'MH12 LM 1659',
                'in_time' => '2017-09-06 06:00:00',
                'out_time' => '2017-09-06 18:00:00',
                'payment_type_id' => 1,
                'date' => $now->subDays(6) ,
                'next_maintenance_hour' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'inventory_component_id' => DB::table('inventory_components')->where('name','Cement Ppc')->pluck('id')->first(),
                'transfer_type_id' => DB::table('inventory_transfer_types')->where('slug','client')->where('type','OUT')->pluck('id')->first(),
                'quantity' => 30,
                'unit_id' => DB::table('units')->where('slug','nos')->pluck('id')->first(),
                'source_name' => 'Dwarkadhish',
                'bill_number' => null,
                'bill_amount' => null,
                'vehicle_number' => null,
                'in_time' => null,
                'out_time' => null,
                'payment_type_id' => 1,
                'date' => $now->subDays(3) ,
                'next_maintenance_hour' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'inventory_component_id' => DB::table('inventory_components')->where('name','Tiles Cutter')->pluck('id')->first(),
                'transfer_type_id' => DB::table('inventory_transfer_types')->where('slug','maintenance')->where('type','IN')->pluck('id')->first(),
                'quantity' => 3,
                'unit_id' => DB::table('units')->where('slug','nos')->pluck('id')->first(),
                'source_name' => null,
                'bill_number' => null,
                'bill_amount' => null,
                'vehicle_number' => null,
                'in_time' => null,
                'out_time' => null,
                'payment_type_id' => null,
                'date' => $now->subDays(6) ,
                'next_maintenance_hour' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'inventory_component_id' => DB::table('inventory_components')->where('name','Tiles Cutter')->pluck('id')->first(),
                'transfer_type_id' => DB::table('inventory_transfer_types')->where('slug','maintenance')->where('type','OUT')->pluck('id')->first(),
                'quantity' => 1,
                'unit_id' => DB::table('units')->where('slug','nos')->pluck('id')->first(),
                'source_name' => null,
                'bill_number' => '122',
                'bill_amount' => 100,
                'vehicle_number' => null,
                'in_time' => null,
                'out_time' => null,
                'payment_type_id' => 1,
                'date' => $now->subDays(3) ,
                'next_maintenance_hour' => 120,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'inventory_component_id' => DB::table('inventory_components')->where('name','Wood Cutter')->pluck('id')->first(),
                'transfer_type_id' => DB::table('inventory_transfer_types')->where('slug','maintenance')->where('type','IN')->pluck('id')->first(),
                'quantity' => 1,
                'unit_id' => DB::table('units')->where('slug','nos')->pluck('id')->first(),
                'source_name' => null,
                'bill_number' => null,
                'bill_amount' => null,
                'vehicle_number' => null,
                'in_time' => null,
                'out_time' => null,
                'payment_type_id' => null,
                'date' => $now->subDays(6) ,
                'next_maintenance_hour' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'inventory_component_id' => DB::table('inventory_components')->where('name','Wood Cutter')->pluck('id')->first(),
                'transfer_type_id' => DB::table('inventory_transfer_types')->where('slug','maintenance')->where('type','OUT')->pluck('id')->first(),
                'quantity' => 1,
                'unit_id' => DB::table('units')->where('slug','nos')->pluck('id')->first(),
                'source_name' => null,
                'bill_number' => '190',
                'bill_amount' => 1000,
                'vehicle_number' => null,
                'in_time' => null,
                'out_time' => null,
                'payment_type_id' => 1,
                'date' => $now->subDays(3) ,
                'next_maintenance_hour' => 120,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
