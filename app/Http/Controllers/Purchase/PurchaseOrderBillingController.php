<?php

namespace App\Http\Controllers\Purchase;

use App\Client;
use App\Helper\UnitHelper;
use App\Http\Controllers\CustomTraits\Purchase\MaterialRequestTrait;
use App\PaymentType;
use App\ProjectSite;
use App\PurchaseOrder;
use App\PurchaseOrderBill;
use App\PurchaseOrderBillImage;
use App\PurchaseOrderBillTransactionRelation;
use App\PurchaseOrderPayment;
use App\PurchaseOrderTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

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
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getPurchaseOrders(Request $request){
        try{
            if(Session::has('global_project_site')){
                $projectSiteId = Session::get('global_project_site');
                $purchaseOrders = PurchaseOrder::join('purchase_requests','purchase_requests.id','=','purchase_orders.purchase_request_id')
                    ->join('purchase_order_transactions','purchase_order_transactions.purchase_order_id','=','purchase_orders.id')
                    ->join('purchase_order_transaction_statuses','purchase_order_transaction_statuses.id','=','purchase_order_transactions.purchase_order_transaction_status_id')
                    ->where('purchase_order_transaction_statuses.slug','bill-pending')
                    ->where('purchase_requests.project_site_id',$projectSiteId)
                    ->where('purchase_orders.format_id','ilike','%'.$request->keyword.'%')
                    ->where('purchase_orders.is_client_order','!=', true)
                    ->select('purchase_orders.id as id','purchase_orders.format_id as format_id','purchase_order_transactions.id as purchase_order_transaction_id','purchase_order_transactions.grn as grn')
                    ->distinct('format_id')
                    ->get();
            }else{
                $purchaseOrders = [];
            }

            $response = array();
            $status = 200;
            $iterator = 0;
            foreach ($purchaseOrders as $purchaseOrder){
                $response[$iterator]['format'] = $purchaseOrder['format_id'];
                $response[$iterator]['id'] = $purchaseOrder['id'];
                $response[$iterator]['grn'] = '<li><input type="checkbox" class="transaction-select" name="transaction_id[]" value="'.$purchaseOrder['purchase_order_transaction_id'].'"><label class="control-label" style="margin-left: 0.5%;">'. $purchaseOrder['grn'].' </label> <a href="javascript:void(0);" onclick="viewTransactionDetails('.$purchaseOrder['purchase_order_transaction_id'].')" class="btn blue btn-xs" style="margin-left: 2%">View Details </a></li>';
                $iterator++;
            }
        }catch (\Exception $e){
            $data = [
                'action' => 'Get PO billing Purchase orders',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            $response = array();
            $status = 500;
        }
        return response()->json($response,$status);
    }
    public function getBillPendingTransactions(Request $request){
        try{
            $status = 200;
            $response = array();
            if(Session::has('global_project_site')){
                $projectSiteId = Session::get('global_project_site');
                $billPendingTransactions = PurchaseOrderTransaction::join('purchase_order_transaction_statuses','purchase_order_transactions.purchase_order_transaction_status_id','=','purchase_order_transaction_statuses.id')
                    ->join('purchase_orders','purchase_orders.id','=','purchase_order_transactions.purchase_order_id')
                    ->join('purchase_requests','purchase_requests.id','=','purchase_orders.purchase_request_id')
                    ->where('purchase_requests.project_site_id', $projectSiteId)
                    ->where('purchase_orders.is_client_order','!=', true)
                    ->where('purchase_order_transaction_statuses.slug','bill-pending')
                    ->where('purchase_order_transactions.grn','ilike','%'.$request->keyword.'%')
                    ->select('purchase_order_transactions.id as id','purchase_order_transactions.grn as grn','purchase_order_transactions.purchase_order_id as purchase_order_id')
                    ->get();
            }else{
                $billPendingTransactions = PurchaseOrderTransaction::join('purchase_order_transaction_statuses','purchase_order_transactions.purchase_order_transaction_status_id','=','purchase_order_transaction_statuses.id')
                    ->join('purchase_orders','purchase_orders.id','=','purchase_order_transactions.purchase_order_id')
                    ->where('purchase_orders.is_client_order','!=', true)
                    ->where('purchase_order_transaction_statuses.slug','bill-pending')
                    ->where('purchase_order_transactions.grn','ilike','%'.$request->keyword.'%')
                    ->select('purchase_order_transactions.id as id','purchase_order_transactions.grn as grn','purchase_order_transactions.purchase_order_id as purchase_order_id')
                    ->get();
            }
            if(count($billPendingTransactions) > 0){
                $iterator = 0;
                foreach($billPendingTransactions as $purchaseOrderTransaction){
                    $response[$iterator]['list'] = '<li><input type="checkbox" class="transaction-select" name="transaction_id[]" value="'.$purchaseOrderTransaction['id'].'"><label class="control-label" style="margin-left: 0.5%;">'. $purchaseOrderTransaction['grn'].' </label><a href="javascript:void(0);" onclick="viewTransactionDetails('.$purchaseOrderTransaction['id'].')" class="btn blue btn-xs" style="margin-left: 2%">View Details </a></li>';
                    $response[$iterator]['purchase_order_id'] = $purchaseOrderTransaction['purchase_order_id'];
                    $response[$iterator]['id'] = $purchaseOrderTransaction['id'];
                    $response[$iterator]['grn'] = $purchaseOrderTransaction['grn'];
                    $iterator++;
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
            Log::critical(json_encode($data));
            $response = array();
            $status = 500;
        }
        return response()->json($response,$status);
    }

    public function getTransactionSubtotal(Request $request){
        try{
            $amount = 0;
            $taxAmount = 0;
            $purchaseOrderTransactions = PurchaseOrderTransaction::whereIn('id',$request->transaction_id)->get();
            foreach($purchaseOrderTransactions as $purchaseOrderTransaction){
                foreach($purchaseOrderTransaction->purchaseOrderTransactionComponents as $purchaseOrderTransactionComponent){
                    $purchaseOrderComponent = $purchaseOrderTransactionComponent->purchaseOrderComponent;
                    $unitConversionRate = UnitHelper::unitConversion($purchaseOrderTransactionComponent->purchaseOrderComponent->unit_id,$purchaseOrderTransactionComponent->unit_id,$purchaseOrderTransactionComponent->purchaseOrderComponent->rate_per_unit);
                    if(!is_array($unitConversionRate)){
                        $tempAmount = $purchaseOrderTransactionComponent->quantity * $unitConversionRate;
                        $amount += $tempAmount;
                        if($purchaseOrderComponent->cgst_percentage != null || $purchaseOrderComponent->cgst_percentage != ''){
                            $taxAmount += $tempAmount * ($purchaseOrderComponent->cgst_percent/100);
                        }
                        if($purchaseOrderComponent->sgst_percentage != null || $purchaseOrderComponent->sgst_percentage != ''){
                            $taxAmount += $tempAmount * ($purchaseOrderComponent->sgst_percent/100);
                        }
                        if($purchaseOrderComponent->igst_percentage != null || $purchaseOrderComponent->igst_percentage != ''){
                            $taxAmount += $tempAmount * ($purchaseOrderComponent->igst_percent/100);
                        }
                    }
                }
            }
            $response = [
                'sub_total' => $amount,
                'tax_amount' => $taxAmount
            ];
            $status = 200;
        }catch (\Exception $e){
            $data = [
                'action' => 'Get PO billing transactions subtotal',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            $response = array();
            $status = 500;
        }
        return response()->json($response,$status);
    }

    use MaterialRequestTrait;
    public function createBill(Request $request){
        try{
            $purchaseOrderBillData = $request->except('_token','project_site_id','bill_images','transaction_id','sub_total','transaction_grn','purchase_order_format');
            $today = Carbon::now();
            $purchaseOrderBillCount = PurchaseOrderBill::whereDate('created_at', $today)->count();
            $purchaseOrderBillData['bill_number'] = $this->getPurchaseIDFormat('purchase-order-bill',$request->project_site_id,$today,(++$purchaseOrderBillCount));
            $purchaseOrderBill = PurchaseOrderBill::create($purchaseOrderBillData);
            $purchaseOrderDirectoryName = sha1($request->purchase_order_id);
            $purchaseBillDirectoryName = sha1($purchaseOrderBill->id);
            $imageUploadPath = public_path().env('PURCHASE_ORDER_IMAGE_UPLOAD').DIRECTORY_SEPARATOR.$purchaseOrderDirectoryName.DIRECTORY_SEPARATOR.'bills'.DIRECTORY_SEPARATOR.$purchaseBillDirectoryName;
            if (!file_exists($imageUploadPath)) {
                File::makeDirectory($imageUploadPath, $mode = 0777, true, true);
            }
            if($request->has('bill_images')){
                foreach($request->bill_images as $billImage){
                    $imageArray = explode(';',$billImage);
                    $image = explode(',',$imageArray[1])[1];
                    $pos  = strpos($billImage, ';');
                    $type = explode(':', substr($billImage, 0, $pos))[1];
                    $extension = explode('/',$type)[1];
                    $filename = mt_rand(1,10000000000).sha1(time()).".{$extension}";
                    $fileFullPath = $imageUploadPath.DIRECTORY_SEPARATOR.$filename;
                    $billImageData = [
                        'purchase_order_bill_id' => $purchaseOrderBill->id,
                        'name' => $filename,
                    ];
                    file_put_contents($fileFullPath,base64_decode($image));
                    PurchaseOrderBillImage::create($billImageData);
                }
            }
            $purchaseOrderBillTransactionRelationData = [
                'purchase_order_bill_id' => $purchaseOrderBill->id
            ];
            foreach($request->transaction_id as $transactionId){
                $purchaseOrderBillTransactionRelationData['purchase_order_transaction_id'] = $transactionId;
                PurchaseOrderBillTransactionRelation::create($purchaseOrderBillTransactionRelationData);
            }
            $request->session()->flash('success','Purchase Order Bill Created Successfully');
            return redirect('/purchase/purchase-order-bill/create');
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

    public function listing(Request $request){
        try{
            $records = array();
            $status = 200;
            $records['data'] = array();
            $records["draw"] = intval($request->draw);
            if(Session::has('global_project_site')){
                $projectSiteId = Session::get('global_project_site');
                $purchaseOrderBillData = PurchaseOrderBill::join('purchase_orders','purchase_orders.id','=','purchase_order_bills.purchase_order_id')
                                                ->join('purchase_requests','purchase_requests.id','=','purchase_orders.purchase_request_id')
                                                ->where('purchase_requests.project_site_id', $projectSiteId)
                                                ->select('purchase_order_bills.id as id','purchase_order_bills.bill_number as bill_number','purchase_order_bills.amount as amount','purchase_orders.format_id as format_id')
                                                ->orderBy('id','desc')
                                                ->get();
            }else{
                $purchaseOrderBillData = PurchaseOrderBill::join('purchase_orders','purchase_orders.id','=','purchase_order_bills.purchase_order_id')
                                                ->select('purchase_order_bills.id as id','purchase_order_bills.bill_number as bill_number','purchase_order_bills.amount as amount','purchase_orders.format_id as format_id')
                                                ->orderBy('id','desc')
                                                ->get();
            }
            $records["recordsFiltered"] = $records["recordsTotal"] = count($purchaseOrderBillData);
            if($request->length == -1){
                $length = $records["recordsTotal"];
            }else{
                $length = $request->length;
            }
            $user = Auth::user();
            for($iterator = 0,$pagination = $request->start; $iterator < $length && $iterator < count($purchaseOrderBillData); $iterator++,$pagination++ ){
                if($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin' || $user->customHasPermission('create-purchase-bill') || $user->customHasPermission('edit-purchase-bill')){
                    $editButton = '<div id="sample_editable_1_new" class="btn btn-small blue" >
                        <a href="/purchase/purchase-order-bill/edit/'.$purchaseOrderBillData[$pagination]['id'].'" style="color: white"> Edit
                    </div>';
                }else{
                    $editButton = '';
                }
                $records['data'][] = [
                    $purchaseOrderBillData[$pagination]['bill_number'],
                    $purchaseOrderBillData[$pagination]['format_id'],
                    $purchaseOrderBillData[$pagination]['amount'],
                    $editButton
                ];
            }
        }catch (\Exception $e){
            $data = [
                'action' => 'Get PO billing listings',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            $records = array();
            $status = 500;
        }
        return response()->json($records,$status);
    }

    public function getEditView(Request $request,$purchaseOrderBill){
        try{
            $grn = '';
            foreach($purchaseOrderBill->purchaseOrderTransactionRelation as $transactionRelation){
                $grn .= $transactionRelation->purchaseOrderTransaction->grn.', ';
            }
            $purchaseOrderBillImagePaths = array();
            $purchaseOrderBillImages = PurchaseOrderBillImage::where('purchase_order_bill_id',$purchaseOrderBill->id)->get();
            $purchaseOrderDirectoryName = sha1($purchaseOrderBill->purchase_order_id);
            $purchaseBillDirectoryName = sha1($purchaseOrderBill->id);
            $imageUploadPath = env('PURCHASE_ORDER_IMAGE_UPLOAD').DIRECTORY_SEPARATOR.$purchaseOrderDirectoryName.DIRECTORY_SEPARATOR.'bills'.DIRECTORY_SEPARATOR.$purchaseBillDirectoryName;
            $subTotalAmount = 0;
            foreach($purchaseOrderBill->purchaseOrderTransactionRelation as $purchaseOrderTransactionRelation){
                foreach($purchaseOrderTransactionRelation->purchaseOrderTransaction->purchaseOrderTransactionComponents as $purchaseOrderTransactionComponent){
                    $unitConversionRate = UnitHelper::unitConversion($purchaseOrderTransactionComponent->purchaseOrderComponent->unit_id,$purchaseOrderTransactionComponent->unit_id,$purchaseOrderTransactionComponent->purchaseOrderComponent->rate_per_unit);
                    if(!is_array($unitConversionRate)){
                        $subTotalAmount += $purchaseOrderTransactionComponent->quantity * $unitConversionRate;
                    }
                }
            }
            foreach($purchaseOrderBillImages as $image){
                $purchaseOrderBillImagePaths[] = $imageUploadPath.DIRECTORY_SEPARATOR.$image['name'];
            }
            $paymentTypes = PaymentType::select('id','name')->get();
            return view('purchase.purchase-order-billing.edit')->with(compact('purchaseOrderBill','purchaseOrderBillImagePaths','subTotalAmount','paymentTypes','grn'));
        }catch(\Exception $e){
            $data = [
                'action' => 'Get PO billing get edit view',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function paymentListing(Request $request, $purchaseOrderBillId){
        try{
            $records = array();
            $status = 200;
            $records['data'] = array();
            $records["draw"] = intval($request->draw);
            $purchaseOrderPaymentData = PurchaseOrderPayment::where('purchase_order_bill_id',$purchaseOrderBillId)->orderBy('created_at','desc')->get();
            $records["recordsFiltered"] = $records["recordsTotal"] = count($purchaseOrderPaymentData);
            for($iterator = 0,$pagination = $request->start; $iterator < $request->length && $iterator < count($purchaseOrderPaymentData); $iterator++,$pagination++ ){
                if($purchaseOrderPaymentData[$pagination]->paymentType == null){
                    $paymentType = 'Advance';
                }else{
                    $paymentType = $purchaseOrderPaymentData[$pagination]->paymentType->name;
                }
                $records['data'][] = [
                    date('d M Y',strtotime($purchaseOrderPaymentData[$pagination]['created_at'])),
                    $purchaseOrderPaymentData[$pagination]['amount'],
                    $paymentType,
                    $purchaseOrderPaymentData[$pagination]['reference_number'],
                ];
            }
        }catch (\Exception $e){
            $data = [
                'action' => 'Get PO billing Payment Listing',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            $status = 500;
            $records = array();
        }
        return response()->json($records,$status);
    }

    public function createPayment(Request $request){
        try{
            $purchaseOrderPaymentData = $request->except('_token','is_advance','payment_id');
            if($request->has('is_advance')){
                $purchaseOrderPaymentData['is_advance'] = true;
                $purchaseOrderId =PurchaseOrderBill::join('purchase_orders','purchase_orders.id','=','purchase_order_bills.purchase_order_id')
                                        ->where('purchase_order_bills.id', $request->purchase_order_bill_id)
                                        ->pluck('purchase_orders.id as id')
                                        ->first();
                $purchaseOrder = PurchaseOrder::findOrFail($purchaseOrderId);
                if($purchaseOrder->balance_advance_amount >= $request->amount){
                    $balanceAdvanceAmount = $purchaseOrder->balance_advance_amount - $request->amount;
                    $purchaseOrder->update(['balance_advance_amount' => $balanceAdvanceAmount]);
                }else{
                    $request->session()->flash('error','Payment Amount is greater than balance advance amount');
                    return redirect('/purchase/purchase-order-bill/edit/'.$request->purchase_order_bill_id);
                }
            }else{
                $purchaseOrderPaymentData['is_advance'] = false;
                $purchaseOrderPaymentData['payment_id'] = $request->payment_id;
            }
            $purchaseOrderPayment = PurchaseOrderPayment::create($purchaseOrderPaymentData);
            $request->session()->flash('success','Purchase Order Payment Created Successfully');
            return redirect('/purchase/purchase-order-bill/edit/'.$request->purchase_order_bill_id);
        }catch (\Exception $e){
            $data = [
                'action' => 'Get PO billing Create Payment',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }
}