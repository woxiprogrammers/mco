<?php

namespace App\Http\Controllers\Purchase;

use App\Client;
use App\Helper\UnitHelper;
use App\ProjectSite;
use App\PurchaseOrder;
use App\PurchaseOrderBill;
use App\PurchaseOrderTransaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class PurchaseOrderBillingController extends Controller
{
    public function __construct(){
        $this->middleware('custom.auth');
    }

    public function getManageView(Request $request){
        try{
            return view('purchase.purchase-order-billing.manage');
        }catch(\Exception $e){
            $data = [
                'action' => 'Get PO billing Manage View',
                'exception' => $e->getMessage()
            ];
            Log::cirtical(json_encode($data));
            abort(500);
        }
    }

    public function getCreateView(Request $request){
        try{
            $clients = Client::join('projects','projects.client_id','=','clients.id')
                                ->join('project_sites','project_sites.project_id','=','projects.id')
                                ->join('quotations','quotations.project_site_id','=','project_sites.id')
                                ->where('clients.is_active', true)
                                ->select('clients.id as id','clients.company as company')
                                ->distinct('id')
                                ->get()
                                ->toArray();
            $purchaseOrderTransactionDetails = PurchaseOrderTransaction::join('purchase_order_transaction_statuses','purchase_order_transaction_statuses.id','=','purchase_order_transactions.purchase_order_transaction_status_id')
                                                                ->where('purchase_order_transaction_statuses.slug','bill-pending')
                                                                ->select('purchase_order_transactions.id as id','purchase_order_transactions.grn as grn')
                                                                ->get();
            return view('purchase.purchase-order-billing.create')->with(compact('purchaseOrderTransactionDetails','clients'));
        }catch(\Exception $e){
            $data = [
                'action' => 'Get PO billing Create View',
                'exception' => $e->getMessage()
            ];
            Log::cirtical(json_encode($data));
            abort(500);
        }
    }

    public function getProjectSites(Request $request){
        try{
            $projectSites = ProjectSite::where('project_id',$request->project_id)->select('id','name')->get();
            $response = array();
            foreach($projectSites as $projectSite){
                $response[] = '<option value="'.$projectSite['id'].'">'.$projectSite['name'].'</option>';
            }
            $status = 200;
        }catch (\Exception $e){
            $data = [
                'action' => 'Get PO billing Project Sites',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::cirtical(json_encode($data));
            $response = array();
            $status = 500;
        }
        return response()->json($response,$status);
    }

    public function getPurchaseOrders(Request $request){
        try{
            $purchaseOrders = PurchaseOrder::join('purchase_requests','purchase_requests.id','=','purchase_orders.purchase_request_id')
                                        ->join('purchase_order_transactions','purchase_order_transactions.purchase_order_id','=','purchase_orders.id')
                                        ->join('purchase_order_transaction_statuses','purchase_order_transaction_statuses.id','=','purchase_order_transactions.purchase_order_transaction_status_id')
                                        ->where('purchase_order_transaction_statuses.slug','bill-pending')
                                        ->where('purchase_requests.project_site_id',$request->project_site_id)
                                        ->select('purchase_orders.id as id','purchase_orders.format_id as format_id')
                                        ->distinct('format_id')
                                        ->get();
            $response = array();
            $status = 200;
            foreach ($purchaseOrders as $purchaseOrder){
                $response[] = '<option value="'.$purchaseOrder['id'].'">'.$purchaseOrder['format_id'].'</option>';
            }
        }catch (\Exception $e){
            $data = [
                'action' => 'Get PO billing Purchase orders',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::cirtical(json_encode($data));
            $response = array();
            $status = 500;
        }
        return response()->json($response,$status);
    }



    public function getBillPendingTransactions(Request $request){
        try{
            $status = 200;
            $response = array();
            $billPendingTransactions = PurchaseOrderTransaction::join('purchase_order_transaction_statuses','purchase_order_transactions.purchase_order_transaction_status_id','=','purchase_order_transaction_statuses.id')
                                                ->where('purchase_order_transaction_statuses.slug','bill-pending')
                                                ->where('purchase_order_transactions.purchase_order_id',$request->purchase_order_id)
                                                ->select('purchase_order_transactions.id as id','purchase_order_transactions.grn as grn')
                                                ->get();
            if(count($billPendingTransactions) > 0){
                foreach($billPendingTransactions as $purchaseOrderTransaction){
                    $response[] = '<li><input type="checkbox" class="transaction-select" name="transaction_id[]" value="'.$purchaseOrderTransaction['id'].'"><label class="control-label" style="margin-left: 0.5%;">'. $purchaseOrderTransaction['grn'].' </label></li>';
                }
            }else{
                $status = 204;
            }

        }catch (\Exception $e){
            $data = [
                'action' => 'Get PO billing pending bill transactions',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::cirtical(json_encode($data));
            $response = array();
            $status = 500;
        }
        return response()->json($response,$status);
    }

    public function getTransactionSubtotal(Request $request){
        try{
            $amount = 0;
            $purchaseOrderTransactions = PurchaseOrderTransaction::whereIn('id',$request->transaction_id)->get();
            foreach($purchaseOrderTransactions as $purchaseOrderTransaction){
                foreach($purchaseOrderTransaction->purchaseOrderTransactionComponents as $purchaseOrderTransactionComponent){
                    $unitConversionRate = UnitHelper::unitConversion($purchaseOrderTransactionComponent->purchaseOrderComponent->unit_id,$purchaseOrderTransactionComponent->unit_id,$purchaseOrderTransactionComponent->purchaseOrderComponent->rate_per_unit);
                    if(!is_array($unitConversionRate)){
                        $amount += $purchaseOrderTransactionComponent->quantity * $unitConversionRate;
                    }
                }
            }
            $response = [
                'sub_total' => $amount
            ];
            $status = 200;
        }catch (\Exception $e){
            $data = [
                'action' => 'Get PO billing transactions subtotal',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::cirtical(json_encode($data));
            $response = array();
            $status = 500;
        }
        return response()->json($response,$status);
    }

    public function createBill(Request $request){
        try{
            dd($request->all());
            $purchaseOrderBillData = $request->except('_token','bill_images','transaction_id','sub_total');
            $purchaseOrderBill = PurchaseOrderBill::create($purchaseOrderBillData);
            if($request->has('bill_images')){
                foreach($request->bill_images as $image){

                }
            }
        }catch(\Exception $e){
            $data = [
                'action' => 'Create Purchase Order Bill',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }
}