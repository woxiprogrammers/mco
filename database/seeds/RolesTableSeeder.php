<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();
        DB::table('roles')->insert([
            [
                'name' => 'Admin',
                'slug' => 'admin',
                'type' => 'active',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Super Admin',
                'slug' => 'superadmin',
                'type' => 'active',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Billing Manager',
                'slug' => 'billing-manager',
                'type' => 'active',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Client',
                'slug' => 'client',
                'type' => 'non-active',
                'created_at' => $now,
                'updated_at' => $now
            ]
        ]);
    }
}
