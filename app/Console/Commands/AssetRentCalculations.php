<?php

namespace App\Console\Commands;

use App\AssetRentMonthlyExpenses;
use App\InventoryComponent;
use App\InventoryComponentTransfers;
use App\InventoryTransferTypes;
use App\Month;
use App\ProjectSite;
use App\Year;
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
    protected $description = 'Command description';

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
            $inventoryComponentTransfer = new InventoryComponentTransfers();
            $inventoryComponent = new InventoryComponent();
            $inventoryTransferType = new InventoryTransferTypes();
            $asseRentMonthlyExpense = new AssetRentMonthlyExpenses();
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
            $projectSites = $projectSite/*->where('id',23)*/->get();
            $data = array();
            $inTransferTypeIds = $inventoryTransferType->where('type','IN')->pluck('id')->toArray();
            $carryForwardQuantity = 0;

            foreach($projectSites as $projectSite) {
                $inventoryComponentData = $inventoryComponent->where('project_site_id',$projectSite['id'])
                    ->where('is_material',false)/*->where('id',953)*/
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
                        $asseRentMonthlyExpenseData = $asseRentMonthlyExpense->where('project_site_id',$projectSite['id'])
                            ->where('year_id',$thisYear['id'])
                            ->where('asset_id',$inventoryComponent['asset_id'])
                            ->first();
                        if(count($inventoryComponentTransfers) == 0){
                            if($thisMonth['slug'] == 'january'){
                                $lastYearAsseRentMonthlyExpenseData = $asseRentMonthlyExpense->where('project_site_id',$projectSite['id'])
                                    ->where('year_id',$thisYear['slug']-1)
                                    ->where('asset_id',$inventoryComponent['asset_id'])
                                    ->first();
                                if($lastYearAsseRentMonthlyExpenseData != null){
                                    $noOfDaysInMonth = cal_days_in_month(CAL_GREGORIAN, 1, $thisYear['slug']);
                                    $lastMonthData = json_decode($lastYearAsseRentMonthlyExpenseData['december']);
                                    $jsonData['rent_per_month'] = $lastMonthData->rent_per_month;
                                    $jsonData['days_used'] = $noOfDaysInMonth;
                                    $jsonData['quantity_used'] = $lastMonthData->carry_forward_quantity;
                                    $jsonData['rent'] = ($lastMonthData->rent_per_month * $noOfDaysInMonth * $lastMonthData->carry_forward_quantity);
                                    $jsonData['carry_forward_quantity'] = $lastMonthData->carry_forward_quantity;
                                    $asseRentMonthlyExpenseData->update([
                                        $thisMonth['slug'] => json_encode($jsonData)
                                    ]);
                                }else{
                                    $jsonData['rent_per_month'] = $jsonData['days_used'] = $jsonData['quantity_used'] = $jsonData['rent'] = $jsonData['carry_forward_quantity'] = 0;
                                    if($asseRentMonthlyExpenseData != null){
                                        $asseRentMonthlyExpenseData->update([
                                            $thisMonth['slug'] => json_encode($jsonData)
                                        ]);
                                    }else{
                                        $asseRentMonthlyExpense->create([
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
                                $lastMonthData = json_decode($asseRentMonthlyExpenseData[$months[$lastMonthId]['slug']]);
                                $jsonData['rent_per_month'] = $lastMonthData->rent_per_month;
                                $jsonData['days_used'] = $noOfDaysInMonth;
                                $jsonData['quantity_used'] = $lastMonthData->carry_forward_quantity;
                                $jsonData['rent'] = ($lastMonthData->rent_per_month * $noOfDaysInMonth * $lastMonthData->carry_forward_quantity);
                                $jsonData['carry_forward_quantity'] = $lastMonthData->carry_forward_quantity;
                                $asseRentMonthlyExpenseData->update([
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

                                if($asseRentMonthlyExpenseData != null){
                                    $asseRentMonthlyExpenseData->update([
                                        $thisMonth['slug'] => json_encode($jsonData)
                                    ]);

                                }else{
                                    $asseRentMonthlyExpense->create([
                                        'project_site_id' => $projectSite['id'],
                                        'year_id' => $thisYear['id'],
                                        'asset_id' => $assetId,
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
    }
}
