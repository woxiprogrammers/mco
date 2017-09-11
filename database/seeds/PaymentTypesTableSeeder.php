<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PaymentTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();
        DB::table('payment_types')->insert([
            [
               'name' => 'Cash',
               'slug' => 'cash',
               'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Individual Cash',
                'slug' => 'individual-cash',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Peticash',
                'slug' => 'peticash',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Cheque',
                'slug' => 'cheque',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'NEFT',
                'slug' => 'neft',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'RTGS',
                'slug' => 'rtgs',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Internet Banking',
                'slug' => 'internet-banking',
                'created_at' => $now,
                'updated_at' => $now
            ],

        ]);
    }
}
