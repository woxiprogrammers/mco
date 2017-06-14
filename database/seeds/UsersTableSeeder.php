<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();
        DB::table('users')->insert([
            [
                'first_name' => 'Admin',
                'last_name' => '',
                'email' => 'admin@gmail.com',
                'mobile' => '1111111111',
                'password' => bcrypt('admin'),
                'dob' => '',
                'gender' => '',
                'is_active' => 'true',
                'role_id' => '1',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'first_name' => 'Super Admin',
                'last_name' => '',
                'email' => 'superadmin@gmail.com',
                'mobile' => '2222222222',
                'password' => bcrypt('superadmin'),
                'dob' => '',
                'gender' => '',
                'is_active' => 'true',
                'role_id' => '2',
                'created_at' => $now,
                'updated_at' => $now]
        ]);
    }
}
