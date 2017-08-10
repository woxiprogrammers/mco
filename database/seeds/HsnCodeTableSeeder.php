<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
class HsnCodeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();
        DB::table('hsn_codes')->insert([
            [
                'code'=> '995411',
                'description' => 'Construction services of single dwelling or multi dewlling or multi-storied residential buildings.',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'code'=> '995412',
                'description' => 'Construction services of other residential buildings such as old age homes, homeless shelters, hostels, etc.',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'code'=> '995413',
                'description' => 'Construction services of industrial buildings such as buildings used for production activities (used for assembly line activities), workshops, storage buildings and other similar industrial buildings.',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'code'=> '995414',
                'description' => 'Construction services of commercial buildings such as office buildings, exhibition & marriage halls, malls, hotels, restaurants, airports, rail or road terminals, parking garages, petrol and service stations, theatres and other similar buildings.',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'code'=> '995415',
                'description' => 'Construction services of other non-residential buildings such as educational institutions, hospitals, clinics including vertinary clinics, religious establishments, courts, prisons, museums and other similar buildings.',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'code'=> '995416',
                'description' => 'Construction Services of other buildings n.e.c.',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'code'=> '995419',
                'description' => 'Services involving Repair, alterations, additions, replacements, renovation, maintenance or remodelling of the buildings covered above.',
                'created_at' => $now,
                'updated_at' => $now
            ]
        ]);
    }
}
