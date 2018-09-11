<?php

namespace App\Console\Commands;

use App\Month;
use App\ProjectSite;
use App\PurchaseOrderBill;
use App\PurchaseOrderBillMonthlyExpense;
use App\Year;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PurchaseOrderBillMonthlyExpenseCalculations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */

   //php artisan custom:purchase-order-bill-monthly-expense-calculation  => Executes Case 3 => $thisMonth == 'null' && $thisYear == 'null'
    //php artisan custom:purchase-order-bill-monthly-expense-calculation --month=all --year=all  => Executes Case 1


    //php artisan custom:purchase-order-bill-monthly-expense-calculation 09 2018
    //php artisan custom:purchase-order-bill-monthly-expense-calculation --month=09 --year=2018
    //protected $signature = 'custom:purchase-order-bill-monthly-expense-calculation {month} {year}';
    protected $signature = 'custom:purchase-order-bill-monthly-expense-calculation {--month=null} {--year=null}';

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
            $thisMonth = $this->option('month');
            $thisYear = $this->option('year');
            $year = new Year();
            $month = new Month();
            $projectSite = new ProjectSite();
            $purchaseOrderBill = new PurchaseOrderBill();
            $purchaseOrderBillMonthlyExpenses = new PurchaseOrderBillMonthlyExpense();
            switch (true){
                case ($thisMonth == 'all' && $thisYear == 'all') :
                    $currentYearId = $year->where('slug',date('Y'))->pluck('id')->first();
                    $tillCurrentYearIds = $year->where('id','>=',$currentYearId)->pluck('id');
                    $monthsData = $month->pluck('id')->toArray();
                    foreach ($tillCurrentYearIds as $thisYearId){
                        $thisYear = $year->where('id',$thisYearId)->pluck('slug')->first();
                        foreach ($monthsData as $thisMonth){
                            $projectSiteIds = $projectSite->pluck('id')->toArray();
                            foreach ($projectSiteIds as $projectSiteId){
                                $purchaseOrderBillData = $purchaseOrderBill
                                    ->join('purchase_orders','purchase_orders.id','='
                                        ,'purchase_order_bills.purchase_order_id')
                                    ->join('purchase_requests','purchase_requests.id','='
                                        ,'purchase_orders.purchase_request_id')
                                    ->join('vendors','vendors.id','=','purchase_orders.vendor_id')
                                    ->where('purchase_requests.project_site_id',$projectSiteId)
                                    ->whereMonth('purchase_order_bills.created_at',$thisMonth)
                                    ->whereYear('purchase_order_bills.created_at',$thisYear)
                                    ->select('purchase_order_bills.amount as basic_amount','purchase_order_bills.transportation_tax_amount as transportation_tax_amount','purchase_order_bills.tax_amount as tax_amount','purchase_order_bills.extra_tax_amount as extra_tax_amount')
                                    ->get();
                                $basicAmount =  round($purchaseOrderBillData
                                    ->sum('basic_amount'),3);
                                $transportationTaxAmount =  round($purchaseOrderBillData
                                    ->sum('transportation_tax_amount'),3);
                                $taxAmount =  round($purchaseOrderBillData
                                    ->sum('tax_amount'),3);
                                $extraTaxAmount =  round($purchaseOrderBillData
                                    ->sum('extra_tax_amount'),3);
                                $totalAmount = $basicAmount + $transportationTaxAmount + $taxAmount + $extraTaxAmount;
                                if($totalAmount != 0){
                                    $alreadyExist = $purchaseOrderBillMonthlyExpenses->where('project_site_id',$projectSiteId)->where('month_id',$thisMonth)->where('year_id',$thisYearId)->first();
                                    if($alreadyExist != null){
                                        $alreadyExist->update([
                                            'total_expense' => round($totalAmount,3)
                                        ]);
                                    }else{
                                        $purchaseOrderBillMonthlyExpenses->create([
                                            'project_site_id' => $projectSiteId,
                                            'month_id' => $thisMonth,
                                            'year_id' => $thisYearId,
                                            'total_expense' => round($totalAmount,3)
                                        ]);
                                    }
                                }
                            }
                        }
                    }

                    break;

                case ($thisMonth == 'all' && $thisYear != null) :
                    dd(2);
                    $yearId = $year->where('slug',$thisYear)->pluck('id')->first();
                    if($yearId == null){
                        $this->info("Please enter proper year in 4 digit (Eg. 2018)");
                    }else{
                        $monthsData = $month->pluck('id')->toArray();
                            foreach ($monthsData as $thisMonth){
                                $projectSiteIds = $projectSite->pluck('id')->toArray();
                                foreach ($projectSiteIds as $projectSiteId){
                                    $purchaseOrderBillData = $purchaseOrderBill
                                        ->join('purchase_orders','purchase_orders.id','='
                                            ,'purchase_order_bills.purchase_order_id')
                                        ->join('purchase_requests','purchase_requests.id','='
                                            ,'purchase_orders.purchase_request_id')
                                        ->join('vendors','vendors.id','=','purchase_orders.vendor_id')
                                        ->where('purchase_requests.project_site_id',$projectSiteId)
                                        ->whereMonth('purchase_order_bills.created_at',$thisMonth)
                                        ->whereYear('purchase_order_bills.created_at',$thisYear)
                                        ->select('purchase_order_bills.amount as basic_amount','purchase_order_bills.transportation_tax_amount as transportation_tax_amount','purchase_order_bills.tax_amount as tax_amount','purchase_order_bills.extra_tax_amount as extra_tax_amount')
                                        ->get();
                                    $basicAmount =  round($purchaseOrderBillData
                                        ->sum('basic_amount'),3);
                                    $transportationTaxAmount =  round($purchaseOrderBillData
                                        ->sum('transportation_tax_amount'),3);
                                    $taxAmount =  round($purchaseOrderBillData
                                        ->sum('tax_amount'),3);
                                    $extraTaxAmount =  round($purchaseOrderBillData
                                        ->sum('extra_tax_amount'),3);
                                    $totalAmount = $basicAmount + $transportationTaxAmount + $taxAmount + $extraTaxAmount;
                                    if($totalAmount != 0){
                                        $alreadyExist = $purchaseOrderBillMonthlyExpenses->where('project_site_id',$projectSiteId)->where('month_id',$monthId)->where('year_id',$yearId)->first();
                                        if($alreadyExist != null){
                                            $alreadyExist->update([
                                                'total_expense' => round($totalAmount,3)
                                            ]);
                                        }else{
                                            $purchaseOrderBillMonthlyExpenses->create([
                                                'project_site_id' => $projectSiteId,
                                                'month_id' => $monthId,
                                                'year_id' => $yearId,
                                                'total_expense' => round($totalAmount,3)
                                            ]);
                                        }
                                    }

                                }
                            }

                    }
                    break;

                case ($thisMonth == 'null' && $thisYear == 'null') :
                    $projectSiteIds = $projectSite->pluck('id')->toArray();
                    $todayDate = Carbon::today();
                    $monthId = date('n',strtotime($todayDate));
                    $yearId = $year->where('slug',date('Y',strtotime($todayDate)))->pluck('id')->first();
                    foreach ($projectSiteIds as $projectSiteId){
                        $purchaseOrderBillData = $purchaseOrderBill
                            ->join('purchase_orders','purchase_orders.id','='
                                ,'purchase_order_bills.purchase_order_id')
                            ->join('purchase_requests','purchase_requests.id','='
                                ,'purchase_orders.purchase_request_id')
                            ->join('vendors','vendors.id','=','purchase_orders.vendor_id')
                            ->where('purchase_requests.project_site_id',$projectSiteId)
                            ->whereDate('purchase_order_bills.created_at','=',$todayDate)
                            ->select('purchase_order_bills.amount as basic_amount','purchase_order_bills.transportation_tax_amount as transportation_tax_amount'
                                ,'purchase_order_bills.tax_amount as tax_amount','purchase_order_bills.extra_tax_amount as extra_tax_amount',
                                'purchase_order_bills')
                            ->get();
                        $basicAmount =  round($purchaseOrderBillData
                            ->sum('basic_amount'),3);
                        $transportationTaxAmount =  round($purchaseOrderBillData
                            ->sum('transportation_tax_amount'),3);
                        $taxAmount =  round($purchaseOrderBillData
                            ->sum('tax_amount'),3);
                        $extraTaxAmount =  round($purchaseOrderBillData
                            ->sum('extra_tax_amount'),3);
                        $totalAmount = $basicAmount + $transportationTaxAmount + $taxAmount + $extraTaxAmount;

                        if($totalAmount != 0){

                            $alreadyExist = $purchaseOrderBillMonthlyExpenses->where('project_site_id',$projectSiteId)->where('month_id',$monthId)->where('year_id',$yearId)->first();
                            if($alreadyExist != null){
                                $alreadyExist->update([
                                    'total_expense' => round($totalAmount,3)
                                ]);
                            }else{
                                $purchaseOrderBillMonthlyExpenses->create([
                                    'project_site_id' => $projectSiteId,
                                    'month_id' => $monthId,
                                    'year_id' => $yearId,
                                    'total_expense' => round($totalAmount,3)
                                ]);
                            }
                        }

                    }
                    break;

                case ($thisMonth != null && $thisYear != null) :
                    dd(3);
                        $yearId = $year->where('slug',$thisYear)->pluck('id')->first();
                        $monthId = $month->where('id',$thisMonth)->pluck('id')->first();
                        if($yearId == null){
                            $this->info("Please enter proper year in 4 digit (Eg. 2018)");
                        }elseif($monthId == null){
                            $this->info("Please enter proper month (Eg. 2)");
                        }else{
                            $projectSiteIds = $projectSite->pluck('id')->toArray();
                            foreach ($projectSiteIds as $projectSiteId){
                                $purchaseOrderBillData = $purchaseOrderBill
                                    ->join('purchase_orders','purchase_orders.id','='
                                        ,'purchase_order_bills.purchase_order_id')
                                    ->join('purchase_requests','purchase_requests.id','='
                                        ,'purchase_orders.purchase_request_id')
                                    ->join('vendors','vendors.id','=','purchase_orders.vendor_id')
                                    ->where('purchase_requests.project_site_id',$projectSiteId)
                                    ->whereMonth('purchase_order_bills.created_at',$thisMonth)
                                    ->whereYear('purchase_order_bills.created_at',$thisYear)
                                    ->select('purchase_order_bills.amount as basic_amount','purchase_order_bills.transportation_tax_amount as transportation_tax_amount','purchase_order_bills.tax_amount as tax_amount','purchase_order_bills.extra_tax_amount as extra_tax_amount')
                                    ->get();
                                $basicAmount =  round($purchaseOrderBillData
                                    ->sum('basic_amount'),3);
                                $transportationTaxAmount =  round($purchaseOrderBillData
                                    ->sum('transportation_tax_amount'),3);
                                $taxAmount =  round($purchaseOrderBillData
                                    ->sum('tax_amount'),3);
                                $extraTaxAmount =  round($purchaseOrderBillData
                                    ->sum('extra_tax_amount'),3);
                                $totalAmount = $basicAmount + $transportationTaxAmount + $taxAmount + $extraTaxAmount;
                                if($totalAmount != 0){
                                    $alreadyExist = $purchaseOrderBillMonthlyExpenses->where('project_site_id',$projectSiteId)->where('month_id',$monthId)->where('year_id',$yearId)->first();
                                    if($alreadyExist != null){
                                        $alreadyExist->update([
                                            'total_expense' => round($totalAmount,3)
                                        ]);
                                    }else{
                                        $purchaseOrderBillMonthlyExpenses->create([
                                            'project_site_id' => $projectSiteId,
                                            'month_id' => $monthId,
                                            'year_id' => $yearId,
                                            'total_expense' => round($totalAmount,3)
                                        ]);
                                    }
                                }

                            }
                        }
                    break;



            }
        }catch(\Exception $e){
            $data = [
                'action' => 'Purchase Order Bill Monthly Expense Calculations',
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            $this->error($e->getMessage());
        }
    }
}
