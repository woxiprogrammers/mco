<?php

use App\InventoryComponentTransferStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class InventoryComponentTransferStatusesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();
        InventoryComponentTransferStatus::updateOrCreate(
            [
                'slug' => 'requested',
            ],
            [
                'name'          => 'Requested',
                'created_at'    => $now,
                'updated_at'    => $now
            ]
        );
        InventoryComponentTransferStatus::updateOrCreate(
            [
                'slug' => 'approved',
            ],
            [
                'name'          => 'Approved',
                'created_at'    => $now,
                'updated_at'    => $now
            ]
        );
        InventoryComponentTransferStatus::updateOrCreate(
            [
                'slug' => 'disapproved',
            ],
            [
                'name'          => 'Disapproved',
                'created_at'    => $now,
                'updated_at'    => $now
            ]
        );

        InventoryComponentTransferStatus::updateOrCreate(
            [
                'slug' => 'open',
            ],
            [
                'name'          => 'Open',
                'created_at'    => $now,
                'updated_at'    => $now
            ]
        );

        InventoryComponentTransferStatus::updateOrCreate(
            [
                'slug' => 'close',
            ],
            [
                'name'          => 'Close',
                'created_at'    => $now,
                'updated_at'    => $now
            ]
        );
    }
}
