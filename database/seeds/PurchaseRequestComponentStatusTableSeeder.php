<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PurchaseRequestComponentStatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();
        DB::table('purchase_request_component_statuses')->insert([
            [
                'name' => 'Pending',
                'slug' => 'pending',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Removed',
                'slug' => 'removed',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Manager Approved',
                'slug' => 'manager-approved',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Manager Disapproved',
                'slug' => 'manager-disapproved',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Admin Approved',
                'slug' => 'admin-approved',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Admin Disapproved',
                'slug' => 'admin-disapproved',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'In indent',
                'slug' => 'in-indent',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'P.R.Assigned',
                'slug' => 'p-r-assigned',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'P.R. Manager Approved',
                'slug' => 'p-r-manager-approved',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'P.R. Manager Disapproved',
                'slug' => 'p-r-manager-disapproved',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'P.R. Admin Approved',
                'slug' => 'p-r-admin-approved',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'P.R. Admin Disapproved',
                'slug' => 'p-r-admin-disapproved',
                'created_at' => $now,
                'updated_at' => $now
            ]
        ]);
    }
}
