<?php

namespace App\Console\Commands;

use App\AssetRentMonthlyExpenses;
use App\Http\Controllers\Inventory\InventoryManageController;
use App\InventoryComponent;
use App\InventoryComponentTransfers;
use App\InventoryTransferChallan;
use App\InventoryTransferTypes;
use App\Month;
use App\ProjectSite;
use App\RentalInventoryComponent;
use App\RentalInventoryTransfer;
use App\RentBill;
use App\Unit;
use App\Year;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SaveRentPastTransaction extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'custom:save-past-transaction';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This is the command to save past inventory transaction in rental_inventory_transfer for all project sites other than head office. 
    Transactions after first head office in to the project site are saved';

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
        try {
            $this->info('Inside Job for saving past inventory transaction data');
            $intransferType = InventoryTransferTypes::where('slug', 'site')->where('type', 'IN')->first();
            $outtransferType = InventoryTransferTypes::where('slug', 'site')->where('type', 'OUT')->first();
            $headOffice = ProjectSite::where('name', env('OFFICE_PROJECT_SITE_NAME'))->first();
            // fetch all project sites other than headOffice
            $otherProjectSites = ProjectSite::where('id', '!=', $headOffice['id'])->get();
            // foreach projectSite save past transactions
            foreach ($otherProjectSites as $projectSite) {
                $firstSiteInDate = InventoryTransferChallan::where('project_site_out_id', $headOffice['id'])->where('project_site_in_id', $projectSite['id'])->orderBy('project_site_out_date', 'asc')->pluck('project_site_out_date')->first();
                if ($firstSiteInDate) {
                    $outChallanIds = InventoryTransferChallan::where('project_site_out_id', $projectSite['id'])->where('project_site_out_date', '>=', $firstSiteInDate)->pluck('id')->toArray();
                    $inventortComponentIds = InventoryComponent::where('project_site_id', $projectSite['id'])->where('is_material', false)->pluck('id');
                    $inventoryTransfersOut = InventoryComponentTransfers::whereIn('inventory_component_id', $inventortComponentIds)->where('transfer_type_id', $outtransferType['id'])->whereIn('inventory_transfer_challan_id', $outChallanIds)->get()->toArray();
                    foreach ($inventoryTransfersOut as $outTransfer) {
                        $this->createRentalTransfer($outTransfer, $outTransfer['created_at']);
                    }
                    $inChallanIds = InventoryTransferChallan::where('project_site_in_id', $projectSite['id'])->where('project_site_in_date', '>=', $firstSiteInDate)->pluck('id')->toArray();
                    $inventoryTransfersIn = InventoryComponentTransfers::whereIn('inventory_component_id', $inventortComponentIds)->where('transfer_type_id', $intransferType['id'])->whereIn('inventory_transfer_challan_id', $inChallanIds)->get()->toArray();
                    foreach ($inventoryTransfersIn as $inTransfer) {
                        $relatedOutTransferDate = InventoryComponentTransfers::where('id', $inTransfer['related_transfer_id'])->pluck('created_at')->first();
                        $this->createRentalTransfer($inTransfer, $relatedOutTransferDate);
                    }
                } else {
                    $this->info('Head office transaction for project site - ' . $projectSite['name'] . ' not found');
                }
            }
            $this->info('Saving past inventory transaction completed');
        } catch (Exception $e) {
            $this->info('Server Exception');
            dd($e->getMessage());
            $data = [
                'action' => 'Rent Calculation Cron',
                'params' => [],
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function createRentalTransfer($inventoryTransfer, $rentStartDate)
    {
        try {
            $rentalDataAlreadyExists = RentalInventoryTransfer::where('inventory_component_transfer_id', $inventoryTransfer['id'])->first();
            if (!$rentalDataAlreadyExists) {
                if ($inventoryTransfer['quantity']) {
                    $quantity = $inventoryTransfer['quantity'];
                } elseif ($inventoryTransfer['related_transfer_id']) {
                    $quantity = InventoryComponentTransfers::where('id', $inventoryTransfer['related_transfer_id'])->pluck('quantity')->first() ?? 0;
                } else {
                    $quantity = 0;
                }
                RentalInventoryTransfer::create([
                    'inventory_component_transfer_id'   => $inventoryTransfer['id'],
                    'quantity'                          => $quantity,
                    'rent_per_day'                      => $inventoryTransfer['rate_per_unit'] ?? 0,
                    'rent_start_date'                   => $rentStartDate
                ]);
            }
        } catch (Exception $e) {
            $this->info('Server Exception while creating rental inventory transfer');
            $data = [
                'action' => 'Create rental inventory transfer',
                'params' => [],
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }
}
