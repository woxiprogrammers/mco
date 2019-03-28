<?php

namespace App\Console\Commands;

use App\AssetRentMonthlyExpenses;
use App\InventoryComponent;
use App\InventoryComponentTransfers;
use App\InventoryTransferTypes;
use App\Month;
use App\ProjectSite;
use App\Year;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AssetRentCalculations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    //php artisan custom:asset-rent-calculate --year=2018  => Executes Case 1

    protected $signature = 'custom:asset-rent-calculate {--year=null}';

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
    public function handle(){
        try{
            $year = new Year();
            $month = new Month();
            $projectSite = new ProjectSite();
            $inventoryComponentTransfer = new InventoryComponentTransfers();
            $inventoryComponent = new InventoryComponent();
            $inventoryTransferType = new InventoryTransferTypes();
            $assetRentMonthlyExpense = new AssetRentMonthlyExpenses();
            if($this->option('year') != 'null'){
                $yearSlug = $this->option('year');
                $currentYear = date('Y');
                $thisYear = $year->where('slug',$yearSlug)->first();
                if($currentYear == $thisYear['slug']){
                    $months = $month->where('id','<',date('m'))->orderBy('id','asc')->get();
                }else{
                    $months = $month->orderBy('id','asc')->get();
                }

            }else{
                $datestring = date('Y-m-d').' first day of last month';
                $dt = date_create($datestring);
                $last_month_id = $dt->format('m');
                $months = $month->where('id',$last_month_id)->orderBy('id','asc')->get();
                $thisYear = $year->where('slug',date('Y', strtotime('last month')))->first();
            }
            $totalMonths = $month->orderBy('id','asc')->get();
            $allProjectSiteIds = $projectSite->pluck('id');
            $inTransferTypeIds = $inventoryTransferType->where('type','IN')->pluck('id')->toArray();
            $outTransferTypeIds = $inventoryTransferType->where('type','OUT')->pluck('id')->toArray();
            $data = array();
            foreach($allProjectSiteIds as $projectSiteId){
                $inventoryComponentData = $inventoryComponent->where('project_site_id',$projectSiteId)
                    ->where('is_material',false)
                    ->select('id','reference_id')->get();
                foreach ($inventoryComponentData as $thisInventoryComponent){
                    $assetId = $thisInventoryComponent['reference_id'];
                    foreach ($months as $thisMonth){
                        $alreadyExistAssetRentMonthlyExpense = $assetRentMonthlyExpense
                            ->where('year_id',$thisYear['id'])
                            ->where('project_site_id',$projectSiteId)
                            ->where('asset_id',$assetId)
                            ->first();
                        $firstDayOfThisMonth = date('Y-m-d H:i:s', mktime(0, 0, 0, $thisMonth['id'], 1, $thisYear['slug']));
                        $lastDayOfThisMonth = date('Y-m-t H:i:s', mktime(23, 59, 59, $thisMonth['id'], 1, $thisYear['slug']));
                        $lastMonthData = array();
                        $thisMonthAssetRentMonthlyExpenseData = array();
                        if($thisMonth['slug'] == 'january'){
                            $lastYearAssetRentMonthlyExpenseData = $assetRentMonthlyExpense->where('project_site_id',$projectSite['id'])
                                ->where('year_id',$thisYear['slug']-1)
                                ->where('asset_id',$inventoryComponent['asset_id'])
                                ->first();
                            if($lastYearAssetRentMonthlyExpenseData != null){
                                $noOfDaysInJanuaryMonth = cal_days_in_month(CAL_GREGORIAN, 1, $thisYear['slug']);
                                $lastYearDecemberMonthData = json_decode($lastYearAssetRentMonthlyExpenseData['december']);
                                $lastMonthData['rent_per_day_per_quantity'] = $lastYearDecemberMonthData->rent_per_day_per_quantity;
                                $lastMonthData['days_used'] = ($lastYearDecemberMonthData->carry_forward_quantity == 0) ? 0 : $noOfDaysInJanuaryMonth;
                                $lastMonthData['quantity_used'] = $lastYearDecemberMonthData->carry_forward_quantity;
                                $lastMonthData['rent_for_month'] = ($lastMonthData['rent_per_day_per_quantity'] * $lastMonthData['days_used'] * $lastMonthData['quantity_used']);
                                $lastMonthData['carry_forward_quantity'] = $lastYearDecemberMonthData->carry_forward_quantity;
                            }else{
                                $lastMonthData['rent_per_day_per_quantity'] = 0;
                                $lastMonthData['quantity_used'] = 0;
                                $lastMonthData['days_used'] = 0;
                                $lastMonthData['rent_for_month'] = 0;
                                $lastMonthData['carry_forward_quantity'] = 0;
                            }
                        }else{
                            if($alreadyExistAssetRentMonthlyExpense != null){
                                $lastMonthId = $thisMonth['id']-1;
                                $lastMonthName = $totalMonths->where('id',$lastMonthId)->pluck('slug')->first();
                                $noOfDaysInThisMonth = cal_days_in_month(CAL_GREGORIAN, $thisMonth['id'], $thisYear['slug']);
                                $lastMonthDataa = json_decode($alreadyExistAssetRentMonthlyExpense[$lastMonthName]);
                                $lastMonthData['rent_per_day_per_quantity'] = ($lastMonthDataa != null) ? $lastMonthDataa->rent_per_day_per_quantity : 0;
                                $lastMonthData['days_used'] = ($lastMonthDataa == null) ? 0 : $noOfDaysInThisMonth;
                                $lastMonthData['quantity_used'] = ($lastMonthDataa != null) ? $lastMonthDataa->carry_forward_quantity : 0;
                                $lastMonthData['rent_for_month'] = ($lastMonthData['rent_per_day_per_quantity'] * $lastMonthData['days_used'] * $lastMonthData['quantity_used']);
                                $lastMonthData['carry_forward_quantity'] = ($lastMonthDataa != null) ? $lastMonthDataa->carry_forward_quantity : 0;
                            }else{
                                $lastMonthData['rent_per_day_per_quantity'] = 0;
                                $lastMonthData['quantity_used'] = 0;
                                $lastMonthData['days_used'] = 0;
                                $lastMonthData['rent_for_month'] = 0;
                                $lastMonthData['carry_forward_quantity'] = 0;
                            }
                        }
                        $inventoryComponentTransfers = $inventoryComponentTransfer
                            ->where('inventory_component_id',$thisInventoryComponent['id'])
                            ->whereMonth('created_at', $thisMonth['id'])
                            ->whereYear('created_at', $thisYear['slug'])
                            ->orderBy('created_at','asc')
                            ->get();
                        $inventoryComponentTransferGroupByDateData = $inventoryComponentTransfers->sortBy('created_at')->groupBy(function($transactionsData) {
                            return Carbon::parse($transactionsData->created_at)->format('Y-m-d');
                        });
                        $thisMonthAssetRentMonthlyExpenseData['rent_per_day_per_quantity'] = $thisMonthAssetRentMonthlyExpenseData['quantity_used'] = 0;
                        $thisMonthAssetRentMonthlyExpenseData['days_used'] = 0;
                        $thisMonthAssetRentMonthlyExpenseData['rent_for_month'] = 0;
                        $thisMonthAssetRentMonthlyExpenseData['carry_forward_quantity'] = 0;

                        if(count($inventoryComponentTransferGroupByDateData) > 0){
                            $iterator = 0;
                            $carryForwardQuantity = $lastMonthData['carry_forward_quantity'];
                            $highestRentForMonth = $inventoryComponentTransfers->max('rate_per_unit');
                            $thisMonthAssetRentMonthlyExpenseData['rent_per_day_per_quantity'] = $highestRentForMonth;
                            $dates = $inventoryComponentTransferGroupByDateData->keys()->toArray();
                            foreach ($inventoryComponentTransferGroupByDateData as $date => $thisTransfer){
                                $parsedData = Carbon::parse($date);
                                if($iterator == 0 && (date('d-m-y',strtotime($firstDayOfThisMonth)) != date('d-m-y',strtotime($parsedData))) && $lastMonthData['carry_forward_quantity'] != 0){
                                    $noOfDays = ceil(abs(strtotime($firstDayOfThisMonth) - strtotime($parsedData))/86400);
                                    $thisMonthAssetRentMonthlyExpenseData['quantity_used'] += $carryForwardQuantity * $noOfDays;
                                    $thisMonthAssetRentMonthlyExpenseData['days_used'] += $noOfDays;
                                    $thisMonthAssetRentMonthlyExpenseData['carry_forward_quantity'] += $carryForwardQuantity;
                                }
                                if(($iterator+1) < count($dates)){
                                    $nextTransactionDate = Carbon::parse($dates[$iterator+1]);
                                    $noOfDays = ceil(abs(strtotime($nextTransactionDate) - strtotime($parsedData))/86400);
                                }else{
                                    if((date('d-m-y',strtotime($lastDayOfThisMonth)) != date('d-m-y',strtotime($parsedData)))){
                                        $noOfDays = ceil(abs(strtotime($lastDayOfThisMonth) - strtotime($parsedData))/86400);
                                    }else{
                                        $noOfDays = 1;
                                    }
                                }
                                $inQuantities = $thisTransfer->whereIn('transfer_type_id',$inTransferTypeIds)->sum('quantity');
                                $outQuantities = $thisTransfer->whereIn('transfer_type_id',$outTransferTypeIds)->sum('quantity');
                                $carryForwardQuantity = $carryForwardQuantity + $inQuantities - $outQuantities;
                                $thisMonthAssetRentMonthlyExpenseData['quantity_used'] += $carryForwardQuantity * $noOfDays;
                                $thisMonthAssetRentMonthlyExpenseData['days_used'] += $noOfDays;
                                $thisMonthAssetRentMonthlyExpenseData['carry_forward_quantity'] = $carryForwardQuantity;
                                $iterator++;
                            } // Transfer loop end
                        }else{
                            $thisMonthAssetRentMonthlyExpenseData = $lastMonthData;
                        }
                        $thisMonthAssetRentMonthlyExpenseData['rent_for_month'] += $thisMonthAssetRentMonthlyExpenseData['rent_per_day_per_quantity'] * $thisMonthAssetRentMonthlyExpenseData['quantity_used'];
                        $data[$projectSite['name']][$thisMonth['name']][$assetId] = $thisMonthAssetRentMonthlyExpenseData;
                        if($alreadyExistAssetRentMonthlyExpense != null){
                            $alreadyExistAssetRentMonthlyExpense->update([
                                $thisMonth['slug'] => json_encode($thisMonthAssetRentMonthlyExpenseData)
                            ]);
                        }else{
                            $assetRentMonthlyExpense->create([
                                'project_site_id' => $projectSiteId,
                                'year_id' => $thisYear['id'],
                                'asset_id' => $assetId,
                                $thisMonth['slug'] => json_encode($thisMonthAssetRentMonthlyExpenseData)
                            ]);
                        }
                    } //Month for loop end
                } // Asset Loop end
            } //Project Site Loop End

        }catch(\Exception $e){
            $data = [
                'action' => 'Asset Rent Calculations',
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            $this->error($e->getMessage());
        }
    }


    /*public function handle(){
        try{

            $year = new Year();
            $month = new Month();
            $inventoryComponentTransfer = new InventoryComponentTransfers();
            $inventoryComponent = new InventoryComponent();
            $inventoryTransferType = new InventoryTransferTypes();
            $assetRentMonthlyExpense = new AssetRentMonthlyExpenses();
            if($this->option('year') != 'null'){
                $yearSlug = $this->option('year');
                $currentYear = date('Y');
                $thisYear = $year->where('slug',$yearSlug)->first();
                if($currentYear == $thisYear['slug']){
                    $months = $month->where('id','<',date('m'))->orderBy('id','asc')->get();
                }else{
                    $months = $month->orderBy('id','asc')->get();
                }

            }else{
                $months = $month->orderBy('id','asc')->get();
                $thisYear = $year->where('slug',date('Y', strtotime('last month')))->first();
            }
            $projectSite = new ProjectSite();
            $projectSites = $projectSite->get();
            $data = array();
            $inTransferTypeIds = $inventoryTransferType->where('type','IN')->pluck('id')->toArray();
            $carryForwardQuantity = 0;

            foreach($projectSites as $projectSite) {
                $inventoryComponentData = $inventoryComponent->where('project_site_id',$projectSite['id'])
                    ->where('is_material',false)/*->where('id',953)
                    ->select('id as inventory_component_id','reference_id as asset_id')->get();
                foreach ($inventoryComponentData as $inventoryComponent){
                    foreach ($months as $thisMonth) {
                        $totalRentForMonth = $noofDaysUsedForMonth = $quantityUsedForMonth = 0;
                        $monthFirstDay = date('Y-m-d H:i:s', mktime(0, 0, 0, $thisMonth['id'], 1, $thisYear['slug']));
                        $monthLastDay = date('Y-m-t H:i:s', mktime(23, 59, 59, $thisMonth['id'], 1, $thisYear['slug']));
                        $inventoryComponentTransfers = $inventoryComponentTransfer
                            ->whereMonth('created_at', $thisMonth['id'])
                            ->whereYear('created_at', $thisYear['slug'])
                            ->where('inventory_component_id',$inventoryComponent['inventory_component_id'])
                            ->orderBy('created_at', 'asc')
                            ->get();
                        $assetRentMonthlyExpenseData = $assetRentMonthlyExpense->where('project_site_id',$projectSite['id'])
                            ->where('year_id',$thisYear['id'])
                            ->where('asset_id',$inventoryComponent['asset_id'])
                            ->first();
                        if(count($inventoryComponentTransfers) == 0){
                            if($thisMonth['slug'] == 'january'){
                                $lastYearAssetRentMonthlyExpenseData = $assetRentMonthlyExpense->where('project_site_id',$projectSite['id'])
                                    ->where('year_id',$thisYear['slug']-1)
                                    ->where('asset_id',$inventoryComponent['asset_id'])
                                    ->first();
                                if($lastYearAssetRentMonthlyExpenseData != null){
                                    $noOfDaysInMonth = cal_days_in_month(CAL_GREGORIAN, 1, $thisYear['slug']);
                                    $lastMonthData = json_decode($lastYearAssetRentMonthlyExpenseData['december']);
                                    $jsonData['rent_per_month'] = $lastMonthData->rent_per_month;
                                    $jsonData['days_used'] = $noOfDaysInMonth;
                                    $jsonData['quantity_used'] = $lastMonthData->carry_forward_quantity;
                                    $jsonData['rent'] = ($lastMonthData->rent_per_month * $noOfDaysInMonth * $lastMonthData->carry_forward_quantity);
                                    $jsonData['carry_forward_quantity'] = $lastMonthData->carry_forward_quantity;
                                    $assetRentMonthlyExpenseData->update([
                                        $thisMonth['slug'] => json_encode($jsonData)
                                    ]);
                                }else{
                                    $jsonData['rent_per_month'] = $jsonData['days_used'] = $jsonData['quantity_used'] = $jsonData['rent'] = $jsonData['carry_forward_quantity'] = 0;
                                    if($assetRentMonthlyExpenseData != null){
                                        $assetRentMonthlyExpenseData->update([
                                            $thisMonth['slug'] => json_encode($jsonData)
                                        ]);
                                    }else{
                                        $assetRentMonthlyExpense->create([
                                            'project_site_id' => $projectSite['id'],
                                            'year_id' => $thisYear['id'],
                                            'asset_id' => $inventoryComponent['asset_id'],
                                            $thisMonth['slug'] => json_encode($jsonData)
                                        ]);
                                    }

                                }

                            }else{
                                $lastMonthId = $thisMonth['id']-2;
                                $noOfDaysInMonth = cal_days_in_month(CAL_GREGORIAN, $thisMonth['id'], $thisYear['slug']);
                                $lastMonthData = json_decode($assetRentMonthlyExpenseData[$months[$lastMonthId]['slug']]);

                                $jsonData['rent_per_month'] = $lastMonthData->rent_per_month;
                                //$jsonData['days_used'] = $noOfDaysInMonth;
                                $jsonData['days_used'] = ($lastMonthData->carry_forward_quantity == 0) ? 0 : $noOfDaysInMonth;
                                $jsonData['quantity_used'] = $lastMonthData->carry_forward_quantity;
                                $jsonData['rent'] = ($lastMonthData->rent_per_month * $jsonData['days_used'] * $lastMonthData->carry_forward_quantity);
                                $jsonData['carry_forward_quantity'] = $lastMonthData->carry_forward_quantity;
                                $assetRentMonthlyExpenseData->update([
                                    $thisMonth['slug'] => json_encode($jsonData)
                                ]);
                            }

                        }else{
                            $inventoryComponentData = $inventoryComponentTransfers->groupBy(function ($inventoryComponentTransfer) {
                                return $inventoryComponentTransfer['inventory_component_id'];
                            });
                            foreach ($inventoryComponentData as $inventoryComponentId => $inventoryComponentTransferData) {
                                $assetId = $inventoryComponentTransferData[0]['reference_id'];
                                $highestRentForMonth = $inventoryComponentTransferData->max('rate_per_unit');
                                $count = count($inventoryComponentTransferData);
                                for ($iterator = 0; $iterator < $count; $iterator++) {
                                    if ((date('d-m-y',strtotime($inventoryComponentTransferData[$iterator]['created_at'])) != date('d-m-y',strtotime($monthFirstDay))) && $carryForwardQuantity != 0 && $iterator == 0) {
                                        $noOfDays = ceil(abs(strtotime($monthFirstDay) - strtotime($inventoryComponentTransferData[$iterator]['created_at']->subDay()))/86400);
                                        $noofDaysUsedForMonth += $noOfDays;
                                        if(in_array($inventoryComponentTransferData[$iterator]['transfer_type_id'],$inTransferTypeIds)){
                                            $carryForwardQuantity += $inventoryComponentTransferData[$iterator]['quantity'];
                                        }else{
                                            $carryForwardQuantity = $carryForwardQuantity - $inventoryComponentTransferData[$iterator]['quantity'];
                                        }
                                        $quantityUsedForMonth += $carryForwardQuantity;
                                        $totalRentForMonth += ($quantityUsedForMonth * $highestRentForMonth * $noOfDays);
                                    } elseif (($iterator + 1) < $count) {
                                        $noOfDays = ceil(abs(strtotime($inventoryComponentTransferData[$iterator]['created_at']) - strtotime($inventoryComponentTransferData[$iterator + 1]['created_at']->subDay()))/86400);
                                        $noofDaysUsedForMonth += $noOfDays;
                                        if(in_array($inventoryComponentTransferData[$iterator]['transfer_type_id'],$inTransferTypeIds)){
                                            $carryForwardQuantity += $inventoryComponentTransferData[$iterator]['quantity'];
                                        }else{
                                            $carryForwardQuantity = $carryForwardQuantity - $inventoryComponentTransferData[$iterator]['quantity'];
                                        }
                                        $quantityUsedForMonth += $carryForwardQuantity;
                                        $totalRentForMonth += ($quantityUsedForMonth * $highestRentForMonth * $noOfDays);
                                    } else {
                                        $noOfDays = ceil(abs(strtotime($inventoryComponentTransferData[$iterator]['created_at']) - strtotime($monthLastDay))/86400);
                                        $noofDaysUsedForMonth += ($noOfDays);
                                        if(in_array($inventoryComponentTransferData[$iterator]['transfer_type_id'],$inTransferTypeIds)){
                                            $carryForwardQuantity += $inventoryComponentTransferData[$iterator]['quantity'];
                                        }else{
                                            $carryForwardQuantity = $carryForwardQuantity - $inventoryComponentTransferData[$iterator]['quantity'];
                                        }
                                        $quantityUsedForMonth += $carryForwardQuantity;
                                        $totalRentForMonth += ($quantityUsedForMonth * $highestRentForMonth * $noOfDays);
                                    }
                                }
                                $data[$projectSite['name']][$thisMonth['name']][$inventoryComponentId]['rent_per_month'] = $highestRentForMonth;
                                $data[$projectSite['name']][$thisMonth['name']][$inventoryComponentId]['days_used'] = $noofDaysUsedForMonth;
                                $data[$projectSite['name']][$thisMonth['name']][$inventoryComponentId]['quantity_used'] = $quantityUsedForMonth;
                                $data[$projectSite['name']][$thisMonth['name']][$inventoryComponentId]['rent'] = $totalRentForMonth;
                                $data[$projectSite['name']][$thisMonth['name']][$inventoryComponentId]['carry_forward_quantity'] = $carryForwardQuantity;
                                $jsonData['rent_per_month'] = $highestRentForMonth;
                                $jsonData['days_used'] = $noofDaysUsedForMonth;
                                $jsonData['quantity_used'] = $quantityUsedForMonth;
                                $jsonData['rent'] = $totalRentForMonth;
                                $jsonData['carry_forward_quantity'] = $carryForwardQuantity;

                                if($assetRentMonthlyExpenseData != null){
                                    $assetRentMonthlyExpenseData->update([
                                        $thisMonth['slug'] => json_encode($jsonData)
                                    ]);

                                }else{
                                    $assetRentMonthlyExpense->create([
                                        'project_site_id' => $projectSite['id'],
                                        'year_id' => $thisYear['id'],
                                        'asset_id' => $inventoryComponent['asset_id'],
                                        $thisMonth['slug'] => json_encode($jsonData)
                                    ]);
                                }
                            }
                        }
                    }
                }

            }
        }catch(\Exception $e){
            $data = [
                'action' => 'Salary Distribution among sites',
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            $this->error($e->getMessage());
        }
    }*/
}
