<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class QuotationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();
        DB::table('quotations')->insert([
            'project_site_id' => 4,
            'quotation_status_id' => 2,
            'remark' => 'This is quotation seeder',
            'created_at' => $now,
            'updated_at' => $now
        ]);
    }
}
