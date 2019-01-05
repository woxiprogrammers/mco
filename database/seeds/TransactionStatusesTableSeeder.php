<?php

use Illuminate\Database\Seeder;

class TransactionStatusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = \Carbon\Carbon::now();
        \Illuminate\Support\Facades\DB::table('transaction_statuses')->insert([
            [
                'name' => 'Approved',
                'slug' => 'approved',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Cancelled',
                'slug' => 'cancelled',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Deleted',
                'slug' => 'deleted',
                'created_at' => $now,
                'updated_at' => $now
            ],
        ]);
    }
}
