<?php

namespace App\Console\Commands;

use App\Month;
use App\ProjectSite;
use App\PurchaseOrderBill;
use App\PurchaseOrderBillMonthlyExpense;
use App\Year;
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
    protected $signature = 'custom:purchase-order-bill-monthly-expense-calculation {month} {year}';

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
            $purchaseOrderBillMonthlyExpenses = new PurchaseOrderBillMonthlyExpense();
            $thisMonth = $this->argument('month');
            $thisYear = $this->argument('year');
            $purchaseOrderBill = new PurchaseOrderBill();
            $projectSite = new ProjectSite();
            $year = new Year();
            $month = new Month();
            $projectSiteIds = $projectSite->pluck('id')->toArray();
            $totalAmount = 0;
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
                Log::info($purchaseOrderBillData->toArray());
                $basicAmount =  $purchaseOrderBillData
                    ->sum('basic_amount');
                $transportationTaxAmount =  $purchaseOrderBillData
                    ->sum('transportation_tax_amount');
                $taxAmount =  $purchaseOrderBillData
                    ->sum('tax_amount');
                $extraTaxAmount =  $purchaseOrderBillData
                    ->sum('extra_tax_amount');
                $totalAmount += $basicAmount + $transportationTaxAmount + $taxAmount + $extraTaxAmount;
                $yearId = $year->where('slug',$thisYear)->pluck('id')->first();
                $monthId = $month->where('id',$thisMonth)->pluck('id')->first();
                $alreadyExist = $purchaseOrderBillMonthlyExpenses->where('month_id',$monthId)->where('year_id',$yearId)->first();
                if($alreadyExist != null){
                    $alreadyExist->update([
                        'total_expense' => $totalAmount
                    ]);
                }else{
                    $purchaseOrderBillMonthlyExpenses->create([
                        'project_site_id' => $projectSiteId,
                        'month_id' => $monthId,
                        'year_id' => $yearId,
                        'total_expense' => $totalAmount
                    ]);
                }
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
