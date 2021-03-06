<?php
    /**
     * Created by PhpStorm.
     * User: manoj
     * Date: 14/5/18
     * Time: 12:39 PM
     */

namespace App\Http\Controllers\CustomTraits;

use App\PaymentType;
use App\AssetMaintenanceBillPayment;
use App\BillReconcileTransaction;
use App\BillTransaction;
use App\PeticashSalaryTransaction;
use App\PeticashSiteTransfer;
use App\PeticashStatus;
use App\PeticashTransactionType;
use App\Project;
use App\ProjectSite;
use App\ProjectSiteAdvancePayment;
use App\ProjectSiteIndirectExpense;
use App\TransactionStatus;
use App\PurcahsePeticashTransaction;
use App\PurchaseOrderAdvancePayment;
use App\PurchaseOrderPayment;
use App\SiteTransferBillPayment;
use App\SubcontractorAdvancePayment;
use App\SubcontractorBillReconcileTransaction;
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
	    $paymenttypes = PaymentType::whereIn('slug',['cheque','neft','rtgs','internet-banking'])->pluck('id')->toArray();
            $allocatedAmount  = PeticashSiteTransfer::where('project_site_id',$projectSiteId)->sum('amount');
            $totalSalaryAmount = PeticashSalaryTransaction::where('peticash_transaction_type_id',PeticashTransactionType::where('slug','salary')->pluck('id')->first())
                ->where('project_site_id',$projectSiteId)
                ->where('peticash_status_id',$approvedPeticashStatusId)
		->whereNotIn('payment_type_id',$paymenttypes)
                ->sum('payable_amount');
            $totalAdvanceAmount = PeticashSalaryTransaction::where('peticash_transaction_type_id',PeticashTransactionType::where('slug','advance')->pluck('id')->first())
                ->where('project_site_id',$projectSiteId)
                ->where('peticash_status_id',$approvedPeticashStatusId)
		->whereNotIn('payment_type_id',$paymenttypes)
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

            $purchaseOrderBillPayments = PurchaseOrderPayment::join('purchase_order_bills','purchase_order_bills.id','=','purchase_order_payments.purchase_order_bill_id')
                ->join('purchase_orders','purchase_orders.id','=','purchase_order_bills.purchase_order_id')
                ->join('purchase_requests','purchase_requests.id','=','purchase_orders.purchase_request_id')
                ->where('purchase_order_payments.paid_from_slug','cash')
                ->where('purchase_requests.project_site_id',$projectSiteId)
                ->sum('purchase_order_payments.amount');

            $cashSubcontractorAdvancePaymentTotal = SubcontractorAdvancePayment::where('subcontractor_advance_payments.paid_from_slug','cash')
                ->where('project_site_id',$projectSiteId)->sum('amount');
            $cashSubcontractorBillTransactionTotal = SubcontractorBillTransaction::join('subcontractor_bills','subcontractor_bills.id','=','subcontractor_bill_transactions.subcontractor_bills_id')
                ->join('subcontractor_structure','subcontractor_structure.id','=','subcontractor_bills.sc_structure_id')
                ->where('subcontractor_structure.project_site_id',$projectSiteId)
                ->where('subcontractor_bill_transactions.paid_from_slug','cash')->sum('subcontractor_bill_transactions.subtotal');

            $subcontractorBillReconcile = SubcontractorBillReconcileTransaction::join('subcontractor_bills','subcontractor_bills.id','=','subcontractor_bill_reconcile_transactions.subcontractor_bill_id')
                ->join('payment_types','payment_types.id','=','subcontractor_bill_reconcile_transactions.payment_type_id')
                ->join('subcontractor_structure','subcontractor_structure.id','=','subcontractor_bills.sc_structure_id')
                ->where('subcontractor_structure.project_site_id',$projectSiteId)
                ->where('subcontractor_bill_reconcile_transactions.paid_from_slug','cash')
                ->sum('amount');

            $siteTransferCashAmount = SiteTransferBillPayment::join('site_transfer_bills','site_transfer_bills.id','=','site_transfer_bill_payments.site_transfer_bill_id')
                ->join('inventory_component_transfers','inventory_component_transfers.id','=','site_transfer_bills.inventory_component_transfer_id')
                ->join('inventory_components','inventory_components.id','inventory_component_transfers.inventory_component_id')
                ->where('site_transfer_bill_payments.paid_from_slug','cash')
                ->where('inventory_components.project_site_id',$projectSiteId)
                ->sum('site_transfer_bill_payments.amount');

            $assetMaintenanceCashAmount = AssetMaintenanceBillPayment::join('asset_maintenance_bills','asset_maintenance_bills.id','asset_maintenance_bill_payments.asset_maintenance_bill_id')
                ->join('asset_maintenance','asset_maintenance.id','=','asset_maintenance_bills.asset_maintenance_id')
                ->where('asset_maintenance.project_site_id',$projectSiteId)
                ->where('asset_maintenance_bill_payments.paid_from_slug','cash')
                ->sum('asset_maintenance_bill_payments.amount');

            $indirectGSTCashAmount = ProjectSiteIndirectExpense::where('project_site_id',$projectSiteId)
                ->where('paid_from_slug','cash')->sum('gst');

            $indirectTDSCashAmount = ProjectSiteIndirectExpense::where('project_site_id',$projectSiteId)
                ->where('paid_from_slug','cash')->sum('tds');

            $remainingAmount = ($allocatedAmount + $projectSiteAdvancedAmount + $salesBillCashAmount + $salesBillTransactions)
                                - ($totalSalaryAmount + $totalAdvanceAmount + $totalPurchaseAmount
                                    + $cashPurchaseOrderAdvancePaymentTotal + $cashSubcontractorAdvancePaymentTotal
                                    + $cashSubcontractorBillTransactionTotal + $subcontractorBillReconcile
                                    + $siteTransferCashAmount + $assetMaintenanceCashAmount
                                    + $indirectGSTCashAmount + $indirectTDSCashAmount + $purchaseOrderBillPayments);

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
                                        ->orderBy('projects.name','asc')
                                        ->pluck('project_sites.id')->toArray();
            }else{
                $projectSiteIds = ProjectSite::join('projects','projects.id','=','project_sites.project_id')
                    ->join('user_project_site_relation','user_project_site_relation.project_site_id','=','project_sites.id')
                    ->where('projects.is_active', true)
                    ->where('user_project_site_relation.user_id', $user->id)
                    ->orderBy('projects.name','asc')
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
                $billTxnStatusIds = TransactionStatus::where('slug',['approved'])->pluck('id');
                $salesBillTransactions = BillTransaction::join('bills','bills.id','=','bill_transactions.bill_id')
                    ->join('quotations','quotations.id','=','bills.quotation_id')
                    ->where('quotations.project_site_id', $projectSiteId)
                    ->where('bill_transactions.paid_from_slug','cash')
                    ->whereIn('bill_transactions.transaction_status_id',$billTxnStatusIds)
                    ->sum('total');
                $approvedPeticashStatusId = PeticashStatus::where('slug','approved')->pluck('id')->first();
		$paymenttypes = PaymentType::whereIn('slug',['cheque','neft','rtgs','internet-banking'])->pluck('id')->toArray();
                $allocatedAmount  = PeticashSiteTransfer::where('project_site_id',$projectSiteId)->sum('amount');
                $totalSalaryAmount = PeticashSalaryTransaction::where('peticash_transaction_type_id',PeticashTransactionType::where('slug','salary')->pluck('id')->first())
                    ->where('project_site_id',$projectSiteId)
                    ->where('peticash_status_id',$approvedPeticashStatusId)
		    ->whereNotIn('payment_type_id',$paymenttypes)
                    ->sum('payable_amount');
                $totalAdvanceAmount = PeticashSalaryTransaction::where('peticash_transaction_type_id',PeticashTransactionType::where('slug','advance')->pluck('id')->first())
                    ->where('project_site_id',$projectSiteId)
                    ->where('peticash_status_id',$approvedPeticashStatusId)
                    ->whereNotIn('payment_type_id',$paymenttypes)
                    ->sum('amount');
                $purchaseOrderBillPayments = PurchaseOrderPayment::join('purchase_order_bills','purchase_order_bills.id','=','purchase_order_payments.purchase_order_bill_id')
                    ->join('purchase_orders','purchase_orders.id','=','purchase_order_bills.purchase_order_id')
                    ->join('purchase_requests','purchase_requests.id','=','purchase_orders.purchase_request_id')
                    ->where('purchase_order_payments.paid_from_slug','cash')
                    ->where('purchase_requests.project_site_id',$projectSiteId)
                    ->sum('purchase_order_payments.amount');
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
                $approvedBillStatusId = TransactionStatus::where('slug','approved')->pluck('id')->first();
                $cashSubcontractorBillTransactionTotal = SubcontractorBillTransaction::join('subcontractor_bills','subcontractor_bills.id','=','subcontractor_bill_transactions.subcontractor_bills_id')
                    ->join('subcontractor_structure','subcontractor_structure.id','=','subcontractor_bills.sc_structure_id')
                    ->where('subcontractor_structure.project_site_id',$projectSiteId)
                    ->where('subcontractor_bill_transactions.transaction_status_id', $approvedBillStatusId)
                    ->where('subcontractor_bill_transactions.paid_from_slug','cash')->sum('subcontractor_bill_transactions.subtotal');
                $subcontractorBillReconcile = SubcontractorBillReconcileTransaction::join('subcontractor_bills','subcontractor_bills.id','=','subcontractor_bill_reconcile_transactions.subcontractor_bill_id')
                    ->join('subcontractor_structure','subcontractor_structure.id','=','subcontractor_bills.sc_structure_id')
                    ->where('subcontractor_structure.project_site_id',$projectSiteId)
                    ->where('subcontractor_bill_reconcile_transactions.paid_from_slug','cash')
                    ->sum('subcontractor_bill_reconcile_transactions.amount');
                $siteTransferCashAmount = SiteTransferBillPayment::join('site_transfer_bills','site_transfer_bills.id','=','site_transfer_bill_payments.site_transfer_bill_id')
                    ->join('inventory_component_transfers','inventory_component_transfers.id','=','site_transfer_bills.inventory_component_transfer_id')
                    ->join('inventory_components','inventory_components.id','inventory_component_transfers.inventory_component_id')
                    ->where('site_transfer_bill_payments.paid_from_slug','cash')
                    ->where('inventory_components.project_site_id',$projectSiteId)
                    ->sum('site_transfer_bill_payments.amount');
                $assetMaintenanceCashAmount = AssetMaintenanceBillPayment::join('asset_maintenance_bills','asset_maintenance_bills.id','asset_maintenance_bill_payments.asset_maintenance_bill_id')
                    ->join('asset_maintenance','asset_maintenance.id','=','asset_maintenance_bills.asset_maintenance_id')
                    ->where('asset_maintenance.project_site_id',$projectSiteId)
                    ->where('asset_maintenance_bill_payments.paid_from_slug','cash')
                    ->sum('asset_maintenance_bill_payments.amount');
                $indirectGSTCashAmount = ProjectSiteIndirectExpense::where('project_site_id',$projectSiteId)
                    ->where('paid_from_slug','cash')->sum('gst');
                $indirectTDSCashAmount = ProjectSiteIndirectExpense::where('project_site_id',$projectSiteId)
                    ->where('paid_from_slug','cash')->sum('tds');
                $remainingAmount = round(($allocatedAmount -
                    ($totalSalaryAmount + $totalAdvanceAmount + $totalPurchaseAmount + $cashPurchaseOrderAdvancePaymentTotal
                        + $cashSubcontractorAdvancePaymentTotal + $cashSubcontractorBillTransactionTotal
                        + $subcontractorBillReconcile + $siteTransferCashAmount + $assetMaintenanceCashAmount
                        + $indirectGSTCashAmount + $indirectTDSCashAmount + $purchaseOrderBillPayments)),3);
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
