<?php

namespace App\Http\Controllers\Purchase;

use App\Asset;
use App\AssetType;
use App\BankInfo;
use App\Category;
use App\CategoryMaterialRelation;
use App\Client;
use App\Helper\MaterialProductHelper;
use App\Http\Controllers\CustomTraits\Inventory\InventoryTrait;
use App\Http\Controllers\CustomTraits\Notification\NotificationTrait;
use App\Http\Controllers\CustomTraits\PeticashTrait;
use App\InventoryComponent;
use App\InventoryComponentTransferStatus;
use App\InventoryTransferTypes;
use App\MaterialRequestComponentTypes;
use App\MaterialVersion;
use App\PaymentType;
use App\Project;
use App\ProjectSite;
use App\PurchaseOrder;
use App\Helper\UnitHelper;
use App\Http\Controllers\CustomTraits\Purchase\MaterialRequestTrait;
use App\Material;
use App\PurchaseOrderAdvancePayment;
use App\PurchaseOrderBill;
use App\PurchaseOrderBillTransactionRelation;
use App\PurchaseOrderComponent;
use App\PurchaseOrderStatus;
use App\PurchaseOrderTransaction;
use App\PurchaseOrderTransactionComponent;
use App\PurchaseOrderTransactionImage;
use App\PurchaseOrderTransactionStatus;
use App\PurchaseRequest;
use App\PurchaseRequestComponentVendorMailInfo;
use App\Quotation;
use App\QuotationStatus;
use App\UnitConversion;
use App\User;
use App\Vendor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use App\PurchaseOrderComponentImage;
use App\PurchaseRequestComponent;
use App\PurchaseRequestComponentVendorRelation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use App\Unit;
use Illuminate\Support\Facades\Session;


class PurchaseOrderController extends Controller
{
    use MaterialRequestTrait;
    use InventoryTrait;
    use NotificationTrait;
    use PeticashTrait;
    public function __construct(){
        $this->middleware('custom.auth');
    }
    public function getManageView(Request $request){
        $approvedQuotationStatus = QuotationStatus::where('slug','approved')->first();
        $projectSiteIds = Quotation::where('quotation_status_id',$approvedQuotationStatus['id'])->pluck('project_site_id')->toArray();
        $projectIds = ProjectSite::whereIn('id',$projectSiteIds)->pluck('project_id')->toArray();
        $clientIds = Project::whereIn('id',$projectIds)->pluck('client_id')->toArray();
        $clients = Client::whereIn('id',$clientIds)->where('is_active',true)->orderBy('id','asc')->get()->toArray();
        return view('purchase/purchase-order/manage')->with(compact('clients'));
    }

