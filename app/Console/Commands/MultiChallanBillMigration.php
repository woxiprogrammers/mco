<?php

namespace App\Console\Commands;

use App\SiteTransferBill;
use App\SiteTransferBillChallan;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class MultiChallanBillMigration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:challan-bill-as-multi';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command migrates existing single challan bill to multi challan bill';

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
        $siteTransfers = SiteTransferBill::whereNotNull('inventory_transfer_challan_id')->get();
        foreach ($siteTransfers as $siteTransfer) {
            $siteTransferBillChallan = SiteTransferBillChallan::where('site_transfer_bill_id', $siteTransfer->id)->where('inventory_transfer_challan_id', $siteTransfer->inventory_transfer_challan_id)->first();
            if (!$siteTransferBillChallan) {
                SiteTransferBillChallan::create([
                    'site_transfer_bill_id'    => $siteTransfer->id,
                    'inventory_transfer_challan_id' => $siteTransfer->inventory_transfer_challan_id
                ]);
            }
        }
    }
}
