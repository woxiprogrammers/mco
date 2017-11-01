<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class NewPaymentTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();
        DB::table('payment_types')->where('slug','individual-cash')->delete();
        DB::table('payment_types')->insert([
            [
                'name' => 'Other',
                'slug' => 'other',
                'created_at' => $now,
                'updated_at' => $now
            ],
        ]);
    }
}
