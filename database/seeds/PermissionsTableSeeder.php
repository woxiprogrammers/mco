<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();
        DB::table('permissions')->insert([
            [
                'name' => 'create-quotation',
                'slug' => 'create-quotation',
                'module_id' => 1,
                'is_mobile' => false,
                'is_web' => true,
                'type' => 'CREATE',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'edit-quotation',
                'slug' => 'edit-quotation',
                'module_id' => 1,
                'is_mobile' => false,
                'is_web' => true,
                'type' => 'EDIT',
                'created_at' => $now,
                'updated_at' => $now],
            [
                'name' => 'view-quotation',
                'slug' => 'view-quotation',
                'module_id' => 1,
                'is_mobile' => false,
                'is_web' => true,
                'type' => 'VIEW',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'approve-disapprove-quotation',
                'slug' => 'approve-disapprove-quotation',
                'module_id' => 1,
                'is_mobile' => false,
                'is_web' => true,
                'type' => 'APPROVE/DISAPPROVE',
                'created_at' => $now,
                'updated_at' => $now
            ]
        ]);
    }
}