    public function getCreateView(Request $request){
        try{
            $purchaseOrderComponentPRIds = PurchaseOrderComponent::pluck('purchase_request_component_id');
            if(Session::has('global_project_site')){
                $projectSiteId = Session::get('global_project_site');
                $adminApprovePurchaseRequestInfo = PurchaseRequestComponent::join('purchase_requests','purchase_requests.id','=','purchase_request_components.purchase_request_id')
                    ->join('purchase_request_component_statuses','purchase_request_component_statuses.id','=','purchase_requests.purchase_component_status_id')
                    ->join('purchase_request_component_vendor_relation','purchase_request_component_vendor_relation.purchase_request_component_id','=','purchase_request_components.id')
                    ->whereIn('purchase_request_component_statuses.slug',['p-r-admin-approved','p-r-manager-approved'])
                    ->whereNotIn('purchase_request_components.id',$purchaseOrderComponentPRIds)
                    ->where('purchase_requests.project_site_id',$projectSiteId)
                    ->select('purchase_requests.id as id','purchase_requests.project_site_id as project_site_id','purchase_requests.created_at as created_at','purchase_requests.serial_no as serial_no')
                    ->get()
                    ->toArray();
            }else{
                $adminApprovePurchaseRequestInfo = PurchaseRequestComponent::join('purchase_requests','purchase_requests.id','=','purchase_request_components.purchase_request_id')
                    ->join('purchase_request_component_statuses','purchase_request_component_statuses.id','=','purchase_requests.purchase_component_status_id')
                    ->join('purchase_request_component_vendor_relation','purchase_request_component_vendor_relation.purchase_request_component_id','=','purchase_request_components.id')
                    ->whereIn('purchase_request_component_statuses.slug',['p-r-admin-approved','p-r-manager-approved'])
                    ->whereNotIn('purchase_request_components.id',$purchaseOrderComponentPRIds)
                    ->select('purchase_requests.id as id','purchase_requests.project_site_id as project_site_id','purchase_requests.created_at as created_at','purchase_requests.serial_no as serial_no')
                    ->get()
                    ->toArray();
            }
            $categories = Category::where('is_miscellaneous',true)->select('id','name')->get()->toArray();
            $purchaseRequests = array();
            foreach($adminApprovePurchaseRequestInfo as $purchaseRequest){
                $purchaseRequests[$purchaseRequest['id']] = $this->getPurchaseIDFormat('purchase-request',$purchaseRequest['project_site_id'],($purchaseRequest['created_at']),$purchaseRequest['serial_no']);
            }
            return view('purchase/purchase-order/create')->with(compact('purchaseRequests','categories'));
        }catch(\Exception $e){
            $data = [
                'action' => 'Get Purchase order create view',
                'exception' => $e->getMessage(),
                'params' => $request->all()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }
    
    public function getListing(Request $request){
        try{
            $user = Auth::user();
            $postdata = null;
            $status = 0;
            $site_id = 0;
            $month = 0;
            $year = 0;
            $po_count = 0;
            $vendor_name = "";
            $po_id = "";
            $postDataArray = array();
            if ($request->has('po_id')) {
                if ($request['po_id'] != "") {
                    $po_id = $request['po_id'];
                }
            }

            if ($request->has('vendor_name')) {
                if ($request['vendor_name'] != "") {
                    $vendor_name = $request['vendor_name'];
                }
            }

            if ($request->has('status')) {
                $status = $request['status'];
            }
            if($request->has('postdata')) {
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
                $site_id = $postDataArray['site_id'];
                $month = $postDataArray['month'];
                $year = $postDataArray['year'];
                $po_count = $postDataArray['po_count'];
            }
            $purchaseOrderDetail = array();
            if($request->has('site_id')){
                $site_id = $request->site_id;
            }
            $ids = PurchaseOrder::all()->pluck('id');
            $filterFlag = true;
            if ($site_id != 0 && $filterFlag == true) {
                $ids = PurchaseOrder::join('purchase_requests','purchase_requests.id','=','purchase_orders.purchase_request_id')
                    ->where('purchase_requests.project_site_id',$site_id)->whereIn('purchase_orders.id',$ids)->pluck('purchase_orders.id');
                if(count($ids) <= 0) {
                    $filterFlag = false;
                }
            }

            if ($year != 0 && $filterFlag == true) {
                $ids = PurchaseOrder::whereIn('id',$ids)->whereYear('created_at', $year)->pluck('id');
                if(count($ids) <= 0) {
                    $filterFlag = false;
                }
            }

            if ($month != 0 && $filterFlag == true) {
                $ids = PurchaseOrder::whereIn('id',$ids)->whereMonth('created_at', $month)->pluck('id');
                if(count($ids) <= 0) {
                    $filterFlag = false;
                }
            }
            if ($status != 0 && $filterFlag == true) {
                if ($status == 1 ) {
                    $status = true;
                } else {
                    $status = false;
                }
                $ids = PurchaseOrder::whereIn('id',$ids)->where('is_approved', $status)->pluck('id');
                if(count($ids) <= 0) {
                    $filterFlag = false;
                }
            }

            if ($po_count != 0 && $filterFlag == true) {
                $ids = PurchaseOrder::whereIn('id',$ids)->where('serial_no', $po_count)->pluck('id');
                if(count($ids) <= 0) {
                    $filterFlag = false;
                }
            }

            if ($vendor_name != "" & $filterFlag == true) {
                $forVendorSearchIds = PurchaseOrder::join('vendors','vendors.id','=','purchase_orders.vendor_id')
                    ->join('clients','clients.id','=','purchase_orders.vendor_id')
                    ->where('vendors.company','ilike','%'.$vendor_name.'%')->orWhere('vendors.company','ilike','%'.$vendor_name.'%')->whereIn('purchase_orders.id',$ids)->pluck('purchase_orders.id');
                $forClientSearchIds = PurchaseOrder::join('clients','clients.id','=','purchase_orders.client_id')
                    ->join('clients','clients.id','=','purchase_orders.client_id')
                    ->where('clients.company','ilike','%'.$vendor_name.'%')->orWhere('clients.company','ilike','%'.$vendor_name.'%')->whereIn('purchase_orders.id',$ids)->pluck('purchase_orders.id');
                $ids = array_merge($forVendorSearchIds,$forClientSearchIds);
                if(count($ids) <= 0) {
                    $filterFlag = false;
                }
            }

            if ($po_id != "" & $filterFlag == true) {
                $ids = PurchaseOrder::where('format_id','ilike','%'.$po_id.'%')->whereIn('id',$ids)->pluck('id');
                if(count($ids) <= 0) {
                    $filterFlag = false;
                }
            }

            if ($filterFlag) {
                $purchaseOrderDetail = PurchaseOrder::whereIn('id',$ids)->orderBy('created_at','desc')->get();
            }



            $purchaseOrderList = array();
            $iterator = 0;
            if(count($purchaseOrderDetail) > 0){
                 foreach($purchaseOrderDetail as $key => $purchaseOrder){
                     $projectSite = $purchaseOrder->purchaseRequest->projectSite;
                     $purchaseOrderList[$iterator]['purchase_order_id'] = $purchaseOrder['id'];
                     $purchaseRequest = PurchaseRequest::where('id',$purchaseOrder['purchase_request_id'])->first();
                     $purchaseOrderList[$iterator]['purchase_order_format_id'] = $this->getPurchaseIDFormat('purchase-order',$projectSite['id'],$purchaseOrder['created_at'],$purchaseOrder['serial_no']);
                     $purchaseOrderList[$iterator]['purchase_request_id'] = $purchaseOrder['purchase_request_id'];
                     $purchaseOrderList[$iterator]['purchase_request_format_id'] = $this->getPurchaseIDFormat('purchase-request',$projectSite['id'],$purchaseRequest['created_at'],$purchaseRequest['serial_no']);
                     $project = $projectSite->project;
                     $purchaseOrderList[$iterator]['client_name'] = ($purchaseOrder->vendor_id != null) ? $purchaseOrder->vendor->company : $purchaseOrder->client->company;
                     $purchaseOrderList[$iterator]['site_name'] = $projectSite->name;
                     $purchaseOrderComponents = $purchaseOrder->purchaseOrderComponent;
                     $purchaseOrderList[$iterator]['approved_quantity'] = $purchaseOrderComponents->sum('quantity');
                     $quantity = ($purchaseOrderComponents->sum('quantity') + ($purchaseOrderComponents->sum('quantity') * (10/100)));
                     $consumedQuantity = 0;
                     foreach($purchaseOrderComponents as $purchaseOrderComponent){
                         $consumedQuantity += $purchaseOrderComponent->purchaseOrderTransactionComponent->sum('quantity');
                     }
                     $purchaseOrderList[$iterator]['remaining_quantity'] = $quantity - $consumedQuantity;
                     $purchaseOrderList[$iterator]['project'] = $project->name;
                     $purchaseOrderList[$iterator]['chk_status'] = $purchaseOrder['is_approved'];
                     $purchaseOrderList[$iterator]['status'] = ($purchaseOrder['is_approved'] == true) ? '<span class="label label-sm label-success"> Approved </span>' : '<span class="label label-sm label-danger"> Disapproved </span>';
                     $purchaseOrderList[$iterator]['created_at'] = $purchaseOrder['created_at'];
                     $purchaseOrderList[$iterator]['is_email_sent'] = $purchaseOrder['is_email_sent'];
                     $iterator++;
                 }
             }
            $iTotalRecords = count($purchaseOrderList);
            $records = array();
            $records['data'] = array();
            if($request->length == -1){
                $length = $iTotalRecords;
            }else{
                $length = $request->length;
            }
            for($iterator = 0,$pagination = $request->start; $iterator < $length && $iterator < count($purchaseOrderList); $iterator++,$pagination++ ){
                $actionData = "";
                if ($purchaseOrderList[$pagination]['chk_status'] == true) {
                    if($purchaseOrderList[$pagination]['is_email_sent'] == true || !isset($purchaseOrderList[$pagination]['is_email_sent'])){
                        $imageName = 'email_sent.svg';
                        $imageTitle = 'Email is Sent.';
                    }else{
                        $imageName = 'email_pending.svg';
                        $imageTitle = 'Email is pending.';
                    }
                    $actionData =  '<div>
                                        <img src="/assets/global/img/'.$imageName.'" style="height: 20px" title="'.$imageTitle.'">';
                    if($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin' || $user->customHasPermission('approve-purchase-order') || $user->customHasPermission('view-purchase-order')){
                        $actionData .= '<div id="sample_editable_1_new" class="btn btn-small blue" >
                                            <a href="/purchase/purchase-order/edit/'.$purchaseOrderList[$iterator]['purchase_order_id'].'" style="color: white; margin-left: 8%"> Edit
                                            </a> &nbsp; | &nbsp;
                                            <a href="/purchase/purchase-order/download-po-pdf/'.$purchaseOrderList[$iterator]['purchase_order_id'].'" style="color: white">
                                                <i class="fa fa-download" aria-hidden="true"></i>
                                            </a>
                                        </div>';
                    }
                    $actionData .= '</div>';
                }
                $records['data'][$iterator] = [
                    '<a href="javascript:void(0);" onclick="openPurchaseOrderDetails('.$purchaseOrderList[$pagination]['purchase_order_id'].')">
                        '.$purchaseOrderList[$pagination]['purchase_order_format_id'].'
                    </a>',
                    '<a href="javascript:void(0);" onclick="openPurchaseRequestDetails('.$purchaseOrderList[$pagination]['purchase_request_id'].')">
                        '.$purchaseOrderList[$pagination]['purchase_request_format_id'].'
                    </a>',
                    $purchaseOrderList[$pagination]['client_name'],
                    $purchaseOrderList[$pagination]['project']." - ".$purchaseOrderList[$pagination]['site_name'],
                    date('d M Y',strtotime($purchaseOrderList[$pagination]['created_at'])),
                    $purchaseOrderList[$pagination]['approved_quantity'],
                    $purchaseOrderList[$pagination]['remaining_quantity'],
                    $purchaseOrderList[$pagination]['status'],
                    $actionData
                ];
            }
            $records["draw"] = intval($request->draw);
            $records["recordsTotal"] = $iTotalRecords;
            $records["recordsFiltered"] = $iTotalRecords;
            $responseStatus = 200;
            return response()->json($records,$responseStatus);
        }catch(\Exception $e){
            $data = [
                'action' => 'Purchase Order listing',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            $responseStatus = 500;
            $records = array();
        }
    }

    public function createMaterial(Request $request){
        try{
            $now = Carbon::now();
            $is_present = Material::where('name','ilike',$request->name)->pluck('id')->toArray();
            $id = Material::where('name','ilike',$request->name)->pluck('id')->first();
            if($is_present != null){
                $categoryMaterialData['category_id'] = $request->category;
                $categoryMaterialData['material_id'] = $id;
                CategoryMaterialRelation::create($categoryMaterialData);
            }else{
                $materialData['name'] = ucwords(trim($request->name));
                $categoryMaterialData['category_id'] = $request->category;
                $materialData['rate_per_unit'] = round($request->rate_per_unit,3);
                $materialData['unit_id'] = $request->unit_id;
                $materialData['is_active'] = (boolean)0;
                $materialData['created_at'] = $now;
                $materialData['updated_at'] = $now;
                $materialData['hsn_code'] = $request->hsn_code;
                $material = Material::create($materialData);
                $categoryMaterialData['material_id'] = $material['id'];
                CategoryMaterialRelation::create($categoryMaterialData);
                $materialVersionData['material_id'] = $material->id;
                $materialVersionData['rate_per_unit'] = round($request->rate_per_unit,3);
                $materialVersionData['unit_id'] = $request->unit_id;
                MaterialVersion::create($materialVersionData);
            }
            return response()->json(['message' => 'Material Created Successfully.!'], 200);
        }catch(\Exception $e){
            $data = [
                'action' => 'create material',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function closePurchaseOrder(Request $request){
        try{
            $mail_id = Vendor::where('id',$request['vendor_id'])->pluck('email')->first();
            $purchase_order_data['purchase_order_status_id'] = PurchaseOrderStatus::where('slug','close')->pluck('id')->first();
            $purchaseOrder = PurchaseOrder::where('id',$request['po_id'])->first();
            $purchaseOrder->update($purchase_order_data);
            $mailData = ['toMail' => $mail_id];
            $purchaseOrderComponent = $purchaseOrder->purchaseOrderComponent;
            Mail::send('purchase.purchase-order.email.purchase-order-close', ['purchaseOrder' => $purchaseOrder,'purchaseOrderComponent' => $purchaseOrderComponent], function($message) use ($mailData,$purchaseOrder){
                $message->subject('PO '.$purchaseOrder->purchaseRequest->format_id.'has been closed');
                $message->to($mailData['toMail']);
                $message->from(env('MAIL_USERNAME'));
            });
            $message="Purchase order closed successfully !";
        }catch(\Exception $e){
            $data = [
                'action' => 'create material',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            $message = "Something went wrong" .$e->getMessage();
        }
        return response()->json($message);
    }

    public function reopenPurchaseOrder(Request $request){
        try{
            $purchase_order_data['purchase_order_status_id'] = PurchaseOrderStatus::where('slug','re-open')->pluck('id')->first();
            PurchaseOrder::where('id',$request['po_id'])->update($purchase_order_data);
            $message = "Purchase order re-opened successfully !";
        }catch(\Exception $e){
            $data = [
                'action' => 'Reopen Purchase Order',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            $message = "Something went wrong" .$e->getMessage();
        }
        return response()->json($message);
    }

    public function getEditView(Request $request,$id){
        try{
            $user = Auth::user();
            $userRole = $user->roles[0]->role->slug;
            $purchaseOrder =PurchaseOrder::where('id',$id)->first();
            $purchaseOrderList = array();
            $iterator = 0;
            if($purchaseOrder->is_client_order == true){
                $vendorName = $purchaseOrder->client->company;
            }else{
                $vendorName = $purchaseOrder->vendor->company;
            }
            if(($purchaseOrder) != null){
                    $purchaseOrderList['purchase_order_id'] = $purchaseOrder['id'];
                    $projectSite = $purchaseOrder->purchaseRequest->projectSite;
                    $purchaseRequest = PurchaseRequest::where('id',$purchaseOrder['purchase_request_id'])->first();
                    $purchaseOrderList['purchase_order_format_id'] = $this->getPurchaseIDFormat('purchase-order',$projectSite['id'],$purchaseOrder['created_at'],$purchaseOrder['serial_no']);
                    $purchaseOrderList['purchase_request_id'] = $purchaseOrder['purchase_request_id'];
                    $purchaseOrderList['purchase_request_format_id'] = $this->getPurchaseIDFormat('purchase-request',$projectSite['id'],$purchaseRequest['created_at'],$purchaseRequest['serial_no']);
                    $project = $projectSite->project;
                    $purchaseOrderList['client_name'] = $project->client->company;
                    $purchaseOrderList['project'] = $project->name.'  '.'-'.'  '.$projectSite->name;
                    if($purchaseOrder->is_client_order == true){
                        $purchaseOrderList['vendor_name'] = $purchaseOrder->client->company;
                        $purchaseOrderList['vendor_id'] = $purchaseOrder->client->id;
                        $purchaseOrderList['is_client_order'] = true;
                    }else{
                        $purchaseOrderList['vendor_name'] = $purchaseOrder->vendor->company;
                        $purchaseOrderList['vendor_id'] = $purchaseOrder->vendor->id;
                        $purchaseOrderList['is_client_order'] = false;
                    }
                    $purchaseOrderList['total_advance_amount'] = $purchaseOrder->total_advance_amount;
                    $purchaseOrderList['balance_advance_amount'] = $purchaseOrder->balance_advance_amount;
                    $purchaseOrderList['status'] = ($purchaseOrder['is_approved'] == true) ? '<span class="label label-sm label-success"> Approved </span>' : '<span class="label label-sm label-danger"> Disapproved </span>';
            }
            $materialList = array();
            foreach($purchaseOrder->purchaseOrderComponent as $key => $purchaseOrderComponent){
                $materialRequestComponent = $purchaseOrderComponent->purchaseRequestComponent->materialRequestComponent;
                $materialList[$iterator]['purchase_order_component_id'] = $purchaseOrderComponent['id'];
                $materialList[$iterator]['material_request_component_id'] = $materialRequestComponent['id'];
                $materialList[$iterator]['material_component_name'] = $materialRequestComponent['name'];
                $materialList[$iterator]['material_component_unit_id'] = $purchaseOrderComponent['unit_id'];
                $materialList[$iterator]['material_component_unit_name'] = $purchaseOrderComponent->unit->name;
                $materialList[$iterator]['material_component_quantity'] = $purchaseOrderComponent->quantity;
                $quantityConsumed = $purchaseOrderComponent->purchaseOrderTransactionComponent->sum('quantity');
                $quantityUnused = $purchaseOrderComponent['quantity'] - $quantityConsumed;
                $materialList[$iterator]['material_component_remaining_quantity'] = (0.1 * ($quantityUnused)) + $quantityUnused;
                $materialList[$iterator]['consumed_quantity'] = $quantityConsumed;
                $mainDirectoryName = sha1($id);
                $componentDirectoryName = sha1($purchaseOrderComponent['id']);
                $uploadPath = url('/').public_path().env('PURCHASE_ORDER_IMAGE_UPLOAD').DIRECTORY_SEPARATOR.$mainDirectoryName.DIRECTORY_SEPARATOR.'vendor_quotation_images'.DIRECTORY_SEPARATOR.$componentDirectoryName;
                $images = PurchaseOrderComponentImage::where('purchase_order_component_id',$purchaseOrderComponent['id'])->where('is_vendor_approval',true)->select('name')->get();
                $j = 0;
                $materialComponentImages = array();
                if(count($images) > 0){
                    foreach ($images as $image){
                        $materialComponentImages[$j]['name'] = $uploadPath.'/'.$image['name'];
                        $j++;
                    }
                    $materialList[$iterator]['material_component_images'] = $materialComponentImages;
                }
                $iterator++;
            }
            $purchaseOrderTransactionListingData = PurchaseOrderTransaction::where('purchase_order_id',$purchaseOrder->id)->orderBy('created_at','desc')->select('id','grn','purchase_order_transaction_status_id')->get();
            $purchaseOrderTransactionListing = array();
            $iterator = 0;
            foreach($purchaseOrderTransactionListingData as $listing){
                $statusInfo = PurchaseOrderTransactionStatus::where('id',$listing['purchase_order_transaction_status_id'])->select('slug','name')->first();
                switch ($statusInfo['slug']){
                    case 'grn-generated':
                        $status = "<span class=\"btn btn-xs btn-warning\"> ".$statusInfo['name']." </span>";
                        break;

                    case 'bill-generated':
                        $status = "<span class=\"btn btn-xs btn-success\"> ".$statusInfo['name']." </span>";
                        break;

                    case 'bill-pending':
                        $status = "<span class=\"btn btn-xs green-meadow\"> ".$statusInfo['name']." </span>";
                        break;
                }
                $purchaseOrderTransactionListing[$iterator] = [
                    'grn' => $listing['grn'],
                    'status' => $status,
                    'purchase_order_transaction_id' => $listing['id']
                ];
                $iterator++;
            }
            $systemUsers = User::where('is_active',true)->select('id','first_name','last_name')->get();
            $transaction_types = PaymentType::select('id','name')->whereIn('slug',['cheque','neft','rtgs','internet-banking'])->get();
            $banks = BankInfo::where('is_active',true)->select('id','bank_name','balance_amount')->get();
            $statistics = $this->getSiteWiseStatistics();
            $cashAllowedLimit = ($statistics['remainingAmount'] > 0) ? $statistics['remainingAmount'] : 0 ;
            $purchaseOrderStatusSlug = $purchaseOrder->purchaseOrderStatus->slug;
            return view('purchase/purchase-order/edit')->with(compact('userRole','purchaseOrderStatusSlug','transaction_types','purchaseOrderList','materialList','purchaseOrderTransactionListing','systemUsers','vendorName','banks','cashAllowedLimit'));
        }catch (\Exception $e){
                $data = [
                    'action' => 'Get Purchase Order Edit View',
                    'params' => $request->all(),
                    'exception' => $e->getMessage()
                ];
                Log::critical(json_encode($data));
                abort(500);
        }
    }

    public function getPurchaseOrderComponentDetails(Request $request){
        try{
            $data = $request->all();
            $purchaseOrderComponent = PurchaseOrderComponent::where('id',$data['component_id'])->first();
            if($purchaseOrderComponent->purchaseOrder->is_client_order == true){
                $vendorName = $purchaseOrderComponent->purchaseOrder->client->company;
            }else{
                $vendorName = $purchaseOrderComponent->purchaseOrder->vendor->name;
            }
            $purchaseOrderComponentData['purchase_order_component_id'] = $purchaseOrderComponent['id'];
            $purchaseOrderComponentData['hsn_code'] = $purchaseOrderComponent['hsn_code'];
            $purchaseOrderComponentData['rate_per_unit'] = $purchaseOrderComponent['rate_per_unit'];
            $materialRequestComponent = $purchaseOrderComponent->purchaseRequestComponent->materialRequestComponent;
            $purchaseOrderComponentData['quantity'] = $purchaseOrderComponent['quantity'];
            $purchaseOrderComponentData['name'] = $materialRequestComponent['name'];
            $purchaseOrderComponentData['material_component_id'] = $materialRequestComponent['id'];
            $purchaseOrderComponentData['unit_name'] = $materialRequestComponent->unit->name;
            $purchaseOrderComponentData['unit_id'] = $materialRequestComponent['unit_id'];
            $purchaseOrderComponentData['vendor_name'] = $vendorName;
            $mainDirectoryName = sha1($purchaseOrderComponent->purchaseOrder->id);
            $componentDirectoryName = sha1($purchaseOrderComponent['id']);
            $uploadPath = env('APP_URL').env('PURCHASE_ORDER_IMAGE_UPLOAD').DIRECTORY_SEPARATOR.$mainDirectoryName.DIRECTORY_SEPARATOR.'vendor_quotation_images'.DIRECTORY_SEPARATOR.$componentDirectoryName;
            $uploadPathForClientImages = env('APP_URL').env('PURCHASE_ORDER_IMAGE_UPLOAD').DIRECTORY_SEPARATOR.$mainDirectoryName.DIRECTORY_SEPARATOR.'client_approval_images'.DIRECTORY_SEPARATOR.$componentDirectoryName;;
            $images = PurchaseOrderComponentImage::where('purchase_order_component_id',$purchaseOrderComponent['id'])->where('is_vendor_approval',true)->select('name')->get();
            $imagesOfClient = PurchaseOrderComponentImage::where('purchase_order_component_id',$purchaseOrderComponent['id'])->where('is_vendor_approval',false)->select('name')->get();
            $j = 0;
            $materialComponentImages = array();
            if(count($images) > 0){
                foreach ($images as $image){
                    $materialComponentImages[$j]['name'] = $uploadPath.'/'.$image['name'];
                    $materialComponentImages[$j]['extension'] = pathinfo($image['name'], PATHINFO_EXTENSION);;
                    $j++;
                }
                $purchaseOrderComponentData['material_component_images'] = $materialComponentImages;
            }
            $materialComponentImagesOfClientApproval = array();
            if(count($imagesOfClient) > 0){
                foreach ($imagesOfClient as $image){
                    $materialComponentImagesOfClientApproval[$j]['name'] = $uploadPathForClientImages.'/'.$image['name'];
                    $materialComponentImagesOfClientApproval[$j]['extension'] = pathinfo($image['name'], PATHINFO_EXTENSION);
                    $j++;
                }
                $purchaseOrderComponentData['client_approval_images'] = $materialComponentImagesOfClientApproval;
            }
            $transactionQuantity = 0;
            foreach($purchaseOrderComponent->purchaseOrderTransactionComponent as $purchaseOrderTransactionComponent){
                $transactionQuantity += UnitHelper::unitQuantityConversion($purchaseOrderTransactionComponent->unit_id, $purchaseOrderComponent->unit_id,$purchaseOrderTransactionComponent->quantity);
            }
            $purchaseOrderComponentData['transaction_quantity'] = $transactionQuantity;
            return view('partials.purchase.purchase-order.component-details')->with(compact('purchaseOrderComponentData','purchaseOrderComponent'));
        }catch(\Exception $e){
            $message = $e->getMessage();
            $status = 500;
           return response()->json($message,$status);
        }
    }

    public function getPurchaseOrderBillDetails(Request $request){
        try{
                $purchaseOrderBillData = PurchaseOrderBill::where('id',$request['po_id'])->first();
                $purchaseOrderBillData['unit'] = Unit::where('id',$purchaseOrderBillData['unit_id'])->pluck('name')->first();
                $status = 200;
            return response()->json($purchaseOrderBillData,$status);
        }catch(\Exception $e){
            $message = $e->getMessage();
            $status = 500;
            return response()->json($message,$status);
        }
    }

    public function createTransaction(Request $request){
        try{
            $purchaseOrderTransactionData = $request->except('_token','pre_grn_image','post_grn_image','component_data','vendor_name','purchase_order_id','purchase_order_transaction_id');
            $purchaseOrderTransactionData['in_time'] = $purchaseOrderTransactionData['out_time'] = Carbon::now();
            $purchaseOrderTransaction = PurchaseOrderTransaction::findOrFail($request->purchase_order_transaction_id);
            $purchaseOrderTransactionData['purchase_order_transaction_status_id'] = PurchaseOrderTransactionStatus::where('slug','bill-pending')->pluck('id')->first();
            $purchaseOrderTransaction->update($purchaseOrderTransactionData);
            $purchaseOrderDirectoryName = sha1($request->purchase_order_id);
            $purchaseTransactionDirectoryName = sha1($purchaseOrderTransaction->id);
            $imageUploadPath = public_path().env('PURCHASE_ORDER_IMAGE_UPLOAD').DIRECTORY_SEPARATOR.$purchaseOrderDirectoryName.DIRECTORY_SEPARATOR.'bill_transaction'.DIRECTORY_SEPARATOR.$purchaseTransactionDirectoryName;
            if (!file_exists($imageUploadPath)) {
                File::makeDirectory($imageUploadPath, $mode = 0777, true, true);
            }
            if($request->has('post_grn_image') && count($request->post_grn_image) > 0){
                foreach($request->post_grn_image as $postGrnImage){
                    $imageArray = explode(';',$postGrnImage);
                    $image = explode(',',$imageArray[1])[1];
                    $pos  = strpos($postGrnImage, ';');
                    $type = explode(':', substr($postGrnImage, 0, $pos))[1];
                    $extension = explode('/',$type)[1];
                    $filename = mt_rand(1,10000000000).sha1(time()).".{$extension}";
                    $fileFullPath = $imageUploadPath.DIRECTORY_SEPARATOR.$filename;
                    $transactionImageData = [
                        'purchase_order_transaction_id' => $purchaseOrderTransaction->id,
                        'name' => $filename,
                        'is_pre_grn' => false
                    ];
                    file_put_contents($fileFullPath,base64_decode($image));
                    PurchaseOrderTransactionImage::create($transactionImageData);
                }
            }
            $user = Auth::user();
            $purchaseOrder = PurchaseOrder::findOrFail($request->purchase_order_id);
            $projectInfo = $purchaseOrder->purchaseRequest->projectSite->project->name.' '.$purchaseOrder->purchaseRequest->projectSite->name;
            $mainNotificationString = '4-'.$projectInfo.' '.$user->first_name.' '.$user->last_name.' Material Received. ';
            foreach($request->component_data as $purchaseOrderComponentId => $purchaseOrderComponentData){
                $purchaseOrderComponent = PurchaseOrderComponent::findOrFail($purchaseOrderComponentId);
                $purchaseOrderTransactionComponentData = [
                    'purchase_order_component_id' => $purchaseOrderComponentId,
                    'quantity' => $purchaseOrderComponentData['quantity'],
                    'unit_id' => $purchaseOrderComponentData['unit_id'],
                    'purchase_order_transaction_id' => $purchaseOrderTransaction->id
                ];
                $purchaseOrderTransactionComponent = PurchaseOrderTransactionComponent::create($purchaseOrderTransactionComponentData);
                $materialRequestUserToken = User::join('material_requests','material_requests.on_behalf_of','=','users.id')
                    ->join('material_request_components','material_request_components.material_request_id','=','material_requests.id')
                    ->join('purchase_request_components','purchase_request_components.material_request_component_id','=','material_request_components.id')
                    ->join('purchase_order_components','purchase_order_components.purchase_request_component_id','=','purchase_request_components.id')
                    ->join('purchase_orders','purchase_orders.id','=','purchase_order_components.purchase_order_id')
                    ->where('purchase_orders.id', $purchaseOrder->id)
                    ->where('purchase_request_components.id', $purchaseOrderComponent->purchase_request_component_id)
                    ->select('users.web_fcm_token as web_fcm_function','users.mobile_fcm_token as mobile_fcm_function')
                    ->get()->toArray();
                $purchaseRequestApproveUserToken = User::join('material_request_component_history_table','material_request_component_history_table.user_id','=','users.id')
                    ->join('material_request_components','material_request_component_history_table.material_request_component_id','=','material_request_components.id')
                    ->join('purchase_request_components','purchase_request_components.material_request_component_id','=','material_request_components.id')
                    ->join('purchase_request_component_statuses','purchase_request_component_statuses.id','=','material_request_component_history_table.component_status_id')
                    ->whereIn('purchase_request_component_statuses.slug',['p-r-manager-approved','p-r-admin-approved'])
                    ->where('purchase_request_components.id',$purchaseOrderComponent->purchase_request_component_id)
                    ->select('users.web_fcm_token as web_fcm_function','users.mobile_fcm_token as mobile_fcm_function')
                    ->get()->toArray();
                $webTokens = array_merge(array_column($materialRequestUserToken,'web_fcm_token'), array_column($purchaseRequestApproveUserToken,'web_fcm_token'));
                $mobileTokens = array_merge(array_column($materialRequestUserToken,'mobile_fcm_token'), array_column($purchaseRequestApproveUserToken,'mobile_fcm_token'));
                $notificationString = $mainNotificationString.' '.$purchaseOrderComponent->purchaseRequestComponent->materialRequestComponent->name;
                $notificationString .= ' '.$purchaseOrderTransactionComponent->quantity.' '.$purchaseOrderTransactionComponent->unit->name;
                $this->sendPushNotification('Manisha Construction',$notificationString,$webTokens,$mobileTokens,'c-p-b');
                $projectSiteId = $purchaseOrderComponent->purchaseOrder->purchaseRequest->project_site_id;
                $inventoryComponent = InventoryComponent::where('project_site_id',$projectSiteId)->where('name','ilike',$purchaseOrderComponentData['name'])->first();
                if($inventoryComponent == null){
                    $assetComponentTypeIds = MaterialRequestComponentTypes::whereIn('slug',['system-asset','new-asset'])->pluck('id')->toArray();
                    $inventoryComponentData = [
                        'name' => $purchaseOrderComponent->purchaseRequestComponent->materialRequestComponent->name,
                        'purchase_order_component_id' => $purchaseOrderComponent->id,
                        'opening_stock' => 0,
                        'project_site_id' => $purchaseOrderComponent->purchaseOrder->purchaseRequest->project_site_id
                    ];
                    if(in_array($purchaseOrderComponent->purchaseRequestComponent->materialRequestComponent->component_type_id,$assetComponentTypeIds)){
                        $inventoryComponentData['is_material'] = false;
                        $inventoryComponentData['reference_id'] = Asset::where('name','ilike',$inventoryComponentData['name'])->pluck('id')->first();
                    }else{
                        $inventoryComponentData['is_material'] = true;
                        $inventoryComponentData['reference_id'] = Material::where('name','ilike',$inventoryComponentData['name'])->pluck('id')->first();
                    }
                    $inventoryComponent = InventoryComponent::create($inventoryComponentData);
                }
                $transferTypeId = InventoryTransferTypes::where('slug','supplier')->where('type','ilike','IN')->pluck('id')->first();
                $inventoryComponentTransferData = [
                    'inventory_component_id' => $inventoryComponent->id,
                    'transfer_type_id' => $transferTypeId,
                    'unit_id' => $purchaseOrderComponentData['unit_id'],
                    'quantity' =>  $purchaseOrderComponentData['quantity']
                ];
                $inventoryComponentTransferData = array_merge($inventoryComponentTransferData,$request->except('type','component_data','material','unit_name','vendor_name','purchase_order_component_id'));
                $inventoryComponentTransferData['source_name'] = $request->vendor_name;
                $inventoryComponentTransferData['inventory_component_transfer_status_id'] = InventoryComponentTransferStatus::where('slug','approved')->pluck('id')->first();
                $this->createInventoryComponentTransfer($inventoryComponentTransferData);
            }
            $request->session()->flash('success','Transaction added successfully');
            return redirect('/purchase/purchase-order/edit/'.$request->purchase_order_id);
        }catch(\Exception $e){
            $data = [
                'action' => 'Create P.O. Transaction',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function createAdvancePayment(Request $request){
        try{
            $advancePaymentData = $request->except('_token');
            if($request['paid_from_slug'] == 'bank'){
                $bank = BankInfo::where('id',$request['bank_id'])->first();
                if($request['amount'] <= $bank['balance_amount']){
                    PurchaseOrderAdvancePayment::create($advancePaymentData);
                    $purchaseOrder = PurchaseOrder::findOrFail($request->purchase_order_id);
                    $newAdvancePaymentAmount = $purchaseOrder->total_advance_amount + $request->amount;
                    $balanceAdvanceAmount = $purchaseOrder->balance_advance_amount + $request->amount;
                    $purchaseOrder->update([
                        'total_advance_amount' => $newAdvancePaymentAmount,
                        'balance_advance_amount' => $balanceAdvanceAmount
                    ]);
                    $bankData['balance_amount'] = $bank['balance_amount'] - $request['amount'];
                    $bank->update($bankData);
                    $request->session()->flash('success','Advance Payment added successfully');
                }else{
                    $request->session()->flash('success','Bank Balance Amount is insufficient for this transaction');
                }
            }else{
                $statistics = $this->getSiteWiseStatistics();
                $cashAllowedLimit = ($statistics['remainingAmount'] > 0) ? $statistics['remainingAmount'] : 0 ;
                if($request['amount'] <= $cashAllowedLimit){
                    PurchaseOrderAdvancePayment::create($advancePaymentData);
                    $purchaseOrder = PurchaseOrder::findOrFail($request->purchase_order_id);
                    $newAdvancePaymentAmount = $purchaseOrder->total_advance_amount + $request->amount;
                    $balanceAdvanceAmount = $purchaseOrder->balance_advance_amount + $request->amount;
                    $purchaseOrder->update([
                        'total_advance_amount' => $newAdvancePaymentAmount,
                        'balance_advance_amount' => $balanceAdvanceAmount
                    ]);
                    $request->session()->flash('success','Advance Payment added successfully');
                }else{
                    $request->session()->flash('success','Bank Balance Amount is insufficient for this transaction');
                }
            }


            return redirect('/purchase/purchase-order/edit/'.$purchaseOrder->id);
       }catch (\Exception $e){
            $data = [
                'action' => 'Create Bill Payment',
                'exception' => $e->getMessage(),
                'params' => $request->all()
            ];
            Log::critical($data);
            abort(500);
        }
    }

    public function changeStatus(Request $request){
        try{
            PurchaseOrderBill::where('id',$request['purchase_order_bill_id'])->update(['is_paid' => false, 'is_amendment' => false,'remark' => $request['remark']]);
            $request->session()->flash('success','Approved successfully');
            return Redirect::Back();
        }catch (\Exception $e){
            $data = [
                'action' => 'Change Status od Purchase Order',
                'exception' => $e->getMessage(),
                'params' => $request->all()
            ];
            $request->session()->flash('danger','Something went wrong');
            return Redirect::Back();
        }
    }

    public function getPurchaseRequestComponents(Request $request,$purchaseRequestId){
        try{
            $purchaseOrderComponentIds = PurchaseOrderComponent::pluck('purchase_request_component_id');
            $purchaseRequestComponentData = PurchaseRequestComponent::where('purchase_request_id',$purchaseRequestId)
                                                                ->whereNotIn('id',$purchaseOrderComponentIds)
                                                                ->get();
            $purchaseRequestComponents = array();
            $iterator = 0;
            foreach ($purchaseRequestComponentData as $purchaseRequestComponent){
                $requestComponentVendors = PurchaseRequestComponentVendorRelation::where('purchase_request_component_id',$purchaseRequestComponent->id)->get();
                foreach($requestComponentVendors as $vendorRelation){
                    $purchaseRequestComponents[$iterator] = array();
                    $materialRequest = $purchaseRequestComponent->materialRequestComponent;
                    $materialRequestComponentSlug = $materialRequest->materialRequestComponentTypes->slug;
                    $purchaseRequestComponents[$iterator]['purchase_request_component_id'] = $purchaseRequestComponent->id;
                    $purchaseRequestComponents[$iterator]['name'] = $materialRequest->name;
                    $last_three_rates = PurchaseOrderComponent::join('purchase_request_components','purchase_order_components.purchase_request_component_id','=','purchase_request_components.id')
                        ->join('purchase_orders','purchase_orders.id','=','purchase_order_components.purchase_order_id')
                        ->join('material_request_components','material_request_components.id','=','purchase_request_components.material_request_component_id')
                        ->where('purchase_orders.is_approved',true)
                        ->where('material_request_components.name','ilike',$materialRequest->name)
                        ->orderBy('purchase_order_components.created_at','desc')
                        ->select('purchase_order_components.rate_per_unit','purchase_request_components.id')
                        ->take(3)->get()->toArray();
                    $purchaseRequestComponents[$iterator]['last_three_rates'] = $last_three_rates;
                    $purchaseRequestComponents[$iterator]['quantity'] = $purchaseRequestComponent->materialRequestComponent->quantity;
                    $purchaseRequestComponents[$iterator]['unit_id'] = $purchaseRequestComponent->materialRequestComponent->unit_id;
                    $purchaseRequestComponents[$iterator]['vendor'] = $vendorRelation->vendor->company;
                    $purchaseRequestComponents[$iterator]['vendor_id'] = $vendorRelation->vendor_id;
                    $purchaseRequestComponents[$iterator]['material_request_component_slug'] = $materialRequestComponentSlug;
                    if($materialRequestComponentSlug == 'new-material'){
                        $purchaseRequestComponents[$iterator]['categories'] = Category::where('is_miscellaneous', true)->where('is_active', true)->select('id','name')->get();
                        if(count($purchaseRequestComponents[$iterator]['categories']) <= 0){
                            $purchaseRequestComponents[$iterator]['categories'][] = [
                                'id' => '',
                                'name' => 'No special categories found'
                            ];
                        }
                    }elseif($materialRequestComponentSlug == 'new-asset' || $materialRequestComponentSlug == 'system-asset'){
                        $purchaseRequestComponents[$iterator]['categories'][] = [
                            'id' => '',
                            'name' => 'Asset'
                        ];
                    }else{
                        $materialId = Material::where('name','ilike',$purchaseRequestComponents[$iterator]['name'])->pluck('id')->first();
                        $purchaseRequestComponents[$iterator]['categories'] = CategoryMaterialRelation::join('categories','categories.id','=','category_material_relations.category_id')
                                                                ->where('category_material_relations.material_id', $materialId)
                                                                ->select('categories.id as id','categories.name as name')
                                                                ->get()
                                                                ->toArray();
                    }
                    $materialInfo = Material::where('name','ilike',trim($purchaseRequestComponent->materialRequestComponent->name))->first();
                    if($materialInfo == null){
                        $purchaseRequestComponents[$iterator]['rate'] = '0';
                        $purchaseRequestComponents[$iterator]['hsn_code'] = '0';
                    }else{
                        $purchaseRequestComponents[$iterator]['rate'] = UnitHelper::unitConversion($materialInfo['unit_id'],$purchaseRequestComponent->materialRequestComponent->unit_id,$materialInfo['rate_per_unit']);
                        if(is_array($purchaseRequestComponents[$iterator]['rate'])){
                            return response()->json(['message' => $purchaseRequestComponents[$iterator]['rate']['message']],203);
                        }
                        $purchaseRequestComponents[$iterator]['hsn_code'] = $materialInfo['hsn_code'];
                    }
                    $iterator++;
                }
            }
            $unitInfo = Unit::where('is_active', true)->select('id','name')->get()->toArray();
            return view('partials.purchase.purchase-order.material-listing')->with(compact('purchaseRequestComponents','unitInfo'));
        }catch(\Exception $e){
            $data = [
                'action' => 'Get P.R. component listing in P.O.',
                'P.R.Id' => $purchaseRequestId,
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
        }
    }

    public function getClientProjectName(Request $request ,$purchaseRequestId){
        try{
            $status = 200;
            $response = array();
            $purchaseRequest = PurchaseRequest::findOrFail($purchaseRequestId);
            $response['client'] = $purchaseRequest->projectSite->project->client->company;
            $response['project'] = $purchaseRequest->projectSite->project->name.' - '.$purchaseRequest->projectSite->name;
        }catch (\Exception $e){
            $data = [
                'action' => 'Get P.R. client and project name in P.O.',
                'P.R.Id' => $purchaseRequestId,
                'exception' => $e->getMessage()
            ];
            $status = 500;
            $response = null;
            Log::critical(json_encode($data));
        }
        return response()->json($response,$status);
    }

    public function getOrderDetails(Request $request,$purchaseRequestId){
        try{
            $purchaseOrderComponentIds = PurchaseOrderComponent::pluck('purchase_request_component_id');
            $purchaseRequestComponentData = PurchaseRequestComponent::where('purchase_request_id',$purchaseRequestId)
                ->whereNotIn('id',$purchaseOrderComponentIds)
                ->get();
            $purchaseRequestComponents = array();
            $iterator = 0;
            foreach ($purchaseRequestComponentData as $purchaseRequestComponent){
                $requestComponentVendors = PurchaseRequestComponentVendorRelation::where('purchase_request_component_id',$purchaseRequestComponent->id)->get();
                foreach($requestComponentVendors as $vendorRelation){
                    $purchaseRequestComponents[$iterator] = array();
                    $materialRequest = $purchaseRequestComponent->materialRequestComponent;
                    $materialRequestComponentSlug = $materialRequest->materialRequestComponentTypes->slug;
                    $purchaseRequestComponents[$iterator]['purchase_request_component_id'] = $purchaseRequestComponent->id;
                    $purchaseRequestComponents[$iterator]['name'] = $materialRequest->name;
                    $last_three_rates = PurchaseOrderComponent::join('purchase_request_components','purchase_order_components.purchase_request_component_id','=','purchase_request_components.id')
                        ->join('purchase_orders','purchase_orders.id','=','purchase_order_components.purchase_order_id')
                        ->join('material_request_components','material_request_components.id','=','purchase_request_components.material_request_component_id')
                        ->where('purchase_orders.is_approved',true)
                        ->where('material_request_components.name','ilike',$materialRequest->name)
                        ->orderBy('purchase_order_components.created_at','desc')
                        ->select('purchase_order_components.rate_per_unit','purchase_request_components.id')
                        ->take(3)->get()->toArray();
                    $purchaseRequestComponents[$iterator]['last_three_rates'] = $last_three_rates;
                    $purchaseRequestComponents[$iterator]['quantity'] = $purchaseRequestComponent->materialRequestComponent->quantity;
                    $purchaseRequestComponents[$iterator]['unit_id'] = $purchaseRequestComponent->materialRequestComponent->unit_id;
                    $purchaseRequestComponents[$iterator]['vendor'] = $vendorRelation->vendor->company;
                    $purchaseRequestComponents[$iterator]['vendor_id'] = $vendorRelation->vendor_id;
                    $purchaseRequestComponents[$iterator]['material_request_component_slug'] = $materialRequestComponentSlug;
                    if($materialRequestComponentSlug == 'new-material'){
                        $purchaseRequestComponents[$iterator]['categories'] = Category::where('is_miscellaneous', true)->where('is_active', true)->select('id','name')->get();
                        if(count($purchaseRequestComponents[$iterator]['categories']) <= 0){
                            $purchaseRequestComponents[$iterator]['categories'][] = [
                                'id' => '',
                                'name' => 'No special categories found'
                            ];
                        }
                    }elseif($materialRequestComponentSlug == 'new-asset' || $materialRequestComponentSlug == 'system-asset'){
                        $purchaseRequestComponents[$iterator]['categories'][] = [
                            'id' => '',
                            'name' => 'Asset'
                        ];
                    }else{
                        $materialId = Material::where('name','ilike',$purchaseRequestComponents[$iterator]['name'])->pluck('id')->first();
                        $purchaseRequestComponents[$iterator]['categories'] = CategoryMaterialRelation::join('categories','categories.id','=','category_material_relations.category_id')
                            ->where('category_material_relations.material_id', $materialId)
                            ->select('categories.id as id','categories.name as name')
                            ->get()
                            ->toArray();
                    }
                    $materialInfo = Material::where('name','ilike',trim($purchaseRequestComponent->materialRequestComponent->name))->first();
                    if($materialInfo == null){
                        $purchaseRequestComponents[$iterator]['rate'] = '0';
                        $purchaseRequestComponents[$iterator]['hsn_code'] = '0';
                    }else{
                        $purchaseRequestComponents[$iterator]['rate'] = UnitHelper::unitConversion($materialInfo['unit_id'],$purchaseRequestComponent->materialRequestComponent->unit_id,$materialInfo['rate_per_unit']);
                        if(is_array($purchaseRequestComponents[$iterator]['rate'])){
                            return response()->json(['message' => $purchaseRequestComponents[$iterator]['rate']['message']],203);
                        }
                        $purchaseRequestComponents[$iterator]['hsn_code'] = $materialInfo['hsn_code'];
                    }
                    $iterator++;
                }
            }
            $unitInfo = Unit::where('is_active', true)->select('id','name')->get()->toArray();
            return view('partials.purchase.purchase-order.details')->with(compact('purchaseRequestComponents','unitInfo'));
        }catch(\Exception $e){
            $data = [
                'action' => 'Get P.R. component listing in P.O.',
                'P.R.Id' => $purchaseRequestId,
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
        }
    }

    public function downloadPoPDF(Request $request, $purchaseOrder) {
        try {
            $pdfTitle = 'Purchase Order';
            $vendorInfo = $purchaseOrder->vendor->toArray();
            $vendorInfo['materials'] = array();
            $iterator = 0;
            $projectSiteInfo = array();
            foreach($purchaseOrder->purchaseOrderComponent as $purchaseOrderComponent){
                $vendorInfo['materials'][$iterator]['item_name'] = $purchaseOrderComponent->purchaseRequestComponent->materialRequestComponent->name;
                $vendorInfo['materials'][$iterator]['quantity'] = $purchaseOrderComponent['quantity'];
                $vendorInfo['materials'][$iterator]['unit'] = Unit::where('id',$purchaseOrderComponent['unit_id'])->pluck('name')->first();
                $vendorInfo['materials'][$iterator]['subtotal'] = MaterialProductHelper::customRound(($purchaseOrderComponent['quantity'] * $purchaseOrderComponent['rate_per_unit']));
                if($purchaseOrderComponent['cgst_percentage'] == null || $purchaseOrderComponent['cgst_percentage'] == ''){
                    $vendorInfo['materials'][$iterator]['cgst_percentage'] = 0;
                }else{
                    $vendorInfo['materials'][$iterator]['cgst_percentage'] = $purchaseOrderComponent['cgst_percentage'];
                }
                $vendorInfo['materials'][$iterator]['cgst_amount'] = $vendorInfo['materials'][$iterator]['subtotal'] * ($vendorInfo['materials'][$iterator]['cgst_percentage']/100);
                if($purchaseOrderComponent['sgst_percentage'] == null || $purchaseOrderComponent['sgst_percentage'] == ''){
                    $vendorInfo['materials'][$iterator]['sgst_percentage'] = 0;
                }else{
                    $vendorInfo['materials'][$iterator]['sgst_percentage'] = $purchaseOrderComponent['sgst_percentage'];
                }
                $vendorInfo['materials'][$iterator]['sgst_amount'] = $vendorInfo['materials'][$iterator]['subtotal'] * ($vendorInfo['materials'][$iterator]['sgst_percentage']/100);
                if($purchaseOrderComponent['igst_percentage'] == null || $purchaseOrderComponent['igst_percentage'] == ''){
                    $vendorInfo['materials'][$iterator]['igst_percentage'] = 0;
                }else{
                    $vendorInfo['materials'][$iterator]['igst_percentage'] = $purchaseOrderComponent['igst_percentage'];
                }
                $vendorInfo['materials'][$iterator]['igst_amount'] = $vendorInfo['materials'][$iterator]['subtotal'] * ($vendorInfo['materials'][$iterator]['igst_percentage']/100);
                $vendorInfo['materials'][$iterator]['total'] = $vendorInfo['materials'][$iterator]['subtotal'] + $vendorInfo['materials'][$iterator]['cgst_amount'] + $vendorInfo['materials'][$iterator]['sgst_amount'] + $vendorInfo['materials'][$iterator]['igst_amount'];
                if($purchaseOrderComponent['expected_delivery_date'] == null || $purchaseOrderComponent['expected_delivery_date'] == ''){
                    $vendorInfo['materials'][$iterator]['due_date'] = '';
                }else{
                    $vendorInfo['materials'][$iterator]['due_date'] = 'Due on '.date('j/n/Y',strtotime($purchaseOrderComponent['expected_delivery_date']));
                }
                $purchaseOrderRequestComponent = $purchaseOrderComponent->purchaseOrderRequestComponent;
                if($purchaseOrderRequestComponent['transportation_amount'] == null || $purchaseOrderRequestComponent['transportation_amount'] == ''){
                    $vendorInfo['materials'][$iterator]['transportation_amount'] = 0;
                }else{
                    $vendorInfo['materials'][$iterator]['transportation_amount'] = $purchaseOrderRequestComponent['transportation_amount'];
                }
                if($purchaseOrderRequestComponent['transportation_cgst_percentage'] == null || $purchaseOrderRequestComponent['transportation_cgst_percentage'] == ''){
                    $vendorInfo['materials'][$iterator]['transportation_cgst_percentage'] = 0;
                }else{
                    $vendorInfo['materials'][$iterator]['transportation_cgst_percentage'] = $purchaseOrderRequestComponent['transportation_cgst_percentage'];
                }
                if($purchaseOrderRequestComponent['transportation_sgst_percentage'] == null || $purchaseOrderRequestComponent['transportation_sgst_percentage'] == ''){
                    $vendorInfo['materials'][$iterator]['transportation_sgst_percentage'] = 0;
                }else{
                    $vendorInfo['materials'][$iterator]['transportation_sgst_percentage'] = $purchaseOrderRequestComponent['transportation_sgst_percentage'];
                }
                if($purchaseOrderRequestComponent['transportation_igst_percentage'] == null || $purchaseOrderRequestComponent['transportation_igst_percentage'] == ''){
                    $vendorInfo['materials'][$iterator]['transportation_igst_percentage'] = 0;
                }else{
                    $vendorInfo['materials'][$iterator]['transportation_igst_percentage'] = $purchaseOrderRequestComponent['transportation_igst_percentage'];
                }
                $vendorInfo['materials'][$iterator]['transportation_cgst_amount'] = ($vendorInfo['materials'][$iterator]['transportation_cgst_percentage'] * $vendorInfo['materials'][$iterator]['transportation_amount']) / 100 ;
                $vendorInfo['materials'][$iterator]['transportation_sgst_amount'] = ($vendorInfo['materials'][$iterator]['transportation_sgst_percentage'] * $vendorInfo['materials'][$iterator]['transportation_amount']) / 100 ;
                $vendorInfo['materials'][$iterator]['transportation_igst_amount'] = ($vendorInfo['materials'][$iterator]['transportation_igst_percentage'] * $vendorInfo['materials'][$iterator]['transportation_amount']) / 100 ;
                $vendorInfo['materials'][$iterator]['transportation_total_amount'] = $vendorInfo['materials'][$iterator]['transportation_amount'] + $vendorInfo['materials'][$iterator]['transportation_cgst_amount'] + $vendorInfo['materials'][$iterator]['transportation_sgst_amount'] + $vendorInfo['materials'][$iterator]['transportation_igst_amount'];
                $vendorInfo['materials'][$iterator]['hsn_code'] = $purchaseOrderComponent['hsn_code'];
                $vendorInfo['materials'][$iterator]['rate'] = $purchaseOrderComponent['rate_per_unit'];
                $iterator++;
                $projectSiteInfo['project_site_address'] = $purchaseOrderComponent->purchaseRequestComponent->materialRequestComponent->materialRequest->projectSite->address;
                if($purchaseOrder->purchaseOrderRequest->delivery_address != null){
                    $projectSiteInfo['delivery_address'] = $purchaseOrder->purchaseOrderRequest->delivery_address;
                }else{
                    $projectSiteInfo['project_name'] = $purchaseOrderComponent->purchaseRequestComponent->materialRequestComponent->materialRequest->projectSite->project->name;
                    $projectSiteInfo['project_site_name'] = $purchaseOrderComponent->purchaseRequestComponent->materialRequestComponent->materialRequest->projectSite->name;

                    if($purchaseOrderComponent->purchaseRequestComponent->materialRequestComponent->materialRequest->projectSite->city_id == null){
                        $projectSiteInfo['project_site_city'] = '';
                    }else{
                        $projectSiteInfo['project_site_city'] = $purchaseOrderComponent->purchaseRequestComponent->materialRequestComponent->materialRequest->projectSite->city->name;
                    }
                    $projectSiteInfo['delivery_address'] = $projectSiteInfo['project_name'].', '.$projectSiteInfo['project_site_name'].', '.$projectSiteInfo['project_site_address'].', '.$projectSiteInfo['project_site_city'];
                }

                /*if(count($projectSiteInfo) <= 0){
                    $projectSiteInfo['project_name'] = $purchaseOrderComponent->purchaseRequestComponent->materialRequestComponent->materialRequest->projectSite->project->name;
                    $projectSiteInfo['project_site_name'] = $purchaseOrderComponent->purchaseRequestComponent->materialRequestComponent->materialRequest->projectSite->name;
                    $projectSiteInfo['project_site_address'] = $purchaseOrderComponent->purchaseRequestComponent->materialRequestComponent->materialRequest->projectSite->address;
                    if($purchaseOrderComponent->purchaseRequestComponent->materialRequestComponent->materialRequest->projectSite->city_id == null){
                        $projectSiteInfo['project_site_city'] = '';
                    }else{
                        $projectSiteInfo['project_site_city'] = $purchaseOrderComponent->purchaseRequestComponent->materialRequestComponent->materialRequest->projectSite->city->name;
                    }
                }*/
            }
            $pdf = App::make('dompdf.wrapper');
            $pdfFlag = "purchase-order-listing-download";
            $formatId = $purchaseOrder['format_id'];
            $pdf->loadHTML(view('purchase.purchase-request.pdf.vendor-quotation')->with(compact('vendorInfo','projectSiteInfo','pdfFlag','pdfTitle','formatId')));
            if($purchaseOrder['format_id'] == null){
                return $pdf->download('PO.pdf');
            }else{
                return $pdf->download($purchaseOrder['format_id'].'.pdf');
            }
        }catch (\Exception $e){
            $data = [
                'action' => 'Download P.O. Pdf from Listing',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function createAsset(Request $request){
        try{
            $is_present = Asset::where('name','ilike',$request->name)->pluck('id')->toArray();
            if($is_present == null){
                $asset_type = AssetType::where('slug','other')->pluck('id')->first();
                $categoryAssetData['asset_types_id'] = $asset_type;
                $categoryAssetData['name'] = $request->name;
                $categoryAssetData['quantity'] = 1;
                Asset::create($categoryAssetData);
            }
            $request->session()->flash('success','New asset created successfully');
            return response()->json(['message' => 'Asset Created Successfully.'],200);
        }catch(\Exception $e){
            $data = [
                'action' => 'Get Purchase order create view',
                'exception' => $e->getMessage(),
                'params' => $request->all()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getComponentDetails(Request $request){
        try{
            $purchaseOrderComponentIds = $request->purchase_order_component_id;
            $purchaseOrderComponentData = array();
            $iterator = 0;
            $assetComponentTypeIds = MaterialRequestComponentTypes::whereIn('slug',['system-asset','new-asset'])->pluck('id')->toArray();
            foreach($purchaseOrderComponentIds as $purchaseOrderComponentId){
                $purchaseOrderComponent = PurchaseOrderComponent::findOrFail($purchaseOrderComponentId);
                $purchaseOrderComponentData[$iterator]['purchase_order_component_id'] = $purchaseOrderComponentId;
                $purchaseOrderComponentData[$iterator]['name'] = $purchaseOrderComponent->purchaseRequestComponent->materialRequestComponent->name;
                $purchaseOrderComponentData[$iterator]['unit_id'] = $purchaseOrderComponent->unit_id;
                $purchaseOrderComponentData[$iterator]['units'] = array();
                if(in_array($purchaseOrderComponent->purchaseRequestComponent->materialRequestComponent->component_type_id,$assetComponentTypeIds)){
                    $purchaseOrderComponentData[$iterator]['units'] = Unit::where('slug','nos')->select('id','name')->get()->toArray();
                    $asset = Asset::where('name','ilike',$purchaseOrderComponentData[$iterator]['name'])->first();
                    $otherAssetTypeId = AssetType::where('slug','other')->pluck('id')->first();
                    if($asset != null && $asset->asset_types_id != $otherAssetTypeId){
                        $quantityIsFixed = true;
                    }else{
                        $quantityIsFixed = false;
                    }
                }else{
                    $quantityIsFixed = false;
                    $newMaterialTypeId = MaterialRequestComponentTypes::where('slug', 'new-material')->pluck('id')->first();
                    if ($newMaterialTypeId == $purchaseOrderComponent->purchaseRequestComponent->materialRequestComponent->component_type_id) {
                        $purchaseOrderComponentData[$iterator]['units'] = Unit::where('is_active', true)->select('id', 'name')->orderBy('name')->get()->toArray();
                    } else {
                        $material = Material::where('name', 'ilike', $purchaseOrderComponent->purchaseRequestComponent->materialRequestComponent->name)->first();
                        $unit1Array = UnitConversion::join('units', 'units.id', '=', 'unit_conversions.unit_2_id')
                            ->where('unit_conversions.unit_1_id', $material->unit_id)
                            ->select('units.id as id', 'units.name as name')
                            ->get()
                            ->toArray();
                        $units2Array = UnitConversion::join('units', 'units.id', '=', 'unit_conversions.unit_1_id')
                            ->where('unit_conversions.unit_2_id', $material->unit_id)
                            ->whereNotIn('unit_conversions.unit_1_id', array_column($unit1Array, 'id'))
                            ->select('units.id as id', 'units.name as name')
                            ->get()
                            ->toArray();
                        $purchaseOrderComponentData[$iterator]['units'] = array_merge($unit1Array, $units2Array);
                        $purchaseOrderComponentData[$iterator]['units'][] = [
                            'id' => $material->unit->id,
                            'name' => $material->unit->name,
                        ];
                    }
                }
               $iterator++;
            }
            return view('partials.purchase.purchase-order.transaction-component-listing')->with(compact('purchaseOrderComponentData','quantityIsFixed'));
        }catch(\Exception $e){
            $data = [
                'action' => 'Get Purchase Order component Details',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            $status = 500;
            $response = null;
            return response()->json($response,$status);
        }
    }

    public function preGrnImageUpload(Request $request){
        try{
            $generatedGrn = $this->generateGRN();
            $purchaseOrder = PurchaseOrder::findOrFail($request->purchase_order_id);
            $grnGeneratedStatusId = PurchaseOrderTransactionStatus::where('slug','grn-generated')->pluck('id')->first();
            $purchaseOrderTransactionData = [
                'purchase_order_id' => $purchaseOrder->id,
                'purchase_order_transaction_status_id' => $grnGeneratedStatusId,
                'grn' => $generatedGrn
            ];
            $purchaseOrderTransaction = PurchaseOrderTransaction::create($purchaseOrderTransactionData);
            $purchaseOrderDirectoryName = sha1($purchaseOrder->id);
            $purchaseTransactionDirectoryName = sha1($purchaseOrderTransaction->id);
            $imageUploadPath = public_path().env('PURCHASE_ORDER_IMAGE_UPLOAD').DIRECTORY_SEPARATOR.$purchaseOrderDirectoryName.DIRECTORY_SEPARATOR.'bill_transaction'.DIRECTORY_SEPARATOR.$purchaseTransactionDirectoryName;
            if (!file_exists($imageUploadPath)) {
                File::makeDirectory($imageUploadPath, $mode = 0777, true, true);
            }
            foreach($request->pre_grn_image as $preGrnImage){
                $imageArray = explode(';',$preGrnImage);
                $image = explode(',',$imageArray[1])[1];
                $pos  = strpos($preGrnImage, ';');
                $type = explode(':', substr($preGrnImage, 0, $pos))[1];
                $extension = explode('/',$type)[1];
                $filename = mt_rand(1,10000000000).sha1(time()).".{$extension}";
                $fileFullPath = $imageUploadPath.DIRECTORY_SEPARATOR.$filename;
                $transactionImageData = [
                    'purchase_order_transaction_id' => $purchaseOrderTransaction->id,
                    'name' => $filename,
                    'is_pre_grn' => true
                ];
                file_put_contents($fileFullPath,base64_decode($image));
                PurchaseOrderTransactionImage::create($transactionImageData);
            }
            $response = [
                'purchase_order_transaction_id' => $purchaseOrderTransaction->id,
                'grn' => $generatedGrn
            ];
            $status = 200;
        }catch (\Exception $e){
            $data = [
                'action' => 'Upload Pre GRN images',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            $response = array();
            $status = 500;
        }
        return response()->json($response,$status);
    }

    public function checkGeneratedGRN(Request $request,$purchaseOrder){
        try{
            $response = array();
            $grnGeneratedId = PurchaseOrderTransactionStatus::where('slug','grn-generated')->pluck('id')->first();
            $grnGeneratedTransaction = PurchaseOrderTransaction::where('purchase_order_transaction_status_id',$grnGeneratedId)->where('purchase_order_id',$purchaseOrder->id)->orderBy('created_at','desc')->first();
            if($grnGeneratedTransaction != null){
                $response['grn'] = $grnGeneratedTransaction->grn;
                $response['purchase_order_transaction_id'] = $grnGeneratedTransaction->id;
                $transactionImages = PurchaseOrderTransactionImage::where('purchase_order_transaction_id',$grnGeneratedTransaction->id)->where('is_pre_grn', true)->get();
                $response['images'] = array();
                $purchaseOrderDirectoryName = sha1($purchaseOrder->id);
                $purchaseTransactionDirectoryName = sha1($grnGeneratedTransaction->id);
                $imagePath = env('PURCHASE_ORDER_IMAGE_UPLOAD').DIRECTORY_SEPARATOR.$purchaseOrderDirectoryName.DIRECTORY_SEPARATOR.'bill_transaction'.DIRECTORY_SEPARATOR.$purchaseTransactionDirectoryName;
                foreach ($transactionImages as $image){
                    $response['images'][] = $imagePath.DIRECTORY_SEPARATOR.$image['name'];
                }
                $status = 200;
            }else{
                $status = 204;
            }
        }catch(\Exception $e){
            $data = [
                'action' => 'Check GRN Generated transaction',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            $response = array();
            $status = 500;
        }
        return response()->json($response,$status);
    }

    public function getTransactionEditView(Request $request, $purchaseOrderTransaction){
        try{
            $purchaseOrder = $purchaseOrderTransaction->purchaseOrder;
            $vendorName = $purchaseOrder->vendor->company;
            $materialList = array();
            $assetComponentTypeIds = MaterialRequestComponentTypes::whereIn('slug',['system-asset','new-asset'])->pluck('id')->toArray();
            $iterator = 0;
            $purchaseOrderBill = PurchaseOrderBillTransactionRelation::where('purchase_order_transaction_id',$purchaseOrderTransaction->id)->first();
            if($purchaseOrderBill == null){
                $canEdit = true;
            }else{
                $canEdit = false;
            }
            $isShowTaxes = $request->isShowTax;
            if($isShowTaxes == true || $isShowTaxes == 'true'){
                $canEdit = false;
            }
            foreach($purchaseOrderTransaction->purchaseOrderTransactionComponents as $purchaseOrderTransactionComponent){
                $purchaseOrderComponent = $purchaseOrderTransactionComponent->purchaseOrderComponent;
                $materialRequestComponent = $purchaseOrderComponent->purchaseRequestComponent->materialRequestComponent;
                $materialList[$iterator]['purchase_order_component_id'] = $purchaseOrderComponent['id'];
                $materialList[$iterator]['material_request_component_id'] = $materialRequestComponent['id'];
                $materialList[$iterator]['name'] = $materialRequestComponent['name'];
                $materialList[$iterator]['material_component_unit_id'] = $materialRequestComponent['unit_id'];
                $materialList[$iterator]['material_component_unit_name'] = $materialRequestComponent->unit->name;
                $materialList[$iterator]['material_component_quantity'] = $materialRequestComponent->quantity;
                $materialList[$iterator]['unit_id'] = $purchaseOrderComponent->unit_id;
                $materialList[$iterator]['quantity'] = $purchaseOrderTransactionComponent->quantity;
                $materialList[$iterator]['units'] = array();
                if(in_array($purchaseOrderComponent->purchaseRequestComponent->materialRequestComponent->component_type_id,$assetComponentTypeIds)){
                    $materialList[$iterator]['units'] = Unit::where('slug','nos')->select('id','name')->get()->toArray();
                    $asset = Asset::where('name','ilike',$materialList[$iterator]['name'])->first();
                    $otherAssetTypeId = AssetType::where('slug','other')->pluck('id')->first();
                    if($asset != null && $asset->asset_types_id != $otherAssetTypeId){
                        $materialList[$iterator]['quantityIsFixed'] = true;
                    }else{
                        $materialList[$iterator]['quantityIsFixed'] = false;
                    }
                    $materialList[$iterator]['rate_per_unit'] = $purchaseOrderComponent->rate_per_unit;
                }else{
                    $materialList[$iterator]['quantityIsFixed'] = false;
                    $materialList[$iterator]['rate_per_unit'] = UnitHelper::unitConversion($purchaseOrderComponent->unit_id,$purchaseOrderTransactionComponent->unit_id,$purchaseOrderComponent->rate_per_unit);
                    $newMaterialTypeId = MaterialRequestComponentTypes::where('slug', 'new-material')->pluck('id')->first();
                    if ($newMaterialTypeId == $purchaseOrderComponent->purchaseRequestComponent->materialRequestComponent->component_type_id) {
                        $materialList[$iterator]['units'] = Unit::where('is_active', true)->select('id', 'name')->orderBy('name')->get()->toArray();
                    } else {
                        $material = Material::where('name', 'ilike', $purchaseOrderComponent->purchaseRequestComponent->materialRequestComponent->name)->first();
                        $unit1Array = UnitConversion::join('units', 'units.id', '=', 'unit_conversions.unit_2_id')
                            ->where('unit_conversions.unit_1_id', $material->unit_id)
                            ->select('units.id as id', 'units.name as name')
                            ->get()
                            ->toArray();
                        $units2Array = UnitConversion::join('units', 'units.id', '=', 'unit_conversions.unit_1_id')
                            ->where('unit_conversions.unit_2_id', $material->unit_id)
                            ->whereNotIn('unit_conversions.unit_1_id', array_column($unit1Array, 'id'))
                            ->select('units.id as id', 'units.name as name')
                            ->get()
                            ->toArray();
                        $materialList[$iterator]['units'] = array_merge($unit1Array, $units2Array);
                        $materialList[$iterator]['units'][] = [
                            'id' => $material->unit->id,
                            'name' => $material->unit->name,
                        ];
                    }
                }
                if($purchaseOrderComponent->cgst_percentage != null || $purchaseOrderComponent->cgst_percentage != ''){
                    $materialList[$iterator]['cgst_percentage'] = $purchaseOrderComponent->cgst_percentage;
                }else{
                    $materialList[$iterator]['cgst_percentage'] = 0;
                }
                if($purchaseOrderComponent->sgst_percentage != null || $purchaseOrderComponent->sgst_percentage != ''){
                    $materialList[$iterator]['sgst_percentage'] = $purchaseOrderComponent->sgst_percentage;
                }else{
                    $materialList[$iterator]['sgst_percentage'] = 0;
                }
                if($purchaseOrderComponent->igst_percentage != null || $purchaseOrderComponent->igst_percentage != ''){
                    $materialList[$iterator]['igst_percentage'] = $purchaseOrderComponent->igst_percentage;
                }else{
                    $materialList[$iterator]['igst_percentage'] = 0;
                }
                $iterator++;
            }
            $preGrnImages = PurchaseOrderTransactionImage::where('purchase_order_transaction_id',$purchaseOrderTransaction->id)->where('is_pre_grn', true)->get();
            $postGrnImages = PurchaseOrderTransactionImage::where('purchase_order_transaction_id',$purchaseOrderTransaction->id)->where('is_pre_grn', false)->get();
            $purchaseOrderDirectoryName = sha1($purchaseOrderTransaction->purchase_order_id);
            $purchaseTransactionDirectoryName = sha1($purchaseOrderTransaction->id);
            $imageUploadPath = env('PURCHASE_ORDER_IMAGE_UPLOAD').DIRECTORY_SEPARATOR.$purchaseOrderDirectoryName.DIRECTORY_SEPARATOR.'bill_transaction'.DIRECTORY_SEPARATOR.$purchaseTransactionDirectoryName;
            $preGrnImagePaths = array();
            $postGrnImagePaths = array();
            $iterator = 0;
            while($iterator < count($preGrnImages) && $iterator < count($postGrnImages)){
                $preGrnImagePaths[] = $imageUploadPath.DIRECTORY_SEPARATOR.$preGrnImages[$iterator]->name;
                $postGrnImagePaths[] = $imageUploadPath.DIRECTORY_SEPARATOR.$postGrnImages[$iterator]->name;
                $iterator++;
            }
            while($iterator < count($postGrnImages)){
                $postGrnImagePaths[] = $imageUploadPath.DIRECTORY_SEPARATOR.$postGrnImages[$iterator]->name;
                $iterator++;
            }
            while($iterator < count($preGrnImages)){
                $preGrnImagePaths[] = $imageUploadPath.DIRECTORY_SEPARATOR.$preGrnImages[$iterator]->name;
                $iterator++;
            }
            return view('partials.purchase.purchase-order.edit-transaction')->with(compact('purchaseOrderTransaction','preGrnImagePaths','postGrnImagePaths','materialList','vendorName','quantityIsFixed','canEdit','isShowTaxes'));
        }catch(\Exception $e){
            $data = [
                'action' => 'Get Purchase Order Transaction Edit View',
                'params' => $request->all(),
                'transaction' => $purchaseOrderTransaction,
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            return response()->json([],500);
        }
    }

    public function transactionEdit(Request $request,$purchaseOrderTransaction){
        try{
            $purchaseOrderTransactionData = $request->except('_token','component_data','vendor_name','purchase_order_id');
            $purchaseOrderTransaction->update($purchaseOrderTransactionData);
            foreach($request->component_data as $purchaseOrderComponentId => $purchaseOrderComponentData) {
                $purchaseOrderTransactionComponent = PurchaseOrderTransactionComponent::where('purchase_order_component_id',$purchaseOrderComponentId)->first();
                $purchaseOrderTransactionComponentData = [
                    'quantity' => $purchaseOrderComponentData['quantity'],
                    'unit_id' => $purchaseOrderComponentData['unit_id'],
                ];
                $purchaseOrderTransactionComponent->update($purchaseOrderTransactionComponentData);
            }
            return redirect('/purchase/purchase-order/edit/'.$purchaseOrderTransaction->purchase_order_id);
        }catch(\Exception $e){
            $data = [
                'action' => 'Edit Purchase Order Transaction',
                'params' => $request->all(),
                'transaction' => $purchaseOrderTransaction,
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getComponentTaxData(Request $request,$purchaseRequestComponent){
        try{
            $data = [
                'purchase_request_component_id' => $purchaseRequestComponent->id,
                'name' => $purchaseRequestComponent->materialRequestComponent->name,
                'vendor_id' => $request->vendor_id
            ];
            if($request->has('rate') && $request->rate != null){
                $data['rate'] = $request->rate;
            }else{
                $data['rate'] = '';
            }
            if($request->has('quantity') && $request->quantity != null){
                $data['quantity'] = $request->quantity;
            }else{
                $data['quantity'] = '';
            }
            if($request->has('cgst_percentage') && $request->cgst_percentage != null){
                $data['cgst_percentage'] = $request->cgst_percentage;
            }else{
                $data['cgst_percentage'] = '';
            }
            if($request->has('igst_percentage') && $request->igst_percentage != null){
                $data['igst_percentage'] = $request->igst_percentage;
            }else{
                $data['igst_percentage'] = '';
            }
            if($request->has('sgst_percentage') && $request->sgst_percentage != null){
                $data['sgst_percentage'] = $request->sgst_percentage;
            }else{
                $data['sgst_percentage'] = '';
            }
            return view('partials.purchase.purchase-order.component-tax-modal')->with(compact('data'));
        }catch(\Exception $e){
            $data = [
                'action' => 'Get purchase request component tax data',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            $status = 500;
            $response = [];
            return response()->json($response,$status);
        }
    }

    public function getAdvancePaymentListing(Request $request){
        try{
            $status = 200;
            $paymentData = PurchaseOrderAdvancePayment::where('purchase_order_id',$request->purchase_order_id)->orderBy('created_at','desc')->get();
            $iTotalRecords = count($paymentData);
            $records = array();
            $records['data'] = array();
            if($request->length == -1){
                $length = $iTotalRecords;
            }else{
                $length = $request->length;
            }
            for($iterator = 0,$pagination = $request->start; $iterator < $length && $iterator < count($paymentData); $iterator++,$pagination++ ){
                $records['data'][] = [
                    date('d M Y',strtotime($paymentData[$pagination]['created_at'])),
                    $paymentData[$pagination]['amount'],
                    ($paymentData[$pagination]->paymentType != null) ? ucfirst($paymentData[$pagination]->paid_from_slug).' - '.$paymentData[$pagination]->paymentType->name : ucfirst($paymentData[$pagination]->paid_from_slug),
                    $paymentData[$pagination]['reference_number']
                ];
            }
            $records["draw"] = intval($request->draw);
            $records["recordsTotal"] = $iTotalRecords;
            $records["recordsFiltered"] = $iTotalRecords;
        }catch (\Exception $e){
            $data = [
                'action' => 'Get Purchase Order Advance Payment Listing',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            $status = 500;
            $records = [];
        }
        return response()->json($records,$status);
    }

    public function editPurchaseOrder(Request $request,$purchaseOrder){
        try{
            $purchaseOrderComponent = PurchaseOrderComponent::findOrFail($request->purchase_order_component_id);
            $subTotal = $request->quantity * $purchaseOrderComponent['rate_per_unit'];
            $cgst_amount = $purchaseOrderComponent['cgst_percentage'] * $subTotal;
            $sgst_amount = $purchaseOrderComponent['sgst_percentage'] * $subTotal;
            $igst_amount = $purchaseOrderComponent['igst_percentage'] * $subTotal;
            $total = $subTotal + $cgst_amount + $sgst_amount + $igst_amount;
            $purchaseOrderComponent->update([
                'quantity' => $request->quantity,
                'cgst_amount' => $cgst_amount,
                'sgst_amount'=> $sgst_amount,
                'igst_amount' => $igst_amount,
                'total' => $total,
            ]);
            $assetComponentTypeIds = MaterialRequestComponentTypes::whereIn('slug',['new-material','system-asset'])->pluck('id')->toArray();
            $projectSiteInfo = array();
            $projectSiteInfo['project_name'] = $purchaseOrder->purchaseRequest->projectSite->project->name;
            $projectSiteInfo['project_site_name'] = $purchaseOrder->purchaseRequest->projectSite->name;
            $projectSiteInfo['project_site_address'] = $purchaseOrder->purchaseRequest->projectSite->address;
            $pdfFlag = 'after-purchase-order-create';
            if($purchaseOrder->purchaseRequest->projectSite->city_id == null){
                $projectSiteInfo['project_site_city'] = '';
            }else{
                $projectSiteInfo['project_site_city'] = $purchaseOrder->purchaseRequest->projectSite->city->name;
            }
            if($purchaseOrder->purchaseOrderRequest->delivery_address != null){
                $projectSiteInfo['delivery_address'] = $purchaseOrder->purchaseOrderRequest->delivery_address;
            }else{
                $projectSiteInfo['delivery_address'] = $projectSiteInfo['project_name'].', '.$projectSiteInfo['project_site_name'].', '.$projectSiteInfo['project_site_address'].', '.$projectSiteInfo['project_site_city'];
            }
            if($purchaseOrder->is_client_order == true){
                $vendorInfo = Client::findOrFail($purchaseOrder->client_id)->toArray();
            }else{
                $vendorInfo = Vendor::findOrFail($purchaseOrder->vendor_id)->toArray();
            }
            $vendorInfo['materials'][] = [
                'item_name' => $purchaseOrderComponent->purchaseRequestComponent->materialRequestComponent->name,
                'quantity' => $purchaseOrderComponent['quantity'],
                'unit' => $purchaseOrderComponent->unit->name,
                'hsn_code' => $purchaseOrderComponent->hsn_code,
                'rate' => $purchaseOrderComponent->rate_per_unit,
                'due_date' => 'Due on '.date('j/n/Y',strtotime($purchaseOrderComponent['expected_delivery_date'])),
                'subtotal' => MaterialProductHelper::customRound(($purchaseOrderComponent['quantity'] * $purchaseOrderComponent['rate_per_unit']))
            ];
            $iterator = 0;
            if($purchaseOrderComponent['cgst_percentage'] == null || $purchaseOrderComponent['cgst_percentage'] == ''){
                $vendorInfo['materials'][$iterator]['cgst_percentage'] = 0;
            }else{
                $vendorInfo['materials'][$iterator]['cgst_percentage'] = $purchaseOrderComponent['cgst_percentage'];
            }
            $vendorInfo['materials'][$iterator]['cgst_amount'] = $vendorInfo['materials'][$iterator]['subtotal'] * ($vendorInfo['materials'][$iterator]['cgst_percentage']/100);
            if($purchaseOrderComponent['sgst_percentage'] == null || $purchaseOrderComponent['sgst_percentage'] == ''){
                $vendorInfo['materials'][$iterator]['sgst_percentage'] = 0;
            }else{
                $vendorInfo['materials'][$iterator]['sgst_percentage'] = $purchaseOrderComponent['sgst_percentage'];
            }
            $vendorInfo['materials'][$iterator]['sgst_amount'] = $vendorInfo['materials'][$iterator]['subtotal'] * ($vendorInfo['materials'][$iterator]['sgst_percentage']/100);
            if($purchaseOrderComponent['igst_percentage'] == null || $purchaseOrderComponent['igst_percentage'] == ''){
                $vendorInfo['materials'][$iterator]['igst_percentage'] = 0;
            }else{
                $vendorInfo['materials'][$iterator]['igst_percentage'] = $purchaseOrderComponent['igst_percentage'];
            }
            $vendorInfo['materials'][$iterator]['igst_amount'] = $vendorInfo['materials'][$iterator]['subtotal'] * ($vendorInfo['materials'][$iterator]['igst_percentage']/100);
            $vendorInfo['materials'][$iterator]['total'] = $vendorInfo['materials'][$iterator]['subtotal'] + $vendorInfo['materials'][$iterator]['cgst_amount'] + $vendorInfo['materials'][$iterator]['sgst_amount'] + $vendorInfo['materials'][$iterator]['igst_amount'];
            if($purchaseOrderComponent['expected_delivery_date'] == null || $purchaseOrderComponent['expected_delivery_date'] == ''){
                $vendorInfo['materials'][$iterator]['due_date'] = '';
            }else{
                $vendorInfo['materials'][$iterator]['due_date'] = 'Due on '.date('j/n/Y',strtotime($purchaseOrderComponent['expected_delivery_date']));
            }
            $purchaseOrderRequestComponent = $purchaseOrderComponent->purchaseOrderRequestComponent;
            if($purchaseOrderRequestComponent['transportation_amount'] == null || $purchaseOrderRequestComponent['transportation_amount'] == ''){
                $vendorInfo['materials'][$iterator]['transportation_amount'] = 0;
            }else{
                $vendorInfo['materials'][$iterator]['transportation_amount'] = $purchaseOrderRequestComponent['transportation_amount'];
            }
            if($purchaseOrderRequestComponent['transportation_cgst_percentage'] == null || $purchaseOrderRequestComponent['transportation_cgst_percentage'] == ''){
                $vendorInfo['materials'][$iterator]['transportation_cgst_percentage'] = 0;
            }else{
                $vendorInfo['materials'][$iterator]['transportation_cgst_percentage'] = $purchaseOrderRequestComponent['transportation_cgst_percentage'];
            }
            if($purchaseOrderRequestComponent['transportation_sgst_percentage'] == null || $purchaseOrderRequestComponent['transportation_sgst_percentage'] == ''){
                $vendorInfo['materials'][$iterator]['transportation_sgst_percentage'] = 0;
            }else{
                $vendorInfo['materials'][$iterator]['transportation_sgst_percentage'] = $purchaseOrderRequestComponent['transportation_sgst_percentage'];
            }
            if($purchaseOrderRequestComponent['transportation_igst_percentage'] == null || $purchaseOrderRequestComponent['transportation_igst_percentage'] == ''){
                $vendorInfo['materials'][$iterator]['transportation_igst_percentage'] = 0;
            }else{
                $vendorInfo['materials'][$iterator]['transportation_igst_percentage'] = $purchaseOrderRequestComponent['transportation_igst_percentage'];
            }
            $vendorInfo['materials'][$iterator]['transportation_cgst_amount'] = ($vendorInfo['materials'][$iterator]['transportation_cgst_percentage'] * $vendorInfo['materials'][$iterator]['transportation_amount']) / 100 ;
            $vendorInfo['materials'][$iterator]['transportation_sgst_amount'] = ($vendorInfo['materials'][$iterator]['transportation_sgst_percentage'] * $vendorInfo['materials'][$iterator]['transportation_amount']) / 100 ;
            $vendorInfo['materials'][$iterator]['transportation_igst_amount'] = ($vendorInfo['materials'][$iterator]['transportation_igst_percentage'] * $vendorInfo['materials'][$iterator]['transportation_amount']) / 100 ;
            $vendorInfo['materials'][$iterator]['transportation_total_amount'] = $vendorInfo['materials'][$iterator]['transportation_amount'] + $vendorInfo['materials'][$iterator]['transportation_cgst_amount'] + $vendorInfo['materials'][$iterator]['transportation_sgst_amount'] + $vendorInfo['materials'][$iterator]['transportation_igst_amount'];
            if(in_array($purchaseOrderComponent->purchaseRequestComponent->materialRequestComponent->component_type_id,$assetComponentTypeIds)){
                $vendorInfo['materials'][0]['gst'] = '-';
            }else{
                $vendorInfo['materials'][0]['gst'] = Material::where('name','ilike',$purchaseOrderComponent->purchaseRequestComponent->materialRequestComponent->name)->pluck('gst')->first();
                if($vendorInfo['materials'][0]['gst'] == null){
                    $vendorInfo['materials'][0]['gst'] = '-';
                }
            }

            if($vendorInfo['email'] != null){
                $pdfTitle = "Purchase Order";
                $formatId = $purchaseOrder['format_id'];
                $pdf = App::make('dompdf.wrapper');
                $pdf->loadHTML(view('purchase.purchase-request.pdf.vendor-quotation')->with(compact('vendorInfo','projectSiteInfo','pdfFlag','pdfTitle','formatId')));
                $pdfDirectoryPath = env('PURCHASE_VENDOR_ASSIGNMENT_PDF_FOLDER');
                $pdfFileName = sha1($vendorInfo['id']).'.pdf';
                $pdfUploadPath = public_path().$pdfDirectoryPath.'/'.$pdfFileName;
                $pdfContent = $pdf->stream();
                if(file_exists($pdfUploadPath)){
                    unlink($pdfUploadPath);
                }
                if (!file_exists($pdfDirectoryPath)) {
                    File::makeDirectory(public_path().$pdfDirectoryPath, $mode = 0777, true, true);
                }
                file_put_contents($pdfUploadPath,$pdfContent);
                $mailMessage = 'Attached herewith the Purchase Order '.$purchaseOrder->format_id;
                $mailData = ['path' => $pdfUploadPath, 'toMail' => $vendorInfo['email']];
                Mail::send('purchase.purchase-request.email.vendor-quotation', ['mailMessage' => $mailMessage], function($message) use ($mailData){
                    $message->subject('Testing with attachment');
                    $message->to($mailData['toMail']);
                    $message->from(env('MAIL_USERNAME'));
                    $message->attach($mailData['path']);
                });
                if($purchaseOrder->is_client_order == true){
                    $mailInfoData = [
                        'user_id' => Auth::user()->id,
                        'type_slug' => 'for-purchase-order',
                        'is_client' => true,
                        'reference_id' => $purchaseOrder->id,
                        'client_id' => $vendorInfo['id'],
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ];
                }else{
                    $mailInfoData = [
                        'user_id' => Auth::user()->id,
                        'type_slug' => 'for-purchase-order',
                        'is_client' => false,
                        'reference_id' => $purchaseOrder->id,
                        'vendor_id' => $vendorInfo['id'],
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ];
                }
                PurchaseRequestComponentVendorMailInfo::insert($mailInfoData);
                unlink($pdfUploadPath);
            }
            $request->session()->flash('success', 'Purchase Order Edited Successfully.');
            return \redirect('/purchase/purchase-order/edit/'.$purchaseOrder->id);
        }catch(\Exception $e){
            $data = [
                'action' => 'Edit Purchase Orders',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getPurchaseOrderDetails(Request $request, $purchaseOrderId){
        try{
            $purchaseOrder = PurchaseOrder::where('id',$purchaseOrderId)->first();
            return view('partials.purchase.purchase-order.component-detail')->with(compact('purchaseOrder'));
        }catch(\Exception $e){
            $data = [
                'action' => 'Get Purchase Order Details',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            return response()->json([],500);
        }
    }

    public function authenticatePOClose(Request $request){
        try{
            $password = $request->password;
            if(Hash::check($password, env('CLOSE_PURCHASE_ORDER_PASSWORD'))){
                $status = 200;
                $message = 'Authentication successful !!';
            }else{
                $status = 401;
                $message = 'You are not authorised to close this purchase order.';
            }
        }catch (\Exception $e){
            $message = 'Fail';
            $status = 500;
            $data = [
                'action' => 'Authenticate Purchase Order Close',
                'exception' => $e->getMessage(),
                'params' => $request->all()
            ];
            Log::critical(json_encode($data));
        }
        $response = [
            'message' => $message,
        ];
        return response()->json($response,$status);
    }
}
