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
                'module_id' => DB::table('modules')->where('slug','structure')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Material',
                'slug' => 'material',
                'module_id' => DB::table('modules')->where('slug','structure')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Profit Margin',
                'slug' => 'profit-margin',
                'module_id' => DB::table('modules')->where('slug','structure')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],

            [
                'name' => 'Units',
                'slug' => 'units',
                'module_id' => DB::table('modules')->where('slug','structure')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],

            [
                'name' => 'Summary',
                'slug' => 'summary',
                'module_id' => DB::table('modules')->where('slug','structure')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],

            [
                'name' => 'Tax',
                'slug' => 'tax',
                'module_id' => DB::table('modules')->where('slug','structure')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],

            [
                'name' => 'Product',
                'slug' => 'product',
                'module_id' => DB::table('modules')->where('slug','structure')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],

            [
                'name' => 'Manage Extra Items',
                'slug' => 'manage-extra-items',
                'module_id' => DB::table('modules')->where('slug','structure')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],

            [
                'name' => 'Manage User',
                'slug' => 'manage-user',
                'module_id' => DB::table('modules')->where('slug','users')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],

            [
                'name' => 'Manage Client',
                'slug' => 'manage-client',
                'module_id' => DB::table('modules')->where('slug','clients')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],

            [
                'name' => 'Manage Sites',
                'slug' => 'manage-sites',
                'module_id' => DB::table('modules')->where('slug','clients')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],

            [
                'name' => 'Quotation',
                'slug' => 'quotation',
                'module_id' => DB::table('modules')->where('slug','quotation')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now

            ],

            [
                'name' => 'Product Management After Quotation Approve',
                'slug' => 'product-management-after-quotation-approve',
                'module_id' => DB::table('modules')->where('slug','quotation')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now

            ],

            [
                'name' => 'Extra Item Calculation In Quotation',
                'slug' => 'extra-item-calculation-in-quotation',
                'module_id' => DB::table('modules')->where('slug','quotation')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now

            ],


            [
                'name' => 'Billing',
                'slug' => 'billing',
                'module_id' =>  DB::table('modules')->where('slug','bill')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Billing Transaction',
                'slug' => 'billing-transaction',
                'module_id' =>  DB::table('modules')->where('slug','bill')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],



            [
                'name' => 'Purchase Request',
                'slug' => 'purchase-request',
                'module_id' => DB::table('modules')->where('slug','purchase')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Material Request',
                'slug' => 'material-request',
                'module_id' => DB::table('modules')->where('slug','purchase')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Vendor Assignment',
                'slug' => 'vendor-assignment',
                'module_id' => DB::table('modules')->where('slug','purchase')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Purchase Order',
                'slug' => 'purchase-order',
                'module_id' => DB::table('modules')->where('slug','purchase')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],

            [
                'name' => 'Purchase Bill',
                'slug' => 'purchase-bill',
                'module_id' => DB::table('modules')->where('slug','purchase')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],

            [
                'name' => 'Purchase History',
                'slug' => 'purchase-history',
                'module_id' => DB::table('modules')->where('slug','purchase')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],

            [
                'name' => 'Manage Amendment',
                'slug' => 'manage-amendment',
                'module_id' => DB::table('modules')->where('slug','purchase')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],


            [
                'name' => 'Inventory IN/OUT/TRANSFER',
                'slug' => 'inventory-in-out-transfer',
                'module_id' => DB::table('modules')->where('slug','inventory')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],

            [
                'name' => 'Asset Reading',
                'slug' => 'asset-reading',
                'module_id' => DB::table('modules')->where('slug','inventory')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],

            [
                'name' => 'Asset Maintainance',
                'slug' => 'asset-maintainance',
                'module_id' => DB::table('modules')->where('slug','inventory')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],

            [
                'name' => 'Asset Management',
                'slug' => 'asset-management',
                'module_id' => DB::table('modules')->where('slug','inventory')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],

            [
                'name' => 'Inventory History',
                'slug' => 'inventory-history',
                'module_id' => DB::table('modules')->where('slug','inventory')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ]

        ]);
    }
}
