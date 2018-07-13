<?php
    /**
     * Created by PhpStorm.
     * User: manoj
     * Date: 14/5/18
     * Time: 12:39 PM
     */

namespace App\Http\Controllers\CustomTraits;


use App\BillReconcileTransaction;
use App\BillTransaction;
use App\PeticashSalaryTransaction;
use App\PeticashSiteTransfer;
use App\PeticashStatus;
use App\PeticashTransactionType;
use App\Project;
use App\ProjectSite;
use App\ProjectSiteAdvancePayment;
use App\PurcahsePeticashTransaction;
use App\PurchaseOrderAdvancePayment;
use App\SubcontractorAdvancePayment;
use App\SubcontractorBillTransaction;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

trait PeticashTrait{

    public function getSiteWiseStatistics(){
        $projectSiteId = Session::get('global_project_site');
        try{
            $projectSiteAdvancedAmount = ProjectSiteAdvancePayment::where('project_site_id',$projectSiteId)
                                            ->where('paid_from_slug','cash')
                                            ->sum('amount');
            $salesBillCashAmount = BillReconcileTransaction::join('bills','bills.id','=','bill_reconcile_transactions.bill_id')
                                            ->join('quotations','quotations.id','=','bills.quotation_id')
                                            ->where('quotations.project_site_id', $projectSiteId)
                                            ->where('bill_reconcile_transactions.paid_from_slug','cash')
                                            ->sum('bill_reconcile_transactions.amount');
            $salesBillTransactions = BillTransaction::join('bills','bills.id','=','bill_transactions.bill_id')
                                            ->join('quotations','quotations.id','=','bills.quotation_id')
                                            ->where('quotations.project_site_id', $projectSiteId)
                                            ->where('bill_transactions.paid_from_slug','cash')
                                            ->sum('total');
            $approvedPeticashStatusId = PeticashStatus::where('slug','approved')->pluck('id')->first();
            $allocatedAmount  = PeticashSiteTransfer::where('project_site_id',$projectSiteId)->sum('amount');
            $totalSalaryAmount = PeticashSalaryTransaction::where('peticash_transaction_type_id',PeticashTransactionType::where('slug','salary')->pluck('id')->first())
                ->where('project_site_id',$projectSiteId)
                ->where('peticash_status_id',$approvedPeticashStatusId)
                ->sum('payable_amount');
            $totalAdvanceAmount = PeticashSalaryTransaction::where('peticash_transaction_type_id',PeticashTransactionType::where('slug','advance')->pluck('id')->first())
                ->where('project_site_id',$projectSiteId)
                ->where('peticash_status_id',$approvedPeticashStatusId)
                ->sum('amount');
            $totalPurchaseAmount = PurcahsePeticashTransaction::whereIn('peticash_transaction_type_id', PeticashTransactionType::where('type','PURCHASE')->pluck('id'))
                ->where('project_site_id',$projectSiteId)
                ->where('peticash_status_id',$approvedPeticashStatusId)
                ->sum('bill_amount');
            $cashPurchaseOrderAdvancePaymentTotal = PurchaseOrderAdvancePayment::join('purchase_orders','purchase_orders.id','=','purchase_order_advance_payments.purchase_order_id')
                                                        ->join('purchase_requests','purchase_requests.id','=','purchase_orders.purchase_request_id')
                                                        ->where('purchase_order_advance_payments.paid_from_slug','cash')
                                                        ->where('purchase_requests.project_site_id',$projectSiteId)
                                                        ->sum('amount');

            $cashSubcontractorAdvancePaymentTotal = SubcontractorAdvancePayment::where('subcontractor_advance_payments.paid_from_slug','cash')
                ->where('project_site_id',$projectSiteId)->sum('amount');
            $cashSubcontractorBillTransactionTotal = SubcontractorBillTransaction::join('subcontractor_bills','subcontractor_bills.id','=','subcontractor_bill_transactions.subcontractor_bills_id')
                ->join('subcontractor_structure','subcontractor_structure.id','=','subcontractor_bills.sc_structure_id')
                ->where('subcontractor_structure.project_site_id',$projectSiteId)
                ->where('subcontractor_bill_transactions.paid_from_slug','cash')->sum('subcontractor_bill_transactions.subtotal');
            $remainingAmount = ($allocatedAmount + $projectSiteAdvancedAmount + $salesBillCashAmount + $salesBillTransactions) - ($totalSalaryAmount + $totalAdvanceAmount + $totalPurchaseAmount + $cashPurchaseOrderAdvancePaymentTotal + $cashSubcontractorAdvancePaymentTotal + $cashSubcontractorBillTransactionTotal);
        }catch (\Exception $e){
            $data = [
                'action' => 'Get Peticash sitewise statistics',
                'exception' => $e->getMessage(),
                'params' => $projectSiteId
            ];
            Log::critical(json_encode($data));
            $remainingAmount = $allocatedAmount = $salesBillCashAmount = $salesBillTransactions = $projectSiteAdvancedAmount = 0;
        }
        $response = [
            'remainingAmount' => $remainingAmount,
            'allocatedAmount' => $allocatedAmount + $salesBillCashAmount + $salesBillTransactions + $projectSiteAdvancedAmount
        ];
        return $response;
    }

