<?php

namespace App\Console\Commands;

use App\SiteTransferBill;
use App\SiteTransferBillChallan;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SiteTranferChallanBill extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transfer:challan-bill';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command generates/assigns challan to existing site transfer bill';

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
        $siteTransfers = SiteTransferBill::whereNotNull('inventory_component_transfer_id')->get();
        foreach ($siteTransfers as $siteTransfer) {
            Log::info($siteTransfer . ' - ' . $siteTransfer->inventoryComponentTransfer->inventory_transfer_challan_id);
            $siteTransferChallanBill = SiteTransferBillChallan::where('site_transfer_bill_id', $siteTransfer['id'])->where('inventory_transfer_challan_id', $siteTransfer->inventoryComponentTransfer->inventory_transfer_challan_id)->first();
            if (!$siteTransferChallanBill) {
                SiteTransferBillChallan::create([
                    'site_transfer_bill_id' => $siteTransfer['id'],
                    'inventory_transfer_challan_id' => $siteTransfer->inventoryComponentTransfer->inventory_transfer_challan_id
                ]);
            }
        }
    }
}
