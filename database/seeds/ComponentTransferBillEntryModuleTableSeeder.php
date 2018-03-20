<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ComponentTransferBillEntryModuleTableSeeder extends Seeder
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
                'name' => 'Component Transfer Bill Entry',
                'slug' => 'component-transfer-bill-entry',
                'module_id' => DB::table('modules')->where('slug','inventory')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
        ]);
    }
}
