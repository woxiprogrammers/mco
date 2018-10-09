<?php

namespace App\Console\Commands;

use App\Bill;
use App\BillReconcileTransaction;
use App\BillStatus;
use App\BillTransaction;
use App\Month;
use App\PeticashPurchaseTransactionMonthlyExpense;
use App\PeticashSalaryTransaction;
use App\PeticashSalaryTransactionMonthlyExpense;
use App\PeticashTransactionType;
use App\ProjectSite;
use App\ProjectSiteAdvancePayment;
use App\ProjectSiteSalaryDistribution;
use App\PurchaseOrderBillMonthlyExpense;
use App\Quotation;
use App\SubcontractorBill;
use App\SubcontractorBillStatus;
use App\SubcontractorStructure;
use App\Year;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SalaryDistributionInSites extends Command
{

    //php artisan custom:custom:salary-distribution --month=8 --year=2018
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'custom:salary-distribution';

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
            $thisYear = $year->where('slug',date('Y', strtotime('last month')))->first();
            $thisMonth = $month->where('id',date('m', strtotime('last month')))->first();
            $projectSite = new ProjectSite();
            $quotation = new Quotation();
            $subcontractorStructure = new SubcontractorStructure();
            $subcontractorBill = new SubcontractorBill();
            $subcontractorBillStatus = new SubcontractorBillStatus();
            $peticashSalaryTransaction = new PeticashSalaryTransaction();
            $peticashSalaryTransactionType = new PeticashTransactionType();
            $purchaseOrderBillMonthlyExpense = new PurchaseOrderBillMonthlyExpense();
            $peticashSalaryTransactionMonthlyExpense = new PeticashSalaryTransactionMonthlyExpense();
            $peticashPurchaseTransactionMonthlyExpense = new PeticashPurchaseTransactionMonthlyExpense();
            $projectSiteSalaryDistribution = new ProjectSiteSalaryDistribution();
            $subcontractorApprovedBillStatusId = $subcontractorBillStatus->where('slug','approved')->pluck('id')->first();
            $projectSiteIds = $projectSite->where('name','!=',env('OFFICE_PROJECT_SITE_NAME'))->pluck('id');
            $iterator = $totalExpense = 0;
            $monthlyExpenseData = array();
            foreach($projectSiteIds as $projectSiteId){
                $subcontractorTotal = $purchaseAmount = $salaryAmount = $peticashPurchaseAmount = $officeExpense = $assetRent = 0;
                $totalAssetRent = $totalAssetRentOpeningExpense = 0;
                $purchaseAmount = $purchaseOrderBillMonthlyExpense
                    ->where('month_id',$thisMonth['id'])
                    ->where('year_id',$thisYear['id'])
                    ->where('project_site_id',$projectSiteId)->sum('total_expense');
                $salaryAmount = $peticashSalaryTransactionMonthlyExpense
                    ->where('month_id',$thisMonth['id'])
                    ->where('year_id',$thisYear['id'])
                    ->where('project_site_id',$projectSiteId)->sum('total_expense');
                $peticashPurchaseAmount = $peticashPurchaseTransactionMonthlyExpense
                    ->where('month_id',$thisMonth['id'])
                    ->where('year_id',$thisYear['id'])
                    ->where('project_site_id',$projectSiteId)->sum('total_expense');
                $subcontractorBillIds = $subcontractorBill->join('subcontractor_structure','subcontractor_bills.sc_structure_id',
                    '=','subcontractor_structure.id')
                    ->where('project_site_id',$projectSiteId)
                    ->where('subcontractor_bills.subcontractor_bill_status_id',$subcontractorApprovedBillStatusId)
                    ->whereMonth('subcontractor_bills.created_at',$thisMonth['id'])
                    ->whereYear('subcontractor_bills.created_at',$thisYear['slug'])
                    ->pluck('subcontractor_bills.id');

                if(count($subcontractorBillIds) > 0){
                    foreach ($subcontractorBillIds as $subcontractorBillId){
                        $subcontractorBillData = $subcontractorBill->where('id',$subcontractorBillId)->first();
                        $subcontractorStructureData = $subcontractorStructure->where('id',$subcontractorBillData['sc_structure_id'])->first();
                        if($subcontractorStructureData->contractType->slug == 'sqft'){
                            $rate = $subcontractorStructureData['rate'];
                        }else{
                            $rate = $subcontractorStructureData['rate'] * $subcontractorStructureData['total_work_area'];
                        }
                        $subcontractorBillTaxes = $subcontractorBillData->subcontractorBillTaxes;
                        $subTotal = $subcontractorBillData['qty'] * $rate;
                        $taxTotal = 0;
                        foreach($subcontractorBillTaxes as $key => $subcontractorBillTaxData){
                            $taxTotal += round((($subcontractorBillTaxData['percentage'] * $subTotal) / 100),3);
                        }
                        $subcontractorTotal += round(($subTotal + $taxTotal),3);
                    }
                }
                $openingExpenses = $quotation->where('project_site_id',$projectSiteId)->sum('opening_expenses');
                $totalAssetRentOpeningExpense = $projectSite->where('id',$projectSiteId)->sum('asset_rent_opening_expense');
                $totalExpenseAtSite = round((round($purchaseAmount,3) + round($salaryAmount,3) + round($assetRent,3) +
                                    round($peticashPurchaseAmount,3) + round($officeExpense,3) + round($totalAssetRentOpeningExpense,3)
                                    + round($subcontractorTotal,3) + round($openingExpenses,3)),3);
                if($totalExpenseAtSite > 0){
                    $totalExpense += $totalExpenseAtSite;
                    $monthlyExpenseData[$iterator]['project_site_id'] = $projectSiteId;
                    $monthlyExpenseData[$iterator]['monthly_expense'] = $totalExpenseAtSite;
                    $iterator++;
                }
            }
            $officeProjectSiteId = $projectSite->where('name',env('OFFICE_PROJECT_SITE_NAME'))->pluck('id')->first();
            $peticashSalaryTransactionAtHeadOffice = $peticashSalaryTransaction
                ->where('project_site_id',$officeProjectSiteId)
                ->whereMonth('date',$thisMonth['id'])
                ->whereYear('date',$thisYear['slug'])
                ->get();
            $salaryTransactionId = $peticashSalaryTransactionType->where('slug','salary')->pluck('id')->first();
            $advanceTransactionId = $peticashSalaryTransactionType->where('slug','advance')->pluck('id')->first();
            $salaryTotal = $peticashSalaryTransactionAtHeadOffice->where('peticash_transaction_type_id',$salaryTransactionId)->sum('payable_amount');
            $advanceTotal = $peticashSalaryTransactionAtHeadOffice->where('peticash_transaction_type_id',$advanceTransactionId)->sum('amount');
            $totalSalaryAtHeadOffice = $salaryTotal + $advanceTotal;
            $ratio = round(($totalSalaryAtHeadOffice / $totalExpense),3);
            foreach($monthlyExpenseData as $expenseData){
                $distributedAmount = round(($expenseData['monthly_expense'] * $ratio),3);
                if($distributedAmount != 0){
                    $projectSiteSalaryDistributionData = $projectSiteSalaryDistribution->where('project_site_id',$expenseData['project_site_id'])
                        ->where('month_id',$thisMonth['id'])->where('year_id',$thisYear['id'])->first();
                    if($projectSiteSalaryDistributionData != null){
                        $projectSiteSalaryDistributionData->update([
                            'distributed_amount' => $distributedAmount
                    ]);
                    }else{
                        $projectSiteSalaryDistribution->create([
                            'project_site_id' => $expenseData['project_site_id'],
                            'month_id' => $thisMonth['id'],
                            'year_id' => $thisYear['id'],
                            'distributed_amount' => $distributedAmount
                        ]);
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
