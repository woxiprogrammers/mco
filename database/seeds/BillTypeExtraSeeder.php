<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class BillTypeExtraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();
        DB::table('bill_types')->insert([
            [
                'name' => 'R.A-A',
                'slug' => 'raa',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'R.A-B',
                'slug' => 'rab',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'R.A-C',
                'slug' => 'rac',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'R.A-D',
                'slug' => 'rad',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'R.A-E',
                'slug' => 'rae',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'R.A-F',
                'slug' => 'raf',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'R.A-G',
                'slug' => 'rag',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'R.A-H',
                'slug' => 'rah',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'R.A-I',
                'slug' => 'rai',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'R.A-J',
                'slug' => 'raj',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'EX-A',
                'slug' => 'exa',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'EX-B',
                'slug' => 'exb',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'EX-C',
                'slug' => 'exc',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'EX-D',
                'slug' => 'exd',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'EX-E',
                'slug' => 'exe',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'EX-F',
                'slug' => 'exf',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'EX-G',
                'slug' => 'exg',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'EX-H',
                'slug' => 'exh',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'EX-I',
                'slug' => 'exi',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'EX-J',
                'slug' => 'exj',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'DEP-A',
                'slug' => 'depa',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'DEP-A',
                'slug' => 'depa',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'DEP-B',
                'slug' => 'depb',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'DEP-C',
                'slug' => 'depc',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'DEP-D',
                'slug' => 'depd',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'DEP-E',
                'slug' => 'depe',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'DEP-F',
                'slug' => 'depf',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'DEP-G',
                'slug' => 'depg',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'DEP-H',
                'slug' => 'deph',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'DEP-I',
                'slug' => 'depi',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'DEP-J',
                'slug' => 'depj',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'MAT-A',
                'slug' => 'mata',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'MAT-B',
                'slug' => 'matb',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'MAT-C',
                'slug' => 'matc',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'MAT-D',
                'slug' => 'matd',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'MAT-E',
                'slug' => 'mate',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'MAT-F',
                'slug' => 'matf',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'MAT-G',
                'slug' => 'matg',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'MAT-H',
                'slug' => 'math',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'MAT-I',
                'slug' => 'mati',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'MAT-J',
                'slug' => 'matj',
                'created_at' => $now,
                'updated_at' => $now
            ]
        ]);
    }
}
