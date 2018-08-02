<?php

namespace App\Http\Controllers\Purchase;

use App\BankInfo;
use App\Client;
use App\Helper\MaterialProductHelper;
use App\Helper\UnitHelper;
use App\Http\Controllers\CustomTraits\PeticashTrait;
use App\Http\Controllers\CustomTraits\Purchase\MaterialRequestTrait;
use App\MaterialRequestComponentVersion;
use App\PaymentType;
use App\Project;
use App\ProjectSite;
use App\PurchaseOrder;
use App\PurchaseOrderBill;
use App\PurchaseOrderBillImage;
use App\PurchaseOrderBillTransactionRelation;
use App\PurchaseOrderComponent;
use App\PurchaseOrderPayment;
use App\PurchaseOrderTransaction;
use App\PurchaseOrderTransactionStatus;
use App\PurchaseRequest;
use App\Vendor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class PurchaseOrderBillingController extends Controller
{
    use PeticashTrait;
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

    public function getManageViewForPendingPOBill(Request $request){
        try{
            return view('purchase.pending-po-bills.manage');
        }catch(\Exception $e){
            $data = [
                'action' => 'Manage Pending PO Bills',
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

    public function getManageViewForPendingPOBillListing(Request $request){
        try{

            $status = 200;
            $billPendingTransactions = array();
            $ids = PurchaseOrderTransaction::join('purchase_order_transaction_statuses', 'purchase_order_transactions.purchase_order_transaction_status_id', '=', 'purchase_order_transaction_statuses.id')
                ->join('purchase_orders', 'purchase_orders.id', '=', 'purchase_order_transactions.purchase_order_id')
                ->where('purchase_orders.is_client_order', '!=', true)
                ->where('purchase_order_transaction_statuses.slug', 'bill-pending')
                ->pluck('purchase_order_transactions.id');

            $filterFlag = true;

            if($request->has('project_name') && $request->project_name != '' && $filterFlag == true){
                $ids = PurchaseOrderTransaction::join('purchase_orders','purchase_orders.id','=','purchase_order_transactions.purchase_order_id')
                    ->join('purchase_requests','purchase_requests.id','=','purchase_orders.purchase_request_id')
                    ->join('project_sites','project_sites.id','=','purchase_requests.project_site_id')
                    ->join('projects','projects.id','=','project_sites.project_id')
                    ->where('projects.name', 'ilike','%'.$request->project_name.'%')
                    ->whereIn('purchase_order_transactions.id',$ids)
                    ->pluck('purchase_order_transactions.id');
                if(count($ids) <= 0) {
                    $filterFlag = false;
                }

            }

            if($request->has('po_number') && $request->po_number != '' && $filterFlag == true){
                $ids = PurchaseOrderTransaction::join('purchase_orders','purchase_orders.id','=','purchase_order_transactions.purchase_order_id')
                    ->where('purchase_orders.format_id','ilike','%'.$request->po_number.'%')
                    ->whereIn('purchase_order_transactions.id',$ids)
                    ->pluck('purchase_order_transactions.id');
                if(count($ids) <= 0) {
                    $filterFlag = false;
                }

            }

            if($request->has('grn_number') && $request->grn_number != '' && $filterFlag == true){
                $ids = PurchaseOrderTransaction::where('purchase_order_transactions.grn','ilike','%'.$request->grn_number.'%')
                    ->whereIn('purchase_order_transactions.id',$ids)
                    ->pluck('purchase_order_transactions.id');
                if(count($ids) <= 0) {
                    $filterFlag = false;
                }

            }

            if($request->has('vendor_name') && $request->vendor_name != '' && $filterFlag == true){
                $ids = PurchaseOrderTransaction::join('purchase_orders','purchase_orders.id','=','purchase_order_transactions.purchase_order_id')
                    ->join('vendors','vendors.id','=','purchase_orders.vendor_id')
                    ->where('vendors.company','ilike','%'.$request->vendor_name.'%')
                    ->whereIn('purchase_order_transactions.id',$ids)
                    ->pluck('purchase_order_transactions.id');
                if(count($ids) <= 0) {
                    $filterFlag = false;
                }

            }

           if($filterFlag) {
                $billPendingTransactions = PurchaseOrderTransaction::join('purchase_order_transaction_statuses', 'purchase_order_transactions.purchase_order_transaction_status_id', '=', 'purchase_order_transaction_statuses.id')
                    ->join('purchase_orders', 'purchase_orders.id', '=', 'purchase_order_transactions.purchase_order_id')
                    ->where('purchase_orders.is_client_order', '!=', true)
                    ->where('purchase_order_transaction_statuses.slug', 'bill-pending')
                    ->whereIn('purchase_order_transactions.id',$ids)
                    ->get()->toArray();
            }

            $iTotalRecords = count($billPendingTransactions);
            $records = array();
            $records['data'] = array();
            $end = $request->length < 0 ? count($billPendingTransactions) : $request->length;

            if(count($billPendingTransactions) > 0){
                    for($iterator = 0, $pagination = $request->start; $iterator < $end && $pagination < count($billPendingTransactions); $iterator++,$pagination++ ){
                        $purchaseOrder = PurchaseOrder::where('id',($billPendingTransactions[$iterator]['purchase_order_id']))->first()->toArray();
                        $clientvendorInfo = Vendor::where('id',$purchaseOrder['vendor_id'])->first(['company','mobile'])->toArray();
                        $projectSite = PurchaseRequest::join('project_sites','project_sites.id','=','purchase_requests.project_site_id')
                            ->join('projects','projects.id','=','project_sites.project_id')
                            ->where('purchase_requests.id', $billPendingTransactions[$iterator]['purchase_request_id'])
                            ->select('projects.name')
                            ->first()->toArray();

                        $poNumber = PurchaseOrder::where('id',$billPendingTransactions[$iterator]['purchase_order_id'])->pluck('format_id');

                        $firstname = PurchaseOrderTransaction::join('purchase_orders','purchase_orders.id','=','purchase_order_transactions.purchase_order_id')
                            ->join('purchase_requests','purchase_requests.id','=','purchase_orders.purchase_request_id')
                            ->join('purchase_request_components','purchase_request_components.purchase_request_id','=','purchase_requests.id')
                            ->join('material_request_components','material_request_components.id','=','purchase_request_components.material_request_component_id')
                            ->where('purchase_order_transactions.purchase_order_id', $billPendingTransactions[$iterator]['purchase_order_id'])
                            ->pluck('material_request_components.name')
                            ->first();

                        $records['data'][$iterator] = [
                            $projectSite['name'],
                            $poNumber,
                            $billPendingTransactions[$iterator]['grn'],
                            $clientvendorInfo['company'],
                            $firstname,
                            $clientvendorInfo['mobile']
                        ];

                    }
            }

            $records["draw"] = intval($request->draw);
            $records["recordsTotal"] = $iTotalRecords;
            $records["recordsFiltered"] = $iTotalRecords;
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
                    ->where(function($query){
                        $query->where('purchase_orders.is_client_order','=',null)
                              ->orWhere('purchase_orders.is_client_order','=',false);
                    })
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
                if(!array_key_exists($purchaseOrder['id'], $response)){
                    $response[$purchaseOrder['id']]['format'] = $purchaseOrder['format_id'];
                    $response[$purchaseOrder['id']]['id'] = $purchaseOrder['id'];
                    $response[$purchaseOrder['id']]['grn'] = '';
                }
                $response[$purchaseOrder['id']]['grn'] .= '<li><input type="checkbox" class="transaction-select" name="transaction_id[]" value="'.$purchaseOrder['purchase_order_transaction_id'].'"><label class="control-label" style="margin-left: 0.5%;">'. $purchaseOrder['grn'].' </label> <a href="javascript:void(0);" onclick="viewTransactionDetails('.$purchaseOrder['purchase_order_transaction_id'].')" class="btn blue btn-xs" style="margin-left: 2%">View Details </a></li>';
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
                    ->where(function($query){
                        $query->where('purchase_orders.is_client_order','=',null)
                            ->orWhere('purchase_orders.is_client_order','=',false);
                    })
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
                    $purchaseOrder = PurchaseOrder::where('id',($purchaseOrderTransaction['purchase_order_id']))->first()->toArray();
                    if ($purchaseOrder['is_client_order']) {
                        $clientvendorInfo = Client::where('id',$purchaseOrder['client_id'])->first(['company','mobile'])->toArray();
                    } else {
                        $clientvendorInfo = Vendor::where('id',$purchaseOrder['vendor_id'])->first(['company','mobile'])->toArray();
                    }
                    $response[$iterator]['list'] = '<li><input type="checkbox" class="transaction-select" name="transaction_id[]" value="'.$purchaseOrderTransaction['id'].'"><label class="control-label" style="margin-left: 0.5%;">'. $purchaseOrderTransaction['grn'].' </label><a href="javascript:void(0);" onclick="viewTransactionDetails('.$purchaseOrderTransaction['id'].')" class="btn blue btn-xs" style="margin-left: 2%">View Details </a></li>';
                    $response[$iterator]['purchase_order_id'] = $purchaseOrderTransaction['purchase_order_id'];
                    $response[$iterator]['id'] = $purchaseOrderTransaction['id'];
                    $response[$iterator]['grn'] = $purchaseOrderTransaction['grn'].' : '.$clientvendorInfo['company'].' ('.$clientvendorInfo['mobile'].')';
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
            $transportationAmount = $totalTransportationTaxAmount = 0;
            $purchaseOrderTransactions = PurchaseOrderTransaction::whereIn('id',$request->transaction_id)->get();
            $purchaseOrderId = $purchaseOrderTransactions->pluck('purchase_order_id')->first();
            foreach($purchaseOrderTransactions as $purchaseOrderTransaction){
                foreach($purchaseOrderTransaction->purchaseOrderTransactionComponents as $purchaseOrderTransactionComponent){
                    $purchaseOrderComponent = $purchaseOrderTransactionComponent->purchaseOrderComponent;
                    $unitConversionRate = UnitHelper::unitConversion($purchaseOrderTransactionComponent->purchaseOrderComponent->unit_id,$purchaseOrderTransactionComponent->unit_id,$purchaseOrderTransactionComponent->purchaseOrderComponent->rate_per_unit);
                    if(!is_array($unitConversionRate)){
                        $tempAmount = round(($purchaseOrderTransactionComponent->quantity * $unitConversionRate),3);
                        $amount += $tempAmount;
                        if($purchaseOrderComponent->cgst_percentage != null || $purchaseOrderComponent->cgst_percentage != ''){
                            $taxAmount += round(($tempAmount * ($purchaseOrderComponent->cgst_percentage/100)),3);
                        }
                        if($purchaseOrderComponent->sgst_percentage != null || $purchaseOrderComponent->sgst_percentage != ''){
                            $taxAmount += round(($tempAmount * ($purchaseOrderComponent->sgst_percentage/100)),3);
                        }
                        if($purchaseOrderComponent->igst_percentage != null || $purchaseOrderComponent->igst_percentage != ''){
                            $taxAmount += round(($tempAmount * ($purchaseOrderComponent->igst_percentage/100)),3);
                        }
                    }
                    $purchaseOrderRequestComponent = $purchaseOrderComponent->purchaseOrderRequestComponent;
                    $transportationAmount += $purchaseOrderRequestComponent->transportation_amount;
                    $transportation_cgst_amount = round((($purchaseOrderRequestComponent->transportation_amount * $purchaseOrderRequestComponent->transportation_cgst_percentage) /100),3);
                    $transportation_sgst_amount = round((($purchaseOrderRequestComponent->transportation_amount * $purchaseOrderRequestComponent->transportation_sgst_percentage) / 100),3);
                    $transportation_igst_amount = round((($purchaseOrderRequestComponent->transportation_amount * $purchaseOrderRequestComponent->transportation_igst_percentage) / 100),3);
                    $transportationTaxAmount = round(($transportation_cgst_amount + $transportation_sgst_amount + $transportation_igst_amount),3);
                    $totalTransportationTaxAmount += $transportationTaxAmount;
                }
            }
            $purchaseOrderComponents = PurchaseOrderComponent::where('purchase_order_id',$purchaseOrderId)->get();
            $highestTaxAmount = $purchaseOrderComponents->max(function ($purchaseOrderComponent) {
                return ($purchaseOrderComponent->cgst_percentage + $purchaseOrderComponent->sgst_percentage + $purchaseOrderComponent->igst_percentage);
            });
            $response = [
                'sub_total' => $amount,
                'tax_amount' => $taxAmount,
                'transportation_amount' => $transportationAmount,
                'transportation_tax_amount' => $totalTransportationTaxAmount,
                'extra_tax_percentage' => $highestTaxAmount
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
            $user = Auth::user();
            $purchaseOrderBillData = $request->except('_token','project_site_id','bill_images','transaction_id','sub_total','transaction_grn','purchase_order_format','is_transportation','transportation_total','transportation_tax_amount','extra_amount');
            $today = Carbon::now();
            $purchaseOrderBillCount = PurchaseOrderBill::whereDate('created_at', $today)->count();
            $purchaseOrderBillData['extra_amount'] = round($request['extra_amount'],3);
            $purchaseOrderBillData['bill_number'] = $this->getPurchaseIDFormat('purchase-order-bill',$request->project_site_id,$today,(++$purchaseOrderBillCount));
            $purchaseOrderBillData['transportation_tax_amount'] = ($request['is_transportation'] == 'on') ? $request['transportation_tax_amount'] : 0;
            $purchaseOrderBillData['transportation_total_amount'] = ($request['is_transportation'] == 'on') ? $request['transportation_total'] : 0;
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
                $purchaseOrderTransaction = PurchaseOrderTransaction::where('id',$transactionId)->first();
                $billGeneratedStatusId = PurchaseOrderTransactionStatus::where('slug','bill-generated')->pluck('id')->first();
                $purchaseOrderTransaction->update([
                    'purchase_order_transaction_status_id' => $billGeneratedStatusId
                ]);
                foreach($purchaseOrderTransaction->purchaseOrderTransactionComponents as $key => $purchaseOrderTransactionComponent){
                    $materialRequestComponentVersion['material_request_component_id'] = $purchaseOrderTransactionComponent->purchaseOrderComponent->purchaseRequestComponent->materialRequestComponent->id;
                    $materialRequestComponentVersion['purchase_order_transaction_status_id'] = $billGeneratedStatusId;
                    $materialRequestComponentVersion['user_id'] = $user['id'];
                    $materialRequestComponentVersion['quantity'] = $purchaseOrderTransactionComponent['quantity'];
                    $materialRequestComponentVersion['unit_id'] = $purchaseOrderTransactionComponent['unit_id'];
                    $materialRequestComponentVersion['remark'] = $request['remark'];
                    MaterialRequestComponentVersion::create($materialRequestComponentVersion);
                }
            }
            $request->session()->flash('success','Purchase Order Bill Created Successfully');
            return redirect('/purchase/purchase-order-bill/manage');
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

            $postDataArray = array();
            $purchaseOrderBillIds = PurchaseOrderBill::pluck('id')->toArray();
            $filterFlag = true;
            if($request->has('postdata')){
                $postdata = $request['postdata'];
                if($postdata != null) {
                    $mstr = explode(",",$request['postdata']);
                    foreach($mstr as $nstr)
                    {
                        $narr = explode("=>",$nstr);
                        $narr[0] = str_replace("\x98","",$narr[0]);
                        $ytr[1] = $narr[1];
                        $postDataArray[$narr[0]] = $ytr[1];
                    }
                }
                $start_date = $postDataArray['start_date'];
                $end_date = $postDataArray['end_date'];
                $purchaseOrderBillIds = PurchaseOrderBill::whereIn('id',$purchaseOrderBillIds)
                    ->whereBetween('created_at', [$start_date, $end_date])
                    ->pluck('id')
                    ->toArray();
                if(count($purchaseOrderBillIds) <= 0){
                    $filterFlag = false;
                }
            }
            if($request->has('project_name') && $request->project_name != '' && $filterFlag == true){
                $purchaseOrderBillIds = PurchaseOrderBill::join('purchase_orders','purchase_orders.id','=','purchase_order_bills.purchase_order_id')
                    ->join('purchase_requests','purchase_requests.id','=','purchase_orders.purchase_request_id')
                    ->join('project_sites','purchase_requests.project_site_id','=','project_sites.id')
                    ->join('projects','project_sites.project_id','=','projects.id')
                    ->where('projects.name','ilike','%'.$request->project_name.'%')
                    ->pluck('purchase_order_bills.id')->toArray();
                if(count($purchaseOrderBillIds) <= 0){
                    $filterFlag = false;
                }
            }
            if($request->has('system_bill_number') && $request->system_bill_number != '' && $filterFlag == true){
                $purchaseOrderBillIds = PurchaseOrderBill::join('purchase_orders','purchase_orders.id','=','purchase_order_bills.purchase_order_id')
                    ->where('purchase_order_bills.bill_number','ilike','%'.$request->system_bill_number.'%')
                    ->pluck('purchase_order_bills.id')->toArray();
                if(count($purchaseOrderBillIds) <= 0){
                    $filterFlag = false;
                }
            }
            if($request->has('bill_number') && $request->bill_number != '' && $filterFlag == true){
                $purchaseOrderBillIds = PurchaseOrderBill::join('purchase_orders','purchase_orders.id','=','purchase_order_bills.purchase_order_id')
                    ->where('purchase_order_bills.vendor_bill_number','ilike','%'.$request->bill_number.'%')
                    ->pluck('purchase_order_bills.id')->toArray();
                if(count($purchaseOrderBillIds) <= 0){
                    $filterFlag = false;
                }
            }
            if($request->has('vendor_name') && $request->vendor_name != '' && $filterFlag == true){
                $purchaseOrderBillIds = PurchaseOrderBill::join('purchase_orders','purchase_orders.id','=','purchase_order_bills.purchase_order_id')
                                            ->join('vendors','vendors.id','=','purchase_orders.vendor_id')
                                            ->whereIn('purchase_order_bills.id', $purchaseOrderBillIds)
                                            ->where('vendors.company','ilike','%'.$request->vendor_name.'%')
                                            ->pluck('purchase_order_bills.id')->toArray();
                if(count($purchaseOrderBillIds) <= 0){
                    $filterFlag = false;
                }
            }
            if($request->has('bill_date') && $request->bill_date != '' && $filterFlag == true){
                $purchaseOrderBillIds = PurchaseOrderBill::whereIn('id', $purchaseOrderBillIds)
                                            ->whereDate('bill_date',$request->bill_date)
                                            ->pluck('id')->toArray();
                if(count($purchaseOrderBillIds) <= 0){
                    $filterFlag = false;
                }
            }
            if($filterFlag == true){
                $purchaseOrderBillData = PurchaseOrderBill::join('purchase_orders','purchase_orders.id','=','purchase_order_bills.purchase_order_id')
                    ->whereIn('purchase_order_bills.id', $purchaseOrderBillIds)
                    ->select('purchase_orders.id as purchase_order_id','purchase_order_bills.id as id','purchase_order_bills.bill_number as serial_number','purchase_order_bills.created_at as created_at','purchase_order_bills.bill_date as bill_date','purchase_order_bills.vendor_bill_number as vendor_bill_number','purchase_orders.vendor_id as vendor_id','purchase_order_bills.transportation_tax_amount as transportation_tax_amount','purchase_order_bills.extra_tax_amount as extra_tax_amount','purchase_order_bills.tax_amount as tax_amount','purchase_order_bills.amount as amount')
                    ->orderBy('id','desc')
                    ->get();
            }else{
                $purchaseOrderBillData = array();
            }


            $user = Auth::user();
            $total = 0;
            $billTotals = 0;
            $billPaidAmount = 0;
            if ($request->has('get_total')) {
                if ($filterFlag) {
                    $total = $purchaseOrderBillData->sum('amount');
                    $paidAmount = PurchaseOrderPayment::whereIn('purchase_order_bill_id', $purchaseOrderBillData->pluck('id'))->sum('amount');
                    $billTotals = $total - $paidAmount;
                    $billPaidAmount = PurchaseOrderPayment::whereIn('purchase_order_bill_id', $purchaseOrderBillData->pluck('id'))->sum('amount');;
                }
                $records['total'] = round($total,3);
                $records['billtotal'] = round($billTotals,3);
                $records['paidtotal'] = round($billPaidAmount,3);
            } else {
                $records = array();
                $records["recordsFiltered"] = $records["recordsTotal"] = count($purchaseOrderBillData);
                $records['data'] = array();
                $records["draw"] = intval($request->draw);
                if($request->length == -1){
                    $length = $records["recordsTotal"];
                }else{
                    $length = $request->length;
                }
                for($iterator = 0,$pagination = $request->start; $iterator < $length && $pagination < count($purchaseOrderBillData); $iterator++,$pagination++ ){
                    $taxAmount = round(($purchaseOrderBillData[$pagination]['transportation_tax_amount'] + $purchaseOrderBillData[$pagination]['extra_tax_amount'] + $purchaseOrderBillData[$pagination]['tax_amount']),3);
                    $basicAmount = round(($purchaseOrderBillData[$pagination]['amount'] - $taxAmount),3);
                    $paidAmount = round((PurchaseOrderPayment::where('purchase_order_bill_id', $purchaseOrderBillData[$pagination]['id'])->sum('amount')),3);
                    $pendingAmount = round(($purchaseOrderBillData[$pagination]['amount'] - $paidAmount),3);
                    $vendorName = Vendor::where('id', $purchaseOrderBillData[$pagination]['vendor_id'])->pluck('company')->first();
                    $entryDate = '';
                    if(isset($purchaseOrderBillData[$pagination]['bill_date'])){
                        $entryDate = date('j M Y',strtotime($purchaseOrderBillData[$pagination]['bill_date']));
                    }
                    if($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin' || $user->customHasPermission('create-purchase-bill') || $user->customHasPermission('edit-purchase-bill')){
                        $editButton = '<div id="sample_editable_1_new" class="btn btn-small blue" >
                        <a href="/purchase/purchase-order-bill/edit/'.$purchaseOrderBillData[$pagination]['id'].'" style="color: white"> Edit
                    </div>';
                    }else{
                        $editButton = '';
                    }
                    $projectName = Project::join('project_sites','project_sites.project_id','=','projects.id')
                        ->join('purchase_requests','purchase_requests.project_site_id','=','project_sites.id')
                        ->join('purchase_orders','purchase_requests.id','=','purchase_orders.purchase_request_id')
                        ->where('purchase_orders.id',$purchaseOrderBillData[$pagination]['purchase_order_id'])
                        ->pluck('projects.name')->first();
                    $records['data'][] = [
                        $projectName,
                        $purchaseOrderBillData[$pagination]['serial_number'],
                        date('j M Y',strtotime($purchaseOrderBillData[$pagination]['created_at'])),
                        $entryDate,
                        $purchaseOrderBillData[$pagination]['vendor_bill_number'],
                        $vendorName,
                        $basicAmount,
                        $taxAmount,
                        $purchaseOrderBillData[$pagination]['amount'],
                        $pendingAmount,
                        $paidAmount,
                        $editButton
                    ];
                }
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
            $purchaseOrderPayment = $purchaseOrderBill->purchaseOrderPayment;
            if(count($purchaseOrderPayment) > 0){
                $transactionEditAccess = false;
            }else{
                $transactionEditAccess = true;
            }
            $paymentRemainingAmount = $purchaseOrderBill['amount'] - $purchaseOrderPayment->sum('amount');
            $paymentTillToday = $purchaseOrderBill->purchaseOrder->total_advance_amount + $purchaseOrderBill->purchaseOrderPayment->where('is_advance',false)->sum('amount');
            $banks = BankInfo::where('is_active',true)->select('id','bank_name','balance_amount')->get();
            $paymentTypes = PaymentType::whereIn('slug',['cheque','neft','rtgs','internet-banking'])->select('id','name')->get();
            $statistics = $this->getSiteWiseStatistics();
            $cashAllowedLimit = ($statistics['remainingAmount'] > 0) ? $statistics['remainingAmount'] : 0 ;
            $purchaseOrderComponents = PurchaseOrderComponent::where('purchase_order_id',$purchaseOrderBill['purchase_order_id'])->get();
            $extraTaxPercentage = $purchaseOrderComponents->max(function ($purchaseOrderComponent) {
                return ($purchaseOrderComponent->cgst_percentage + $purchaseOrderComponent->sgst_percentage + $purchaseOrderComponent->igst_percentage);
            });
            return view('purchase.purchase-order-billing.edit')->with(compact('purchaseOrderBill','purchaseOrderBillImagePaths','subTotalAmount','paymentTypes','grn','paymentRemainingAmount','paymentTillToday','banks','transactionEditAccess','extraTaxPercentage','cashAllowedLimit'));

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
                if($purchaseOrderPaymentData[$pagination]->is_advance == true){
                    $paymentType = 'Advance';
                }elseif($purchaseOrderPaymentData[$pagination]->paymentType != null){
                    $paymentType = ucfirst($purchaseOrderPaymentData[$pagination]->paid_from_slug).' - '.$purchaseOrderPaymentData[$pagination]->paymentType->name;
                }else{
                    $paymentType = ucfirst($purchaseOrderPaymentData[$pagination]->paid_from_slug);
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
            $purchaseOrderPaymentData = $request->except('_token','is_advance','payment_id','paid_from_slug');
            if($request->has('is_advance')){
                $purchaseOrderPaymentData['is_advance'] = true;
                $purchaseOrderId = PurchaseOrderBill::join('purchase_orders','purchase_orders.id','=','purchase_order_bills.purchase_order_id')
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
                if($request['paid_from_slug'] == 'bank'){
                    $bank = BankInfo::where('id',$request['bank_id'])->first();
                    if($request['payment_id'] == null){
                        $request->session()->flash('success','Payment Type not selected');
                        return redirect('/purchase/purchase-order-bill/edit/'.$request->purchase_order_bill_id);
                    }elseif($request['amount'] <= $bank['balance_amount']){
                        $purchaseOrderPaymentData['is_advance'] = false;
                        $purchaseOrderPaymentData['paid_from_slug'] = $request['paid_from_slug'];
                        $purchaseOrderPaymentData['payment_id'] = $request->payment_id;
                        $bankData['balance_amount'] = $bank['balance_amount'] - $request['amount'];
                        $bank->update($bankData);
                    }else{
                        $request->session()->flash('success','Bank Balance Amount is insufficient for this transaction');
                        return redirect('/purchase/purchase-order-bill/edit/'.$request->purchase_order_bill_id);
                    }
                }else{
                    $statistics = $this->getSiteWiseStatistics();
                    $cashAllowedLimit = ($statistics['remainingAmount'] > 0) ? $statistics['remainingAmount'] : 0 ;
                    if($request['amount'] <= $cashAllowedLimit){
                        $purchaseOrderPaymentData['paid_from_slug'] = $request['paid_from_slug'];
                        $purchaseOrderPaymentData['is_advance'] = false;
                    }else{
                        $request->session()->flash('success','Amount is insufficient for this transaction');
                        return redirect('/purchase/purchase-order-bill/edit/'.$request->purchase_order_bill_id);
                    }
                }

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

    public function checkBillNumber(Request $request){
        try{
            $purchaseOrderId = $request->purchase_order_id;
            $vendorBillNumber = $request->vendor_bill_number;
            $vendorId = PurchaseOrder::findOrFail($purchaseOrderId)->vendor_id;
            $purchaseBillNumberCount = PurchaseOrderBill::join('purchase_orders','purchase_orders.id','=','purchase_order_bills.purchase_order_id')
                                        ->where('purchase_orders.vendor_id', $vendorId)
                                        ->where('purchase_order_bills.vendor_bill_number','ilike',($vendorBillNumber))
                                        ->count();
            if($purchaseBillNumberCount > 0){
                return 'false';
            }else{
                return 'true';
            }
        }catch (\Exception $e){
            $data = [
                'action' => 'Purchase Order Bill Number Check',
                'data' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            return null;
        }
    }

    public function editPurchaseOrderBill(Request $request,$purchaseOrderBill){
        try{
            $purchaseOrderBillData = $request->except('_token');
            if($request->has('extra_amount')){
                $purchaseOrderBillData['extra_amount'] = round($request['extra_amount'],3);
            }
            $purchaseOrderBill = PurchaseOrderBill::where('id',$purchaseOrderBill['id'])->first();
            $purchaseOrderBill->update($purchaseOrderBillData);
            $request->session()->flash('success','Purchase Order Bill Edited Successfully');
            return redirect('/purchase/purchase-order-bill/edit/'.$purchaseOrderBill['id']);
        }catch (\Exception $e){
            $data = [
                'action' => 'Edit Purchase Order Bill',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }
}