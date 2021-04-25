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

    protected $signature = 'custom:rent-calculate {--year=} {--month=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This is the command to calculate rent for a montn. This can be calculated by either providing a year and month or by default it takes current year and month
        1. Command to calculate asset rent for all project sites for specified month is -
            php artisan custom:asset-rent-calculate --year=2018 --month=01
            Make sure year is 4 digit valid year and valid month 
        2. To calculate for current month for all project sites - 
            php artisan custom:asset-rent-calculate
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
            $this->info('Inside rent calculation');
            $error = false;
            $now = Carbon::now();
            if ($this->option('year') && $this->option('month')) {
                $this->info('Inside year and month check');
                // Year validation
                $year = $this->option('year');
                $requestMonth = $this->option('month');
                if (!in_array($year, [2016,2017,2018, 2019, 2020, 2021, 2022, 2023,2024,2025,2026]) || $year > $now->format('Y')) {
                    $this->info('Invalid year');
                    $error = true;
                    goto completeProcessing;
                }

                // Month validation and sverification
                if ($requestMonth === 'all') {
                    $month = ["01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12"];
                    if ($year === $now->format('Y')) {
                        $currentMonth = $now->format('m');
                        $month = array_filter($month, function ($m) use ($currentMonth) {
                            return $m <= $currentMonth;
                        });
                    }
                } else if (in_array($requestMonth, ["01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12"])) {
                    $month = [$requestMonth];
                } else {
                    $this->info('Invalid month');
                    $error = true;
                    goto completeProcessing;
                }
            } else {
                $year = $now->format('Y');
                $month = [$now->format('m')];
            }
            foreach ($month as $thisMonth) {
                $firstDayOfTheMonth = Carbon::create($year, $thisMonth, 1, 0, 0, 0)->startOfMonth();
                $lastDayOfTheMonth = Carbon::create($year, $thisMonth, 1, 0, 0, 0)->endOfMonth();
                if (!$error) {
                    $thisMonth = $firstDayOfTheMonth->format('m');
                    $thisYear = $firstDayOfTheMonth->format('Y');
                    $projectSites = ProjectSite::all();
                    $this->info('Rent calculation for year - ' . $thisYear . ' and month - ' . $thisMonth);
                    foreach ($projectSites as $projectSite) {
                        $this->info('Calculating for project site - ' . $projectSite['name']);
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
                        $this->info('Calculation done for project site - ' . $projectSite['name']);
                    }
                }
                $this->info('Calculations completed for month - ' . $thisMonth . ' and year - ' . $thisYear);
            }

            completeProcessing:
            if ($error) {
                $this->info('Error while calculating. Please check logs');
            } else {
                $this->info('Rental report completed for month - ' . $month . ' and year - ' . $year);
            }
        } catch (Exception $e) {
            $this->info('Server Exception');
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
