<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionDescriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('permissions')->where('name','create-inventory-history')->update(['description' => 'Add Comment']);
        DB::table('permissions')->where('name','create-purchase-history')->update(['description' => 'Add Comment']);
    }
}
