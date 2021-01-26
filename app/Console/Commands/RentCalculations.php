<?php

namespace App\Console\Commands;

use App\AssetRentMonthlyExpenses;
use App\Http\Controllers\Inventory\InventoryManageController;
use App\InventoryComponent;
use App\InventoryComponentTransfers;
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

class RentCalculations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    //php artisan custom:asset-rent-calculate --year=2018  => Executes Case 1

    protected $signature = 'custom:rent-calculate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'For Asset rent calculation. Two ways to run this command.
        1. Run the command to calculate rent of assets for all project sites for specified month Eg. php artisan custom:asset-rent-calculate --year=2018
        2. Through Scheduler that will run first day of every month where it will calculate the asset rent for all project sites of last month.
        ';

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
            $thisMonth = date('m');
            $thisYear = date('Y');
            $firstDayOfTheMonth = Carbon::now()->startOfMonth();
            $lastDayOfTheMonth = Carbon::now()->endOfMonth();
            $projectSites = ProjectSite::all();
            foreach ($projectSites as $projectSite) {
                $projectSiteRentTotal = $inventoryComponentIterator = 0;
                $unit = Unit::where('slug', 'nos')->select('id', 'name')->first();
                $controller = new InventoryManageController;
                $rentalInventoryTransfers = RentalInventoryTransfer::join('inventory_component_transfers', 'inventory_component_transfers.id', '=', 'rental_inventory_transfers.inventory_component_transfer_id')
                    ->join('inventory_transfer_types', 'inventory_transfer_types.id', '=', 'inventory_component_transfers.transfer_type_id')
                    ->join('inventory_components', 'inventory_components.id', '=', 'inventory_component_transfers.inventory_component_id')
                    ->where('project_site_id', $projectSite['id'])
                    ->whereBetween('rental_inventory_transfers.rent_start_date', [$firstDayOfTheMonth, $lastDayOfTheMonth])
                    ->select(
                        'rental_inventory_transfers.id',
                        'rental_inventory_transfers.inventory_component_transfer_id',
                        'rental_inventory_transfers.quantity',
                        'rental_inventory_transfers.rent_per_day',
                        'rental_inventory_transfers.rent_start_date',
                        'inventory_component_transfers.inventory_component_id',
                        'inventory_transfer_types.type as inventory_transfer_type'
                    )->get();

                $inventoryComponents = $projectSite->inventoryComponents->where('is_material', false);
                foreach ($inventoryComponents as $inventoryComponent) {
                    $inventoryComponentIterator++;
                    $transactionTotal = $transactionQuantity = 0;
                    $openingStockForThisMonth = RentalInventoryComponent::where('inventory_component_id', $inventoryComponent['id'])->where('month', $thisMonth - 1)->where('year', $thisYear - 1)->pluck('closing_stock')->first();
                    if (!$openingStockForThisMonth) {
                        $availableQuantity = $controller->checkInventoryAvailableQuantity(['inventoryComponentId'  => $inventoryComponent['id'], 'quantity' => 0, 'unitId' => $unit['id']]);
                        $openingStockForThisMonth = $availableQuantity['available_quantity'];
                    }
                    $rentalDataAlreadyExists = RentalInventoryComponent::where('inventory_component_id', $inventoryComponent['id'])->where('month', $thisMonth)->where('year', $thisYear)->first();
                    if ($rentalDataAlreadyExists) {
                        $rentalDataAlreadyExists->update(['opening_stock' => $openingStockForThisMonth]);
                        $rentalData = $rentalDataAlreadyExists;
                    } else {
                        $rentalData = RentalInventoryComponent::create([
                            'inventory_component_id'  => $inventoryComponent['id'],
                            'month' => $thisMonth,
                            'year'  => $thisYear,
                            'opening_stock' => $openingStockForThisMonth,
                            'closing_stock' => $openingStockForThisMonth     // Intially closing stock will be same as opening stock but eventually will get updated once trasactions are calculated
                        ]);
                    }

                    $ratePerUnit = $inventoryComponent->asset->rent_per_day;
                    $noOfDays = $lastDayOfTheMonth->diffInDays($firstDayOfTheMonth) + 1;

                    // Opening stock total for a inventry component
                    $openingStockTotal = $noOfDays * $ratePerUnit * $openingStockForThisMonth;

                    $inventoryTraansfers = $rentalInventoryTransfers->where('inventory_component_id', $inventoryComponent['id'])->sortBy('rent_start_date');
                    foreach ($inventoryTraansfers as $inventoryTransfer) {
                        $noOfDays = $lastDayOfTheMonth->diffInDays($inventoryTransfer['rent_start_date']) + 1;
                        $quantity = ($inventoryTransfer['inventory_transfer_type'] === 'IN') ? -1 * abs($inventoryTransfer['quantity']) : $inventoryTransfer['quantity'];
                        $rentPerDay = (float)$inventoryTransfer['rent_per_day'];
                        $total = $quantity * $rentPerDay * $noOfDays;

                        $transactionTotal += $total;
                        $transactionQuantity +=  $quantity;
                    }
                    // Closing stock row
                    $closingStock = $openingStockForThisMonth + $transactionQuantity;
                    $rentalData->update(['closing_stock'    => $closingStock]);
                    $projectSiteRentTotal += ($openingStockTotal  + $transactionTotal);
                }
                $rentBillRecord = RentBill::where('project_site_id', $projectSite['id'])->where('month', $thisMonth)->where('year', $thisYear)->first();
                if ($rentBillRecord) {
                    $rentBillRecord->update(['total' => $projectSiteRentTotal]);
                } else {
                    $rentBillRecord = RentBill::create([
                        'project_site_id'   => $projectSite['id'],
                        'month'             => $thisMonth,
                        'year'              => $thisYear,
                        'total'             => $projectSiteRentTotal
                    ]);
                }
            }
        } catch (Exception $e) {
            $data = [
                'action' => 'Rent Calculation Cron',
                'params' => [],
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }
}
