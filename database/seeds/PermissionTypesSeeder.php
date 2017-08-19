<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PermissionTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();
        DB::table('permission_types')->insert([
            [
                'name' => 'Create',
                'slug'=> 'create',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name'=>'View',
                'slug'=>'view',
                'created_at' => $now,
                'updated_at' => $now

            ],
            [
                'name'=>'Edit',
                'slug'=>'edit',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name'=>'Approve',
                'slug'=>'approve',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name'=>'Remove',
                'slug'=>'remove',
                'created_at' => $now,
                'updated_at' => $now
            ],


        ]);
    }
}
