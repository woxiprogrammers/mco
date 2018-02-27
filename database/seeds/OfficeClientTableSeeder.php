<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OfficeClientTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();
        DB::table('clients')->insert([
            [
                'company' => 'Manisha Construction',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        ]);
    }
}