    public function getAllSitesStatistics($user){
        try{
            if($user->roles[0]->role->slug == 'superadmin' || $user->roles[0]->role->slug == 'admin'){
                $projectSiteIds = ProjectSite::join('projects','projects.id','=','project_sites.project_id')
                                        ->where('projects.is_active', true)
                                        ->pluck('project_sites.id')->toArray();
            }else{
                $projectSiteIds = ProjectSite::join('projects','projects.id','=','project_sites.project_id')
                    ->join('user_project_site_relation','user_project_site_relation.project_site_id','=','project_sites.id')
                    ->where('projects.is_active', true)
                    ->where('user_project_site_relation.user_id', $user->id)
                    ->pluck('project_sites.id')->toArray();
            }
            $statistics = array();
            foreach($projectSiteIds as $projectSiteId){
                $projectSiteAdvancedAmount = ProjectSiteAdvancePayment::where('project_site_id',$projectSiteId)
                    ->where('paid_from_slug','cash')
                    ->sum('amount');
                $salesBillCashAmount = BillReconcileTransaction::join('bills','bills.id','=','bill_reconcile_transactions.bill_id')
                    ->join('quotations','quotations.id','=','bills.quotation_id')
                    ->where('quotations.project_site_id', $projectSiteId)
                    ->where('bill_reconcile_transactions.paid_from_slug','cash')
                    ->sum('bill_reconcile_transactions.amount');
                $salesBillTransactions = BillTransaction::join('bills','bills.id','=','bill_transactions.bill_id')
                    ->join('quotations','quotations.id','=','bills.quotation_id')
                    ->where('quotations.project_site_id', $projectSiteId)
                    ->where('bill_transactions.paid_from_slug','cash')
                    ->sum('total');
                $approvedPeticashStatusId = PeticashStatus::where('slug','approved')->pluck('id')->first();
                $allocatedAmount  = PeticashSiteTransfer::where('project_site_id',$projectSiteId)->sum('amount');
                $totalSalaryAmount = PeticashSalaryTransaction::where('peticash_transaction_type_id',PeticashTransactionType::where('slug','salary')->pluck('id')->first())
                    ->where('project_site_id',$projectSiteId)
                    ->where('peticash_status_id',$approvedPeticashStatusId)
                    ->sum('payable_amount');
                $totalAdvanceAmount = PeticashSalaryTransaction::where('peticash_transaction_type_id',PeticashTransactionType::where('slug','advance')->pluck('id')->first())
                    ->where('project_site_id',$projectSiteId)
                    ->where('peticash_status_id',$approvedPeticashStatusId)
                    ->sum('amount');
                $totalPurchaseAmount = PurcahsePeticashTransaction::whereIn('peticash_transaction_type_id', PeticashTransactionType::where('type','PURCHASE')->pluck('id'))
                    ->where('project_site_id',$projectSiteId)
                    ->where('peticash_status_id',$approvedPeticashStatusId)
                    ->sum('bill_amount');
                $cashPurchaseOrderAdvancePaymentTotal = PurchaseOrderAdvancePayment::join('purchase_orders','purchase_orders.id','=','purchase_order_advance_payments.purchase_order_id')
                    ->join('purchase_requests','purchase_requests.id','=','purchase_orders.purchase_request_id')
                    ->where('purchase_order_advance_payments.paid_from_slug','cash')
                    ->where('purchase_requests.project_site_id',$projectSiteId)
                    ->sum('amount');
                $cashSubcontractorAdvancePaymentTotal = SubcontractorAdvancePayment::where('subcontractor_advance_payments.paid_from_slug','cash')
                    ->where('project_site_id',$projectSiteId)->sum('amount');
                $cashSubcontractorBillTransactionTotal = SubcontractorBillTransaction::join('subcontractor_bills','subcontractor_bills.id','=','subcontractor_bill_transactions.subcontractor_bills_id')
                    ->join('subcontractor_structure','subcontractor_structure.id','=','subcontractor_bills.sc_structure_id')
                    ->where('subcontractor_structure.project_site_id',$projectSiteId)
                    ->where('subcontractor_bill_transactions.paid_from_slug','cash')->sum('subcontractor_bill_transactions.subtotal');
                $remainingAmount = round((($allocatedAmount + $projectSiteAdvancedAmount + $salesBillCashAmount + $salesBillTransactions) - ($totalSalaryAmount + $totalAdvanceAmount + $totalPurchaseAmount + $cashPurchaseOrderAdvancePaymentTotal + $cashSubcontractorAdvancePaymentTotal + $cashSubcontractorBillTransactionTotal)),3);
                $allocatedAmount = $allocatedAmount + $salesBillCashAmount + $salesBillTransactions + $projectSiteAdvancedAmount;
                $projectName = Project::join('project_sites','projects.id','=','project_sites.project_id')
                                        ->where('project_sites.id', $projectSiteId)
                                        ->pluck('projects.name')->first();
                $statistics[] = [
                    'project' => $projectName,
                    'remainingAmount' => $remainingAmount,
                    'allocatedAmount' => $allocatedAmount
                ];
            }
            return $statistics;
        }catch (\Exception $e){
            $data = [
                'action' => 'Get All Sites Statistics',
                'user' => $user,
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            return null;
        }
    }

}