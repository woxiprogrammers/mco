<?php

use Illuminate\Database\Seeder;

class PeticashTransactionTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = \Carbon\Carbon::now();
        \Illuminate\Support\Facades\DB::table('peticash_transaction_types')->insert([
            [
                'name' => 'Client',
                'slug' => 'client',
                'type' => 'PURCHASE',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Hand',
                'slug' => 'hand',
                'type' => 'PURCHASE',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Office',
                'slug' => 'office',
                'type' => 'PURCHASE',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Supplier',
                'slug' => 'supplier',
                'type' => 'PURCHASE',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Salary',
                'slug' => 'salary',
                'type' => 'PAYMENT',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Advance',
                'slug' => 'advance',
                'type' => 'PAYMENT',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
