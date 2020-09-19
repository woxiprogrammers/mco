<?php

namespace App\Console\Commands;

use App\InventoryComponentTransfers;
use App\InventoryTransferTypes;
use Illuminate\Console\Command;

class ChallanCreate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:challan';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command creates challan to existing site transfers';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $outTransferType = InventoryTransferTypes::where('slug', 'Site')->where('type', 'OUT')->first();

        $inventoryComponentOutTransfers = $outTransferType->inventoryComponentTransfers;
        dd($inventoryComponentOutTransfers);
    }
}
