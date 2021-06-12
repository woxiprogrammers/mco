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
use Carbon\CarbonPeriod;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CalculatePastRent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'custom:calculate-past-rent';

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
            $this->info('Inside Job for calculating past rent');
            $headOffice = ProjectSite::where('name', env('OFFICE_PROJECT_SITE_NAME'))->first();
            // fetch all project sites other than headOffice
            $otherProjectSites = ProjectSite::where('id', '!=', $headOffice['id'])->get();
            // foreach projectSite save past transactions
            foreach ($otherProjectSites as $projectSite) {
                $this->info('calculating for project site ' . $projectSite['name']);
                $firstSiteInDate = InventoryTransferChallan::where('project_site_out_id', $headOffice['id'])->where('project_site_in_id', $projectSite['id'])->orderBy('project_site_out_date', 'asc')->pluck('project_site_out_date')->first();
                if ($firstSiteInDate) {
                    $rentalApplicableDate = Carbon::parse($firstSiteInDate);
                    $months = [];
                    foreach (CarbonPeriod::create($rentalApplicableDate, '1 month', Carbon::today()) as $month) {
                        $months[$month->format('m-Y')] = $month->format('F Y');
                        $thisYear =  $month->format('Y');
                        $thisMonth = $month->format('m');
                        $firstDayOfTheMonth = Carbon::create($thisYear, $thisMonth, 1, 0, 0, 0)->startOfMonth();
                        $lastDayOfTheMonth = Carbon::create($thisYear, $thisMonth, 1, 0, 0, 0)->endOfMonth();
                        $lastMonthDate = Carbon::create($thisYear, $thisMonth, 1, 0, 0, 0)->subMonth();

                        $projectSiteRentTotal = $inventoryComponentIterator = 0;
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
                            $openingStockForThisMonth = RentalInventoryComponent::where('inventory_component_id', $inventoryComponent['id'])->where('month', $lastMonthDate->format('m'))->where('year', $lastMonthDate->format('Y'))->pluck('closing_stock')->first();
                            if (!$openingStockForThisMonth) {
                                $openingStockForThisMonth = 0;
                            }
                            $rentalDataAlreadyExists = RentalInventoryComponent::where('inventory_component_id', $inventoryComponent['id'])->where('month', $thisMonth)->where('year', $thisYear)->first();
                            if ($rentalDataAlreadyExists) {
                                $rentalDataAlreadyExists->update(['opening_stock' => $openingStockForThisMonth]);
                                $rentalData = $rentalDataAlreadyExists;
                            } else {
                                $rentalData = RentalInventoryComponent::create([
                                    'inventory_component_id'    => $inventoryComponent['id'],
                                    'month'                     => $thisMonth,
                                    'year'                      => $thisYear,
                                    'opening_stock'             => $openingStockForThisMonth,
                                    'closing_stock'             => $openingStockForThisMonth     // Intially closing stock will be same as opening stock but eventually will get updated once trasactions are calculated
                                ]);
                            }

                            $ratePerUnit = $inventoryComponent->asset->rent_per_day;
                            $noOfDays = $lastDayOfTheMonth->diffInDays($firstDayOfTheMonth) + 1;

                            // Opening stock total for a inventry component
                            $openingStockTotal = $noOfDays * $ratePerUnit * $openingStockForThisMonth;

                            $inventoryTraansfers = $rentalInventoryTransfers->where('inventory_component_id', $inventoryComponent['id'])->sortBy('rent_start_date');
                            foreach ($inventoryTraansfers as $inventoryTransfer) {
                                $noOfDays = $lastDayOfTheMonth->diffInDays($inventoryTransfer['rent_start_date']) + 1;
                                $quantity = ($inventoryTransfer['inventory_transfer_type'] === 'OUT') ? -1 * abs($inventoryTransfer['quantity']) : $inventoryTransfer['quantity'];
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
                } else {
                    $this->info('Head office transaction for project site - ' . $projectSite['name'] . ' not found');
                }
            }
            $this->info('Calculating past rent completed');
        } catch (Exception $e) {
            $this->info('Server Exception');
            dd($e->getMessage());
            $data = [
                'action' => 'Calculate past rent',
                'params' => [],
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }
}
