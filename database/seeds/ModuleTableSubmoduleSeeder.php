<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ModuleTableSubmoduleSeeder extends Seeder
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
                'name' => 'Category',
                'slug' => 'category',
                'module_id' => 1,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Product',
                'slug' => 'product',
                'module_id' => 1,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Billing',
                'slug' => 'billing',
                'module_id' => 3,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Billing Transaction',
                'slug' => 'billing-transaction',
                'module_id' => 3,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Purchase Request',
                'slug' => 'purchase-request',
                'module_id' => 4,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Material Request',
                'slug' => 'material-request',
                'module_id' => 4,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Vendor Assignment',
                'slug' => 'vendor-assignment',
                'module_id' => 4,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Purchase Order',
                'slug' => 'purchase-order',
                'module_id' => 4,
                'created_at' => $now,
                'updated_at' => $now
            ],
        ]);
    }
}
