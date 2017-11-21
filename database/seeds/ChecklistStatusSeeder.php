<?php

use Illuminate\Database\Seeder;

class ChecklistStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = \Carbon\Carbon::now();
        \Illuminate\Support\Facades\DB::table('checklist_statuses')->insert([
            [
                'name' => 'Assigned',
                'slug' => 'assigned',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'In Progress',
                'slug' => 'in-progress',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Review',
                'slug' => 'review',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Recheck',
                'slug' => 'recheck',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Completed',
                'slug' => 'completed',
                'created_at' => $now,
                'updated_at' => $now
            ],

        ]);
    }
}
