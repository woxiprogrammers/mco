<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OfficeProjectsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();
        DB::table('projects')->insert([
            [
                'name' => 'Office',
                'client_id' => \App\Client::where('company','ilike','Manisha Construction')->pluck('id')->first(),
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        ]);
    }
}
