<?php

namespace App\Http\Controllers\Purchase;

use App\Address;
use App\Asset;
use App\AssetType;
use App\Category;
use App\CategoryMaterialRelation;
use App\Client;
use App\Helper\MaterialProductHelper;
use App\Helper\UnitHelper;
use App\Http\Controllers\CustomTraits\Notification\NotificationTrait;
use App\Http\Controllers\CustomTraits\Purchase\MaterialRequestTrait;
use App\Material;
use App\MaterialRequestComponentTypes;
use App\MaterialVersion;
use App\Module;
use App\ProjectSite;
use App\PurchaseOrder;
use App\PurchaseOrderComponent;
use App\PurchaseOrderComponentImage;
use App\PurchaseOrderRequest;
use App\PurchaseOrderRequestComponent;
use App\PurchaseOrderRequestComponentImage;
use App\PurchaseOrderStatus;
use App\PurchaseRequest;
use App\PurchaseRequestComponent;
use App\PurchaseRequestComponentStatuses;
use App\PurchaseRequestComponentVendorMailInfo;
use App\Unit;
use App\User;
use App\UserLastLogin;
use App\Vendor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class PurchaseOrderRequestController extends Controller
{
    use MaterialRequestTrait;
    use NotificationTrait;
    public function __construct(){
        $this->middleware('custom.auth');
    }
    public function getManageView(Request $request){
        try{
            return view('purchase.purchase-order-request.manage');
        }catch(\Exception $e){
            $data = [
                'action' => 'Get purchase order request manage view',
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getCreateView(Request $request){
        try{
            $projectSiteId = Session::get('global_project_site');
            $projectSiteInfo = ProjectSite::where('id',$projectSiteId)->first();
            if($projectSiteInfo->city_id == null){
                $deliveryAddresses[0] = $projectSiteInfo->project->name.', '.$projectSiteInfo->name.', '.$projectSiteInfo->address;
            }else{
                $deliveryAddresses[0] = $projectSiteInfo->project->name.', '.$projectSiteInfo->name.', '.$projectSiteInfo->address.', '.$projectSiteInfo->city->name.', '.$projectSiteInfo->city->state->name;
            }
            $systemAddresses = Address::where('is_active',true)->get();
            $iterator = 1;
            foreach ($systemAddresses as $address){
                $deliveryAddresses[$iterator] = $address->address.', '.$address->cities->name.', '.$address->cities->state->name;
                $iterator++;
            }
            return view('purchase.purchase-order-request.create')->with(compact('deliveryAddresses'));
        }catch(\Exception $e){
            $data = [
                'action' => 'Get purchase order request create view',
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function createPurchaseOrderRequest(Request $request){
        try{
            $user = Auth::user();
            $purchaseOrderRequestData = [
                'purchase_request_id' => $request->purchase_request_id,
                'user_id' => $user->id,
                'delivery_address' => $request->delivery_address
            ];
            $projectSiteInfo = PurchaseRequest::join('project_sites','project_sites.id','=','purchase_requests.project_site_id')
                                        ->join('projects','projects.id','=','project_sites.project_id')
                                        ->where('purchase_requests.id', $request->purchase_request_id)
                                        ->select('project_sites.id as project_site_id','project_sites.name as project_site_name','projects.name as project_name')
                                        ->first()->toArray();
            $purchaseOrderRequest = PurchaseOrderRequest::create($purchaseOrderRequestData);
            $purchaseOrderRequestApproveAclTokens = User::join('user_project_site_relation','user_project_site_relation.user_id','=','users.id')
                ->join('user_has_permissions','user_has_permissions.user_id','=','user_project_site_relation.user_id')
                ->join('permissions','permissions.id','=','user_has_permissions.permission_id')
                ->where('user_project_site_relation.project_site_id', $projectSiteInfo['project_site_id'])
                ->where('permissions.name','approve-purchase-order-request')
                ->select('users.web_fcm_token as web_fcm_token','users.mobile_fcm_token as mobile_fcm_token')
                ->get()->toArray();
            $webTokens = array_column($purchaseOrderRequestApproveAclTokens,'web_fcm_token');
            $mobileTokens = array_column($purchaseOrderRequestApproveAclTokens,'mobile_fcm_token');
            $notificationString = $projectSiteInfo['project_name'].'-'.$projectSiteInfo['project_site_name'].' : Purchase Order waiting for approval.';
            $this->sendPushNotification('Manisha Construction',$notificationString,$webTokens,$mobileTokens,'c-p-o-r');
            foreach($request['data'] as $purchaseRequestComponentVendorRelationId => $componentData){
                if($componentData['rate_per_unit'] == '-'){
                    $componentData['rate_per_unit'] = 0;
                }
                $date = explode('/',$componentData['expected_delivery_date']);
                $expectedDeliveryDate  = $date[2].'-'.$date[1].'-'.$date[0];
                $purchaseOrderRequestComponentData = [
                    'purchase_order_request_id' => $purchaseOrderRequest->id,
                    'purchase_request_component_vendor_relation_id' => $purchaseRequestComponentVendorRelationId,
                    'rate_per_unit' => $componentData['rate_per_unit'],
                    'quantity' => $componentData['quantity'],
                    'unit_id' => $componentData['unit_id'],
                    'hsn_code' => $componentData['hsn_code'],
                    'expected_delivery_date' => date('Y-m-d H:i:s',strtotime($expectedDeliveryDate)),
                    'cgst_percentage' => $componentData['cgst_percentage'],
                    'sgst_percentage' => $componentData['sgst_percentage'],
                    'igst_percentage' => $componentData['igst_percentage'],
                    'cgst_amount' => $componentData['cgst_amount'],
                    'sgst_amount' => $componentData['sgst_amount'],
                    'igst_amount' => $componentData['igst_amount'],
                    'total' => $componentData['total'],
                    'category_id' => $componentData['category_id'],
                    'transportation_amount' => $componentData['transportation_amount'],
                    'transportation_cgst_percentage' => $componentData['transportation_cgst_percentage'],
                    'transportation_sgst_percentage' => $componentData['transportation_sgst_percentage'],
                    'transportation_igst_percentage' => $componentData['transportation_igst_percentage']
                ];
                $purchaseOrderRequestComponent = PurchaseOrderRequestComponent::create($purchaseOrderRequestComponentData);

                if(array_key_exists('client_images',$componentData)) {
                    $mainDirectoryName = sha1($purchaseOrderRequest->id);
                    $componentDirectoryName = sha1($purchaseOrderRequestComponent->id);
                    $userDirectoryName = sha1($user['id']);
                    $tempImageUploadPath = public_path() . env('PURCHASE_ORDER_REQUEST_TEMP_IMAGE_UPLOAD') . DIRECTORY_SEPARATOR . $userDirectoryName;
                    $imageUploadPath = public_path() . env('PURCHASE_ORDER_REQUEST_IMAGE_UPLOAD') . DIRECTORY_SEPARATOR . $mainDirectoryName . DIRECTORY_SEPARATOR . 'client_approval_images' . DIRECTORY_SEPARATOR . $componentDirectoryName;
                    foreach ($componentData['client_images'] as $image) {

                        $imageName = basename($image);
                        $newTempImageUploadPath = $tempImageUploadPath . '/' . $imageName;
                        $imageData = [
                            'purchase_order_request_component_id' => $purchaseOrderRequestComponent['id'],
                            'name' => $imageName,
                            'caption' => '',
                            'is_vendor_approval' => false
                        ];
                        PurchaseOrderRequestComponentImage::create($imageData);
                        if (!file_exists($imageUploadPath)) {
                            File::makeDirectory($imageUploadPath, $mode = 0777, true, true);
                        }
                        if (File::exists($newTempImageUploadPath)) {
                            $imageUploadNewPath = $imageUploadPath . DIRECTORY_SEPARATOR . $imageName;
                            File::move($newTempImageUploadPath, $imageUploadNewPath);
                        }
                    }
                    if (count(scandir($tempImageUploadPath)) <= 2) {
                        rmdir($tempImageUploadPath);
                    }
                }
                if(array_key_exists('vendor_images',$componentData)) {
                    $mainDirectoryName = sha1($purchaseOrderRequest->id);
                    $componentDirectoryName = sha1($purchaseOrderRequestComponent->id);
                    $userDirectoryName = sha1($user['id']);
                    $tempImageUploadPath = public_path() . env('PURCHASE_ORDER_REQUEST_TEMP_IMAGE_UPLOAD') . DIRECTORY_SEPARATOR . $userDirectoryName;
                    $imageUploadPath = public_path() . env('PURCHASE_ORDER_REQUEST_IMAGE_UPLOAD') . DIRECTORY_SEPARATOR . $mainDirectoryName . DIRECTORY_SEPARATOR . 'vendor_quotation_images' . DIRECTORY_SEPARATOR . $componentDirectoryName;
                    foreach ($componentData['vendor_images'] as $image) {
                        $imageName = basename($image);
                        $newTempImageUploadPath = $tempImageUploadPath . '/' . $imageName;
                        $imageData = [
                            'purchase_order_request_component_id' => $purchaseOrderRequestComponent['id'],
                            'name' => $imageName,
                            'caption' => '',
                            'is_vendor_approval' => true
                        ];
                        PurchaseOrderRequestComponentImage::create($imageData);
                        if (!file_exists($imageUploadPath)) {
                            File::makeDirectory($imageUploadPath, $mode = 0777, true, true);
                        }
                        if (File::exists($newTempImageUploadPath)) {
                            $imageUploadNewPath = $imageUploadPath . DIRECTORY_SEPARATOR . $imageName;
                            File::move($newTempImageUploadPath, $imageUploadNewPath);
                        }
                    }
                    if (count(scandir($tempImageUploadPath)) <= 2) {
                        rmdir($tempImageUploadPath);
                    }
                }
            }
            $request->session()->flash('success', "Purchase Order Request Created Successfully.");
            return redirect('/purchase/purchase-order-request/manage');
        }catch(\Exception $e){
            $data = [
                'action' => 'Create purchase order request',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function listing(Request $request){
        try{
            $loggedInUser = Auth::user();
            if(Session::has('global_project_site')){
                $projectSiteId = Session::get('global_project_site');
                $purchaseOrderRequestIds  = PurchaseOrderRequest::join('purchase_requests','purchase_requests.id','=','purchase_order_requests.purchase_request_id')
                ->where('purchase_requests.project_site_id', $projectSiteId)
                ->orderBy('purchase_order_requests.id','desc')
                ->pluck('purchase_order_requests.id');
            }else{
                $purchaseOrderRequestIds = PurchaseOrderRequest::orderBy('id','desc')->pluck('id');
            }

            if($request->has('purchase_request_format')){
                $purchaseOrderRequestIds = PurchaseOrderRequest::join('purchase_requests','purchase_requests.id','=','purchase_order_requests.purchase_request_id')
                                            ->whereIn('purchase_order_requests.id', $purchaseOrderRequestIds)
                                            ->where('purchase_requests.format_id','ilike','%'.trim($request->purchase_request_format).'%')
                                            ->pluck('purchase_order_requests.id');
            }

            if ($request->has('por_status_id')) {
                $draftPurchaseOrderRequestIds = PurchaseOrderRequestComponent::whereIn('purchase_order_request_id',$purchaseOrderRequestIds)
                    ->whereNull('is_approved')
                    ->pluck('purchase_order_request_id')->toArray();
                if ($request->por_status_id == "por_created") {
                    $purchaseOrderRequestsData = PurchaseOrderRequest::where('ready_to_approve', false)
                        ->whereIn('id', $purchaseOrderRequestIds)
                        ->orderBy('id','desc')->get();
                    $status = "Pending for Ready to Approve";
                } elseif ($request->por_status_id == "pending_for_approval") {
                    $purchaseOrderRequestsData = PurchaseOrderRequest::where('ready_to_approve', true)
                        ->whereIn('id',$draftPurchaseOrderRequestIds)
                        ->whereIn('id', $purchaseOrderRequestIds)
                        ->orderBy('id','desc')->get();
                    $status = "Pending for Director Approval";
                } elseif($request->por_status_id == "po_created"){
                    if($draftPurchaseOrderRequestIds > 0){
                        $diffIds = array_diff($purchaseOrderRequestIds->toArray(),$draftPurchaseOrderRequestIds);
                        $purchaseOrderRequestsData = PurchaseOrderRequest::where('ready_to_approve', true)
                            ->whereIn('id',$diffIds)
                            ->orderBy('id','desc')->get();
                        $status = "PO Created";
                    }else{
                        $purchaseOrderRequestsData = array();
                        $status = "";
                    }
                }
            } else {
                $status = "Pending for Ready to Approve";
                $purchaseOrderRequestsData = PurchaseOrderRequest::where('ready_to_approve', false)->whereIn('id', $purchaseOrderRequestIds)->orderBy('id','desc')->get();
            }

            $records = array();
            $records['data'] = array();
            $records["draw"] = intval($request->draw);
            $records["recordsTotal"] = $records["recordsFiltered"] = count($purchaseOrderRequestsData);
            $end = $request->length < 0 ? count($purchaseOrderRequestsData) : $request->length;
            for($iterator = 0,$pagination = $request->start; $iterator < $end && $pagination < count($purchaseOrderRequestsData); $iterator++,$pagination++ ){
                $user = User::where('id',$purchaseOrderRequestsData[$pagination]['user_id'])->select('first_name','last_name')->first();
                $purchaseRequestFormat = $purchaseOrderRequestsData[$pagination]->purchaseRequest->format_id;
                $actionDropdown = '<div class="btn-group">
                            <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                Actions
                                <i class="fa fa-angle-down"></i>
                            </button>';
                if($loggedInUser->roles[0]->role->slug == 'admin' || $loggedInUser->roles[0]->role->slug == 'superadmin' || $loggedInUser->customHasPermission('edit-purchase-order-request')
                    || $loggedInUser->customHasPermission('create-purchase-order-request') || $loggedInUser->customHasPermission('view-purchase-order-request') || $loggedInUser->customHasPermission('approve-purchase-order')){

                    if($purchaseOrderRequestsData[$pagination]['ready_to_approve'] == true){
                        $actionDropdown .='<ul class="dropdown-menu pull-left" role="menu">
                                <li>
                                    <a href="/purchase/purchase-order-request/approve/'.$purchaseOrderRequestsData[$pagination]['id'].'">
                                    <i class="icon-docs"></i> Approve </a>
                                </li>
                            </ul>';
                    }else{
                        $actionDropdown .='<ul class="dropdown-menu pull-left" role="menu">
                                <li>
                                    <a href="/purchase/purchase-order-request/edit/'.$purchaseOrderRequestsData[$pagination]['id'].'">
                                    <i class="icon-docs"></i> Edit </a>
                                </li>
                            </ul>';
                    }
                }
                $actionDropdown .= '</div>';
                $records['data'][] = [
                    $purchaseOrderRequestsData[$pagination]['id'],
                    $purchaseRequestFormat,
                    $status,
                    $user['first_name'].' '.$user['last_name'],
                    $actionDropdown
                ];
            }
            $lastLogin = UserLastLogin::join('modules','modules.id','=','user_last_logins.module_id')
                ->where('modules.slug','purchase-order-request')
                ->where('user_last_logins.user_id',$loggedInUser->id)
                ->pluck('user_last_logins.id')
                ->first();
            if($lastLogin == null){
                UserLastLogin::create([
                    'user_id' => $loggedInUser->id,
                    'module_id' => Module::where('slug','purchase-order-request')->pluck('id')->first(),
                    'last_login' => Carbon::now()
                ]);
            }else{
                UserLastLogin::where('id', $lastLogin)->update(['last_login' => Carbon::now()]);
            }
            $status = 200;
        }catch(\Exception $e){
            $data = [
                'action' => 'Purchase order request Listing',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            $records = array();
            $status = 500;
        }
        return response()->json($records,$status);
    }

    public function getEditView(Request $request,$purchaseOrderRequest){
        try{
            $iterator = 0;
            $purchaseOrderRequestComponentData = array();
            foreach($purchaseOrderRequest->purchaseOrderRequestComponents as $purchaseOrderRequestComponent){
                $purchaseOrderRequestComponentData[$iterator]['id'] = $purchaseOrderRequestComponent->id;
                $purchaseOrderRequestComponentData[$iterator]['vendor_relation_id'] = $purchaseOrderRequestComponent->purchase_request_component_vendor_relation_id;
                $purchaseOrderRequestComponentData[$iterator]['purchase_request_component_id'] = $purchaseOrderRequestComponent->purchaseRequestComponentVendorRelation->purchaseRequestComponent->id;
                $purchaseOrderRequestComponentData[$iterator]['name'] = $purchaseOrderRequestComponent->purchaseRequestComponentVendorRelation->purchaseRequestComponent->materialRequestComponent->name;
                $purchaseOrderRequestComponentData[$iterator]['quantity'] = $purchaseOrderRequestComponent->quantity;
                $purchaseOrderRequestComponentData[$iterator]['unit'] = $purchaseOrderRequestComponent->unit->name;
                $purchaseOrderRequestComponentData[$iterator]['unit_id'] = $purchaseOrderRequestComponent->purchaseRequestComponentVendorRelation->purchaseRequestComponent->materialRequestComponent->unit_id;
                $purchaseOrderRequestComponentData[$iterator]['rate_per_unit'] = $purchaseOrderRequestComponent->rate_per_unit;
                $purchaseOrderRequestComponentData[$iterator]['total'] = $purchaseOrderRequestComponent->total;
                //$purchaseOrderRequestComponentData[$iterator]['rate_with_tax'] = MaterialProductHelper::customRound(($purchaseOrderRequestComponent->total / $purchaseOrderRequestComponent->quantity));
                $purchaseOrderRequestComponentData[$iterator]['rate_with_tax'] = round(($purchaseOrderRequestComponent->total / $purchaseOrderRequestComponent->quantity),3);
                if($purchaseOrderRequestComponent->purchaseRequestComponentVendorRelation['is_client'] == true){
                    $purchaseOrderRequestComponentData[$iterator]['vendor_name'] = $purchaseOrderRequestComponent->purchaseRequestComponentVendorRelation->client->company;
                    $purchaseOrderRequestComponentData[$iterator]['is_client'] = true;
                }else{
                    $purchaseOrderRequestComponentData[$iterator]['vendor_name'] = $purchaseOrderRequestComponent->purchaseRequestComponentVendorRelation->vendor->company;
                    $purchaseOrderRequestComponentData[$iterator]['is_client'] = false;
                }
                $iterator++;
            }
            return view('purchase.purchase-order-request.edit')->with(compact('purchaseOrderRequest', 'purchaseOrderRequestComponentData'));
        }catch (\Exception $e){
            $data = [
                'action' => 'Get Edit Purchase order request',
                'params' => $request->all(),
                'purchase-order-request'=>$purchaseOrderRequest,
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getApproveView(Request $request,$purchaseOrderRequest){
        try{
            $purchaseOrderRequestComponents = array();
            $draftPurchaseOrderRequestComponents = PurchaseOrderRequestComponent::where('purchase_order_request_id',$purchaseOrderRequest->id)->whereNull('is_approved')->get();
            foreach($draftPurchaseOrderRequestComponents as $purchaseOrderRequestComponent){
                $purchaseRequestComponentId = $purchaseOrderRequestComponent->purchaseRequestComponentVendorRelation->purchase_request_component_id;
                if(!array_key_exists($purchaseOrderRequestComponent->purchaseRequestComponentVendorRelation->purchase_request_component_id,$purchaseOrderRequestComponents)){
                    $purchaseOrderRequestComponents[$purchaseRequestComponentId]['name'] = $purchaseOrderRequestComponent->purchaseRequestComponentVendorRelation->purchaseRequestComponent->materialRequestComponent->name;
                    $purchaseOrderRequestComponents[$purchaseRequestComponentId]['quantity'] = $purchaseOrderRequestComponent->quantity;
                    $purchaseOrderRequestComponents[$purchaseRequestComponentId]['unit'] = $purchaseOrderRequestComponent->unit->name;
                    $purchaseOrderRequestComponents[$purchaseRequestComponentId]['purchase_request_component_id'] = $purchaseOrderRequestComponent->purchaseRequestComponentVendorRelation->purchase_request_component_id;
                }
                $rateWithTax = $purchaseOrderRequestComponent->rate_per_unit;
                $rateWithTax += round((($purchaseOrderRequestComponent->rate_per_unit * ($purchaseOrderRequestComponent->cgst_percentage / 100))),3);
                $rateWithTax += round(($purchaseOrderRequestComponent->rate_per_unit * ($purchaseOrderRequestComponent->sgst_percentage / 100)),3);
                $rateWithTax += round(($purchaseOrderRequestComponent->rate_per_unit * ($purchaseOrderRequestComponent->igst_percentage / 100)),3);
                $total_with_tax = round(($purchaseOrderRequestComponent->rate_per_unit * $purchaseOrderRequestComponents[$purchaseRequestComponentId]['quantity'] ),3);
                $total_with_tax += round(($purchaseOrderRequestComponent->rate_per_unit * $purchaseOrderRequestComponents[$purchaseRequestComponentId]['quantity'] *  ($purchaseOrderRequestComponent->cgst_percentage / 100)),3);
                $total_with_tax += round(($purchaseOrderRequestComponent->rate_per_unit * $purchaseOrderRequestComponents[$purchaseRequestComponentId]['quantity'] *  ($purchaseOrderRequestComponent->sgst_percentage / 100)),3);
                $total_with_tax += round(($purchaseOrderRequestComponent->rate_per_unit * $purchaseOrderRequestComponents[$purchaseRequestComponentId]['quantity'] *  ($purchaseOrderRequestComponent->igst_percentage / 100)),3);

                if($purchaseOrderRequestComponent->purchaseRequestComponentVendorRelation->is_client == true){
                    $vendorName = $purchaseOrderRequestComponent->purchaseRequestComponentVendorRelation->client->company;
                    $vendorId = 'client_'.$purchaseOrderRequestComponent->purchaseRequestComponentVendorRelation->client->id;
                }else{
                    $vendorName = $purchaseOrderRequestComponent->purchaseRequestComponentVendorRelation->vendor->company;
                    $vendorId = $purchaseOrderRequestComponent->purchaseRequestComponentVendorRelation->vendor->id;
                }
                $transportationWithTax = $purchaseOrderRequestComponent->transportation_amount;
                $transportationWithTax += round(($purchaseOrderRequestComponent->transportation_amount * ($purchaseOrderRequestComponent->transportation_cgst_percentage / 100)),3);
                $transportationWithTax += round(($purchaseOrderRequestComponent->transportation_amount * ($purchaseOrderRequestComponent->transportation_sgst_percentage / 100)),3);
                $transportationWithTax += round(($purchaseOrderRequestComponent->transportation_amount * ($purchaseOrderRequestComponent->transportation_igst_percentage / 100)),3);
                $purchaseOrderRequestComponents[$purchaseRequestComponentId]['vendor_relations'][] = [
                    'component_vendor_relation_id' => $purchaseOrderRequestComponent->purchase_request_component_vendor_relation_id,
                    'purchase_order_request_component_id' => $purchaseOrderRequestComponent->id,
                    'vendor_name' => $vendorName,
                    'vendor_id' => $vendorId,
                    'rate_without_tax' => $purchaseOrderRequestComponent->rate_per_unit,
                    'rate_with_tax' => $rateWithTax,
                    'total_with_tax' => $total_with_tax,
                    'transportation_without_tax' => $purchaseOrderRequestComponent->transportation_amount,
                    'transportation_with_tax' => $transportationWithTax
                ];
            }
            return view('purchase.purchase-order-request.approve')->with(compact('purchaseOrderRequest','purchaseOrderRequestComponents'));
        }catch(\Exception $e){
            $data = [
                'action' => 'Get Approve Purchase order request',
                'params' => $request->all(),
                'purchase-order-request'=>$purchaseOrderRequest,
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function purchaseRequestAutoSuggest(Request $request,$keyword){
        try{
            if(Session::has('global_project_site')){
                $projectSiteId = Session::get('global_project_site');
                $purchaseOrderCreatedComponentIds = PurchaseOrderRequestComponent::join('purchase_order_requests','purchase_order_requests.id','=','purchase_order_request_components.purchase_order_request_id')
                    ->join('purchase_requests','purchase_requests.id','=','purchase_order_requests.purchase_request_id')
                    ->join('purchase_request_components','purchase_request_components.purchase_request_id','=','purchase_requests.id')
                    ->join('purchase_request_component_vendor_relation','purchase_request_component_vendor_relation.id','=','purchase_order_request_components.purchase_request_component_vendor_relation_id')
                    ->where('purchase_requests.project_site_id',$projectSiteId)
                    ->pluck('purchase_request_component_vendor_relation.id')
                    ->toArray();
                $purchaseRequests = PurchaseRequest::join('purchase_request_components','purchase_request_components.purchase_request_id','=','purchase_requests.id')
                    ->join('purchase_request_component_vendor_relation','purchase_request_component_vendor_relation.purchase_request_component_id','=','purchase_request_components.id')
                    ->whereNotIn('purchase_request_component_vendor_relation.id',$purchaseOrderCreatedComponentIds)
                    ->where('purchase_requests.format_id','ilike','%'.$keyword.'%')
                    ->where('purchase_requests.project_site_id',$projectSiteId)
                    ->select('purchase_requests.id as id','purchase_requests.format_id as format_id')
                    ->distinct('format_id')
                    ->get()
                    ->toArray();
            }else{
                $purchaseOrderCreatedComponentIds = PurchaseOrderRequestComponent::pluck('purchase_request_component_vendor_relation.id')->toArray();
                $purchaseRequests = PurchaseRequest::join('purchase_request_components','purchase_request_components.purchase_request_id','=','purchase_requests.id')
                    ->join('purchase_request_component_vendor_relation','purchase_request_component_vendor_relation.purchase_request_component_id','=','purchase_request_components.id')
                    ->whereNotIn('purchase_request_component_vendor_relation.id',$purchaseOrderCreatedComponentIds)
                    ->where('purchase_requests.format_id','ilike','%'.$keyword.'%')
                    ->select('purchase_requests.id as id','purchase_requests.format_id as format_id')
                    ->distinct('format_id')
                    ->get()
                    ->toArray();
            }
            $purchaseRequestsFinalData = array();
            $pr_itr = 0;
            foreach ($purchaseRequests as $purchaseReq) {
                $purchaseRequestsFinalData[$pr_itr]['id'] = $purchaseReq['id'];
                $purchaseRequestsFinalData[$pr_itr]['format_id'] = $purchaseReq['format_id'];
                $purchaseRequestComponents = PurchaseRequestComponent::where('purchase_request_id', $purchaseReq['id'])->get();
                $iterator = 0;
                $purchaseRequestComponentData = array();
                foreach($purchaseRequestComponents as $purchaseRequestComponent) {
                    foreach ($purchaseRequestComponent->vendorRelations as $vendorRelation) {
                        $vendorRelationAlreadyExists = PurchaseOrderRequestComponent::where('purchase_request_component_vendor_relation_id', $vendorRelation->id)->first();
                        if($vendorRelationAlreadyExists == null) {
                            $purchaseRequestComponentData[] = $purchaseRequestComponent->materialRequestComponent->name;
                        }
                        $iterator++;
                    }
                }
                $purchaseRequestsFinalData[$pr_itr]['material_string'] = substr(implode(",",$purchaseRequestComponentData),0,36)."...";
                $purchaseRequestsFinalData[$pr_itr]['material_count'] = count($purchaseRequestComponentData);
            }
            $status = 200;
        }catch (\Exception $e){
            $data = [
                'action' => 'Purchase Request Auto Suggest',
                'keyword' => $keyword,
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            $status = 500;
            $purchaseRequestsFinalData = array();
        }
        return response()->json($purchaseRequestsFinalData,$status);
    }

    public function getPurchaseRequestComponentDetails(Request $request){
        try{
            $purchaseRequestComponents = PurchaseRequestComponent::where('purchase_request_id', $request->purchase_request_id)->get();
            $iterator = 0;
            $purchaseRequestComponentData = array();
            foreach($purchaseRequestComponents as $purchaseRequestComponent){
                foreach ($purchaseRequestComponent->vendorRelations as $vendorRelation){
                    $vendorRelationAlreadyExists = PurchaseOrderRequestComponent::where('purchase_request_component_vendor_relation_id', $vendorRelation->id)->first();
                    if($vendorRelationAlreadyExists == null){
                        $purchaseRequestComponentData[$iterator]['vendor_relation_id'] = $vendorRelation->id;
                        $purchaseRequestComponentData[$iterator]['purchase_request_component_id'] = $purchaseRequestComponent->id;
                        $purchaseRequestComponentData[$iterator]['name'] = $purchaseRequestComponent->materialRequestComponent->name;
                        $purchaseRequestComponentData[$iterator]['quantity'] = $purchaseRequestComponent->materialRequestComponent->quantity;
                        $purchaseRequestComponentData[$iterator]['unit'] = $purchaseRequestComponent->materialRequestComponent->unit->name;
                        $purchaseRequestComponentData[$iterator]['unit_id'] = $purchaseRequestComponent->materialRequestComponent->unit_id;
                        if($vendorRelation['is_client'] == true){
                            $purchaseRequestComponentData[$iterator]['vendor_name'] = $vendorRelation->client->company;
                            $purchaseRequestComponentData[$iterator]['is_client'] = true;
                            $purchaseRequestComponentData[$iterator]['rate_per_unit'] = '-';
                        }else{
                            $purchaseRequestComponentData[$iterator]['vendor_name'] = $vendorRelation->vendor->company;
                            $purchaseRequestComponentData[$iterator]['is_client'] = false;
                            $lastPurchaseOrderRateInfo = PurchaseOrderComponent::join('purchase_request_components','purchase_request_components.id','=','purchase_order_components.purchase_request_component_id')
                                ->join('material_request_components','material_request_components.id','=','purchase_request_components.material_request_component_id')
                                ->join('purchase_orders','purchase_orders.id','=','purchase_order_components.purchase_order_id')
                                ->where('material_request_components.name','ilike', $purchaseRequestComponentData[$iterator]['name'])
                                ->where('purchase_orders.is_approved', true)
                                ->orderBy('purchase_orders.created_at','desc')
                                ->select('purchase_order_components.rate_per_unit as rate_per_unit','purchase_order_components.unit_id as unit_id')
                                ->first();
                            if($lastPurchaseOrderRateInfo == null){
                                $systemAssetTypeId = MaterialRequestComponentTypes::where('slug','system-asset')->pluck('id')->first();
                                $materialTypeIds = MaterialRequestComponentTypes::whereIn('slug',['quotation-material','structure-material'])->pluck('id')->toArray();
                                if($purchaseRequestComponent->materialRequestComponent->component_type_id == $systemAssetTypeId){
                                    $lastPurchaseOrderRate = Asset::where('name','ilike',$purchaseRequestComponentData[$iterator]['name'])->pluck('price')->first();
                                }elseif(in_array($purchaseRequestComponent->materialRequestComponent->component_type_id,$materialTypeIds)){
                                    $materialInfo = Material::where('name','ilike',$purchaseRequestComponentData[$iterator]['name'])->select('id','rate_per_unit','unit_id')->first();
                                    $lastPurchaseOrderRate = UnitHelper::unitConversion($materialInfo['unit_id'],$purchaseRequestComponentData[$iterator]['unit_id'],$materialInfo['rate_per_unit']);
                                }else{
                                    $lastPurchaseOrderRate = 0;
                                }
                            }else{
                                $lastPurchaseOrderRate = UnitHelper::unitConversion($lastPurchaseOrderRateInfo['unit_id'],$purchaseRequestComponentData[$iterator]['unit_id'],$lastPurchaseOrderRateInfo['rate_per_unit']);
                            }
                            $purchaseRequestComponentData[$iterator]['rate_per_unit'] = $lastPurchaseOrderRate;
                        }
                        $iterator++;
                    }
                }
            }
            return view('partials.purchase.purchase-order-request.component-listing')->with(compact('purchaseRequestComponentData'));
        }catch (\Exception $e){
            $data = [
                'action' => 'Get Purchase Request Component Details',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            return response()->json([], 500);
        }
    }

    public function getComponentTaxDetails(Request $request, $purchaseRequestComponentVendorRelation){
        try{
            $purchaseRequestComponentData = array();
            $purchaseRequestComponentData['is_client'] = $purchaseRequestComponentVendorRelation->is_client;
            $purchaseRequestComponent = $purchaseRequestComponentVendorRelation->purchaseRequestComponent;
            $systemAssetTypeIds = MaterialRequestComponentTypes::whereIn('slug',['system-asset','new-asset'])->pluck('id')->toArray();
            $systemMaterialIds = MaterialRequestComponentTypes::whereIn('slug',['quotation-material','structure-material'])->pluck('id')->toArray();
            if(in_array($purchaseRequestComponent->materialRequestComponent->component_type_id,$systemAssetTypeIds)){
                $purchaseRequestComponentData['categories'] = [
                    [
                        'id' => '',
                        'name' => 'Asset'
                    ]
                ];
                $purchaseRequestComponentData['hsn_code'] = '';
            }elseif(in_array($purchaseRequestComponent->materialRequestComponent->component_type_id,$systemMaterialIds)){
                $purchaseRequestComponentData['categories'] = Material::join('category_material_relations','category_material_relations.material_id','=','materials.id')
                                                                    ->join('categories','category_material_relations.category_id','=','categories.id')
                                                                    ->where('materials.name','ilike',$purchaseRequestComponent->materialRequestComponent->name)
                                                                    ->where('categories.is_active', true)
                                                                    ->select('categories.id as id','categories.name as name')
                                                                    ->get()->toArray();
                $purchaseRequestComponentData['hsn_code'] = Material::where('name','ilike',$purchaseRequestComponent->materialRequestComponent->name)
                                                                    ->pluck('hsn_code')->first();
            }else{
                $purchaseRequestComponentData['categories'] = Category::where('is_miscellaneous', true)->where('is_active', true)->select('id','name')->get()->toArray();
                $purchaseRequestComponentData['hsn_code'] = '';
            }
            $purchaseRequestComponentData['id'] = $purchaseRequestComponent->id;
            $purchaseRequestComponentData['name'] = $purchaseRequestComponent->materialRequestComponent->name;
            $purchaseRequestComponentData['unit'] = $purchaseRequestComponent->materialRequestComponent->unit->name;
            $purchaseRequestComponentData['unit_id'] = $purchaseRequestComponent->materialRequestComponent->unit_id;
            $purchaseRequestComponentData['rate'] = round($request->rate,3);
            $purchaseRequestComponentData['quantity'] = $purchaseRequestComponent->materialRequestComponent->quantity;
            $purchaseRequestComponentData['subtotal'] = round(($purchaseRequestComponentData['rate'] * $purchaseRequestComponentData['quantity']),3);
            $date = date_format(Carbon::now(),'Y-m-d');
            return view('partials.purchase.purchase-order-request.component-tax-details')->with(compact('date','purchaseRequestComponentData'));
        }catch(\Exception $e){
            $data = [
                'action' => 'Get Component Tax Details',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            return response()->json([], 500);
        }
    }

    public function approvePurchaseOrderRequest(Request $request, $purchaseOrderRequest){
        try{
            if($request->has('approved_purchase_order_request_relation')){
                if(Session::has('global_project_site')){
                    $projectSiteId = Session::get('global_project_site');
                }else{
                    $projectSiteId = $purchaseOrderRequest->purchaseRequest->project_site_id;
                }
                $user = Auth::user();
                foreach($request->approved_purchase_order_request_relation as $vendorId => $purchaseOrderRequestComponentArray){
                    $purchaseOrderCount = PurchaseOrder::whereDate('created_at', Carbon::now())->count();
                    $purchaseOrderCount++;
                    $purchaseOrderFormatID = $this->getPurchaseIDFormat('purchase-order',$projectSiteId,Carbon::now(),$purchaseOrderCount);
                    $vendorIdArray = explode('_',$vendorId);
                    if(count($vendorIdArray) == 2){
                        /*Client Supplied*/
                        $vendorId = $vendorIdArray[1];
                        $purchaseOrderData = [
                            'user_id' => Auth::user()->id,
                            'client_id' => $vendorId,
                            'is_approved' => true,
                            'purchase_request_id' => $purchaseOrderRequest->purchase_request_id,
                            'purchase_order_status_id' => PurchaseOrderStatus::where('slug','open')->pluck('id')->first(),
                            'is_client_order' => true,
                            'purchase_order_request_id' => $purchaseOrderRequest->id,
                            'format_id' => $purchaseOrderFormatID,
                            'serial_no' => $purchaseOrderCount,
                            'is_email_sent' => false
                        ];
                    }else{
                        $purchaseOrderData = [
                            'user_id' => Auth::user()->id,
                            'vendor_id' => $vendorId,
                            'is_approved' => true,
                            'purchase_request_id' => $purchaseOrderRequest->purchase_request_id,
                            'purchase_order_status_id' => PurchaseOrderStatus::where('slug','open')->pluck('id')->first(),
                            'is_client_order' => false,
                            'purchase_order_request_id' => $purchaseOrderRequest->id,
                            'format_id' => $purchaseOrderFormatID,
                            'serial_no' => $purchaseOrderCount,
                            'is_email_sent' => false
                        ];
                    }
                    $purchaseOrder = PurchaseOrder::create($purchaseOrderData);

                    $iterator = 0;
                    foreach($purchaseOrderRequestComponentArray as $purchaseOrderRequestComponentId){
                        $purchaseOrderRequestComponent = PurchaseOrderRequestComponent::findOrFail($purchaseOrderRequestComponentId);
                        $purchaseOrderComponentData = PurchaseOrderRequestComponent::where('id', $purchaseOrderRequestComponentId)
                                                                ->select('id as purchase_order_request_component_id','rate_per_unit','gst','hsn_code','expected_delivery_date','remark','credited_days',
                                                                    'quantity','unit_id','cgst_percentage','sgst_percentage','igst_percentage','cgst_amount',
                                                                    'sgst_amount','igst_amount','total')
                                                                ->first()->toArray();
                        $purchaseOrderComponentData['purchase_order_id'] = $purchaseOrder->id;
                        $purchaseOrderComponentData['purchase_request_component_id'] = $purchaseOrderRequestComponent->purchaseRequestComponentVendorRelation->purchase_request_component_id;
                        if($purchaseOrderComponentData['rate_per_unit'] == null || $purchaseOrderComponentData['rate_per_unit'] == ''){
                            $purchaseOrderComponentData['rate_per_unit'] = 0;
                        }
                        $purchaseOrderComponent = PurchaseOrderComponent::create($purchaseOrderComponentData);
                        $newAssetTypeId = MaterialRequestComponentTypes::where('slug','new-asset')->pluck('id')->first();
                        $newMaterialTypeId = MaterialRequestComponentTypes::where('slug','new-material')->pluck('id')->first();
                        $componentTypeId = $purchaseOrderComponent->purchaseRequestComponent->materialRequestComponent->component_type_id;
                        if($newMaterialTypeId == $componentTypeId){
                            $materialName = $purchaseOrderComponent->purchaseRequestComponent->materialRequestComponent->name;
                            $isMaterialExists = Material::where('name','ilike',$materialName)->first();
                            if($isMaterialExists == null){
                                $materialData = [
                                    'name' => $purchaseOrderComponent->purchaseRequestComponent->materialRequestComponent->name,
                                    'is_active' => true,
                                    'rate_per_unit' => $purchaseOrderComponent->rate_per_unit,
                                    'unit_id' => $purchaseOrderComponent->unit_id,
                                    'hsn_code' => $purchaseOrderComponent->hsn_code,
                                    'gst' => $purchaseOrderComponent->gst
                                ];
                                $material = Material::create($materialData);
                                $categoryMaterialData = [
                                    'material_id' => $material->id,
                                    'category_id' => $purchaseOrderRequestComponent->category_id
                                ];
                                CategoryMaterialRelation::create($categoryMaterialData);
                                $materialVersionData = [
                                    'material_id' => $material->id,
                                    'rate_per_unit' => $material->rate_per_unit,
                                    'unit_id' => $material->unit_id
                                ];
                                MaterialVersion::create($materialVersionData);
                            }
                        }elseif ($newAssetTypeId == $componentTypeId){
                            $assetName = $purchaseOrderComponent->purchaseRequestComponent->materialRequestComponent->name;
                            $is_present = Asset::where('name','ilike',$assetName)->pluck('id')->toArray();
                            if($is_present == null){
                                $asset_type = AssetType::where('slug','other')->pluck('id')->first();
                                $categoryAssetData = array();
                                $categoryAssetData['asset_types_id'] = $asset_type;
                                $categoryAssetData['name'] = $assetName;
                                $categoryAssetData['quantity'] = 1;
                                $categoryAssetData['is_fuel_dependent'] = false;
                                Asset::create($categoryAssetData);
                            }
                        }
                        $purchaseOrderRequestComponent->update(['is_approved' => true]);
                        $purchaseRequestComponentId = $purchaseOrderRequestComponent->purchaseRequestComponentVendorRelation->purchase_request_component_id;
                        $disapprovedPurchaseOrderRequestComponentIds = PurchaseOrderRequestComponent::join('purchase_request_component_vendor_relation','purchase_request_component_vendor_relation.id','=','purchase_order_request_components.purchase_request_component_vendor_relation_id')
                                ->where('purchase_request_component_vendor_relation.purchase_request_component_id',$purchaseRequestComponentId)
                                ->where('purchase_order_request_components.id','!=',$purchaseOrderRequestComponent->id)
                                ->where('purchase_order_request_components.purchase_order_request_id', $purchaseOrderRequestComponent->purchase_order_request_id)
                                ->pluck('purchase_order_request_components.id')
                                ->toArray();
                        if(count($disapprovedPurchaseOrderRequestComponentIds) > 0){
                            PurchaseOrderRequestComponent::whereIn('id', $disapprovedPurchaseOrderRequestComponentIds)->update(['is_approved' => false]);
                        }
                        if(count($purchaseOrderRequestComponent->purchaseOrderRequestComponentImages) > 0){
                            $purchaseOrderMainDirectoryName = sha1($purchaseOrderComponent['purchase_order_id']);
                            $purchaseOrderComponentDirectoryName = sha1($purchaseOrderComponent['id']);
                            $mainDirectoryName = sha1($purchaseOrderRequest->id);
                            $componentDirectoryName = sha1($purchaseOrderRequestComponent->id);
                            foreach ($purchaseOrderRequestComponent->purchaseOrderRequestComponentImages as $purchaseOrderRequestComponentImage){
                                if($purchaseOrderRequestComponentImage->is_vendor_approval == true){
                                    $toUploadPath = public_path().env('PURCHASE_ORDER_IMAGE_UPLOAD').DIRECTORY_SEPARATOR.$purchaseOrderMainDirectoryName.DIRECTORY_SEPARATOR.'vendor_quotation_images'.DIRECTORY_SEPARATOR.$purchaseOrderComponentDirectoryName;
                                    $fromUploadPath = public_path().env('PURCHASE_ORDER_REQUEST_IMAGE_UPLOAD').DIRECTORY_SEPARATOR.$mainDirectoryName.DIRECTORY_SEPARATOR.'vendor_quotation_images'.DIRECTORY_SEPARATOR.$componentDirectoryName;
                                }else{
                                    $toUploadPath = public_path().env('PURCHASE_ORDER_IMAGE_UPLOAD').DIRECTORY_SEPARATOR.$purchaseOrderMainDirectoryName.DIRECTORY_SEPARATOR.'client_approval_images'.DIRECTORY_SEPARATOR.$purchaseOrderComponentDirectoryName;
                                    $fromUploadPath = public_path().env('PURCHASE_ORDER_REQUEST_IMAGE_UPLOAD').DIRECTORY_SEPARATOR.$mainDirectoryName.DIRECTORY_SEPARATOR.'client_approval_images'.DIRECTORY_SEPARATOR.$componentDirectoryName;
                                }
                                if (!file_exists($toUploadPath)) {
                                    File::makeDirectory($toUploadPath, $mode = 0777, true, true);
                                }
                                $fromUploadPath = $fromUploadPath.DIRECTORY_SEPARATOR.$purchaseOrderRequestComponentImage->name;
                                $toUploadPath = $toUploadPath.DIRECTORY_SEPARATOR.$purchaseOrderRequestComponentImage->name;
                                if(file_exists($fromUploadPath)){
                                    $imageData = [
                                        'purchase_order_component_id' => $purchaseOrderComponent['id'] ,
                                        'name' => $purchaseOrderRequestComponentImage->name,
                                        'caption' => $purchaseOrderRequestComponentImage->caption,
                                        'is_vendor_approval' => $purchaseOrderRequestComponentImage->is_vendor_approval
                                    ];
                                    File::move($fromUploadPath, $toUploadPath);
                                    PurchaseOrderComponentImage::create($imageData);
                                }
                            }
                        }
                        $iterator++;
                    }
                    $webTokens = [$purchaseOrder->purchaseRequest->onBehalfOfUser->web_fcm_token];
                    $mobileTokens = [$purchaseOrder->purchaseRequest->onBehalfOfUser->mobile_fcm_token];
                    $purchaseRequestComponentIds = array_column(($purchaseOrder->purchaseOrderComponent->toArray()),'purchase_request_component_id');
                    $materialRequestUserToken = User::join('material_requests','material_requests.on_behalf_of','=','users.id')
                        ->join('material_request_components','material_request_components.material_request_id','=','material_requests.id')
                        ->join('purchase_request_components','purchase_request_components.material_request_component_id','=','material_request_components.id')
                        ->join('purchase_order_components','purchase_order_components.purchase_request_component_id','=','purchase_request_components.id')
                        ->join('purchase_orders','purchase_orders.id','=','purchase_order_components.purchase_order_id')
                        ->where('purchase_orders.id', $purchaseOrder->id)
                        ->whereIn('purchase_request_components.id', $purchaseRequestComponentIds)
                        ->select('users.web_fcm_token as web_fcm_function','users.mobile_fcm_token as mobile_fcm_function')
                        ->get()->toArray();

                    $materialRequestComponentIds = PurchaseRequestComponent::whereIn('id',$purchaseRequestComponentIds)->pluck('material_request_component_id')->toArray();
                    $purchaseRequestApproveStatusesId = PurchaseRequestComponentStatuses::whereIn('slug',['p-r-manager-approved','p-r-admin-approved'])->pluck('id');
                    $purchaseRequestApproveUserToken = User::join('material_request_component_history_table','material_request_component_history_table.user_id','=','users.id')
                        ->whereIn('material_request_component_history_table.material_request_component_id',$materialRequestComponentIds)
                        ->whereIn('material_request_component_history_table.component_status_id',$purchaseRequestApproveStatusesId)
                        ->select('users.web_fcm_token as web_fcm_function','users.mobile_fcm_token as mobile_fcm_function')
                        ->get()->toArray();
                    $materialRequestUserToken = array_merge($materialRequestUserToken,$purchaseRequestApproveUserToken);
                    $webTokens = array_merge($webTokens, array_column($materialRequestUserToken,'web_fcm_function'));
                    $mobileTokens = array_merge($mobileTokens, array_column($materialRequestUserToken,'mobile_fcm_function'));
                    $notificationString = '3 -'.$purchaseOrder->purchaseRequest->projectSite->project->name.' '.$purchaseOrder->purchaseRequest->projectSite->name;
                    $notificationString .= ' '.$user['first_name'].' '.$user['last_name'].'Purchase Order Created.';
                    $notificationString .= 'PO number: '.$purchaseOrder->format_id;
                    $this->sendPushNotification('Manisha Construction',$notificationString,array_unique($webTokens),array_unique($mobileTokens),'c-p-o');
                }
                $request->session()->flash('success','Purchase Orders Created Successfully !');
                return redirect('/purchase/purchase-order/manage');
            }else{
                $request->session()->flash('error','Please select at least one material/asset.');
                return redirect('/purchase/purchase-order-request/approve/'.$purchaseOrderRequest->id);
            }
        }catch(\Exception $e){
            $data = [
                'action' => 'Approve Purchase Order Request',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function uploadTempFiles(Request $request,$purchaseRequestComponentId){
        try{
            $user = Auth::user();
            $userDirectoryName = sha1($user['id']);
            $tempUploadPath = public_path().env('PURCHASE_ORDER_REQUEST_TEMP_IMAGE_UPLOAD');
            $tempImageUploadPath = $tempUploadPath.DIRECTORY_SEPARATOR.$userDirectoryName;
            /* Create Upload Directory If Not Exists */
            if (!file_exists($tempImageUploadPath)) {
                File::makeDirectory($tempImageUploadPath, $mode = 0777, true, true);
            }
            $extension = $request->file('file')->getClientOriginalExtension();
            $filename = mt_rand(1,10000000000).sha1(time()).".{$extension}";
            $request->file('file')->move($tempImageUploadPath,$filename);
            $path = env('PURCHASE_ORDER_REQUEST_TEMP_IMAGE_UPLOAD').DIRECTORY_SEPARATOR.$userDirectoryName.DIRECTORY_SEPARATOR.$filename;
            $response = [
                'jsonrpc' => '2.0',
                'result' => 'OK',
                'path' => $path
            ];
        }catch (\Exception $e){
            $response = [
                'jsonrpc' => '2.0',
                'error' => [
                    'code' => 101,
                    'message' => 'Failed to open input stream.',
                ],
                'id' => 'id'
            ];
        }
        return response()->json($response);
    }

    public function displayFiles(Request $request,$forSlug){
        try{
            $fullPath = env('APP_URL').$request->path;
            $path = $request->path;
            $extension = pathinfo($request->path, PATHINFO_EXTENSION);
            if($extension == 'pdf'){
                $isPDF = true;
            }else{
                $isPDF = false;
            }
            $count = $request->count;
            $random = mt_rand(1,10000000000);
        }catch (\Exception $e){
            $path = null;
            $count = null;
        }
        return view('partials.purchase.purchase-order-request.display-file')->with(compact('path','count','random','fullPath','isPDF','forSlug'));
    }

    public function removeTempImage(Request $request){
        try{
            $sellerUploadPath = public_path().$request->path;
            File::delete($sellerUploadPath);
            return response(200);
        }catch(\Exception $e){
            return response(500);
        }
    }

    public function getPurchaseOrderRequestComponentTaxDetails(Request $request, $purchaseOrderRequestComponent){
        try{
            $purchaseOrderRequestComponentData = array();
            $purchaseOrderRequestComponentData['is_client'] = $purchaseOrderRequestComponent->purchaseRequestComponentVendorRelation->is_client;
            $purchaseRequestComponent = $purchaseOrderRequestComponent->purchaseRequestComponentVendorRelation->purchaseRequestComponent;
            $systemAssetTypeIds = MaterialRequestComponentTypes::whereIn('slug',['system-asset','new-asset'])->pluck('id')->toArray();
            $systemMaterialIds = MaterialRequestComponentTypes::whereIn('slug',['quotation-material','structure-material'])->pluck('id')->toArray();
            if(in_array($purchaseRequestComponent->materialRequestComponent->component_type_id,$systemAssetTypeIds)){
                $purchaseOrderRequestComponentData['categories'] = [
                    [
                        'id' => '',
                        'name' => 'Asset'
                    ]
                ];
            }elseif(in_array($purchaseRequestComponent->materialRequestComponent->component_type_id,$systemMaterialIds)){
                $purchaseOrderRequestComponentData['categories'] = Material::join('category_material_relations','category_material_relations.material_id','=','materials.id')
                    ->join('categories','category_material_relations.category_id','=','categories.id')
                    ->where('materials.name','ilike',$purchaseRequestComponent->materialRequestComponent->name)
                    ->where('categories.is_active', true)
                    ->select('categories.id as id','categories.name as name')
                    ->get()->toArray();
            }else{
                $purchaseOrderRequestComponentData['categories'] = Category::where('is_miscellaneous', true)->where('is_active', true)->select('id','name')->get()->toArray();
            }
            $purchaseOrderRequestComponentData['hsn_code'] = $purchaseOrderRequestComponent['hsn_code'];
            $purchaseOrderRequestComponentData['id'] = $purchaseOrderRequestComponent->id;
            $purchaseOrderRequestComponentData['name'] = $purchaseOrderRequestComponent->purchaseRequestComponentVendorRelation->purchaseRequestComponent->materialRequestComponent->name;
            $purchaseOrderRequestComponentData['unit'] = $purchaseOrderRequestComponent->unit->name;
            $purchaseOrderRequestComponentData['unit_id'] = $purchaseOrderRequestComponent->unit_id;
            $purchaseOrderRequestComponentData['rate'] = $request->rate;
            $purchaseOrderRequestComponentData['quantity'] = $purchaseOrderRequestComponent->quantity;
            $purchaseOrderRequestComponentData['subtotal'] = round(($purchaseOrderRequestComponentData['rate'] * $purchaseOrderRequestComponentData['quantity']),3);
            /*$purchaseOrderRequestComponentData['transportation_cgst_amount'] = MaterialProductHelper::customRound(( $purchaseOrderRequestComponent['transportation_amount'] * ($purchaseOrderRequestComponent['transportation_cgst_percentage'] / 100)));
            $purchaseOrderRequestComponentData['transportation_sgst_amount'] = MaterialProductHelper::customRound(( $purchaseOrderRequestComponent['transportation_amount'] * ($purchaseOrderRequestComponent['transportation_sgst_percentage'] / 100)));
            $purchaseOrderRequestComponentData['transportation_igst_amount'] = MaterialProductHelper::customRound(( $purchaseOrderRequestComponent['transportation_amount'] * ($purchaseOrderRequestComponent['transportation_igst_percentage'] / 100)));
            $purchaseOrderRequestComponentData['transportation_total'] = MaterialProductHelper::customRound(($purchaseOrderRequestComponentData['transportation_cgst_amount'] + $purchaseOrderRequestComponentData['transportation_sgst_amount'] + $purchaseOrderRequestComponentData['transportation_igst_amount'] + $purchaseOrderRequestComponent['transportation_amount']));*/
            $purchaseOrderRequestComponentData['transportation_cgst_amount'] = round(( $purchaseOrderRequestComponent['transportation_amount'] * ($purchaseOrderRequestComponent['transportation_cgst_percentage'] / 100)),3);
            $purchaseOrderRequestComponentData['transportation_sgst_amount'] = round(( $purchaseOrderRequestComponent['transportation_amount'] * ($purchaseOrderRequestComponent['transportation_sgst_percentage'] / 100)),3);
            $purchaseOrderRequestComponentData['transportation_igst_amount'] = round(( $purchaseOrderRequestComponent['transportation_amount'] * ($purchaseOrderRequestComponent['transportation_igst_percentage'] / 100)),3);
            $purchaseOrderRequestComponentData['transportation_total'] = round(($purchaseOrderRequestComponentData['transportation_cgst_amount'] + $purchaseOrderRequestComponentData['transportation_sgst_amount'] + $purchaseOrderRequestComponentData['transportation_igst_amount'] + $purchaseOrderRequestComponent['transportation_amount']),3);
            $date = date_format(Carbon::now(),'Y-m-d');
            $purchaseOrderRequestComponentData['vendor_quotation'] = array();
            $purchaseOrderRequestComponentData['client_approval'] = array();
            $mainDirectoryName = sha1($purchaseOrderRequestComponent->purchase_order_request_id);
            $mainDirectoryPath = env('PURCHASE_ORDER_REQUEST_IMAGE_UPLOAD') . DIRECTORY_SEPARATOR . $mainDirectoryName;
            foreach($purchaseOrderRequestComponent->purchaseOrderRequestComponentImages as $purchaseOrderRequestComponentImage){
                $componentDirectoryName = sha1($purchaseOrderRequestComponent->id);
                $ext = pathinfo($purchaseOrderRequestComponent->name, PATHINFO_EXTENSION);
                if($ext == 'pdf' || $ext == 'PDF'){
                    $isPdf = true;
                }else{
                    $isPdf = false;
                }
                if($purchaseOrderRequestComponentImage->is_vendor_approval == true){
                    $path = $mainDirectoryPath.DIRECTORY_SEPARATOR.'vendor_quotation_images'.DIRECTORY_SEPARATOR.$componentDirectoryName.DIRECTORY_SEPARATOR.$purchaseOrderRequestComponentImage->name;
                    $fullPath = env('APP_URL').DIRECTORY_SEPARATOR.$path;
                    $random = $purchaseOrderRequestComponentImage->id;
                    if(file_exists(public_path().$path)){
                        $purchaseOrderRequestComponentData['vendor_quotation'][] = [
                            'random' => $random,
                            'path' => $path,
                            'fullPath' => $fullPath,
                            'isPdf' => $isPdf
                        ];
                    }
                }else{
                    $path = $mainDirectoryPath.DIRECTORY_SEPARATOR.'client_approval_images'.DIRECTORY_SEPARATOR.$componentDirectoryName.DIRECTORY_SEPARATOR.$purchaseOrderRequestComponentImage->name;
                    $fullPath = env('APP_URL').DIRECTORY_SEPARATOR.$path;
                    $random = $purchaseOrderRequestComponentImage->id;
                    if(file_exists(public_path().$path)){
                        $purchaseOrderRequestComponentData['client_approval'][] = [
                            'random' => $random,
                            'path' => $path,
                            'fullPath' => $fullPath,
                            'isPdf' => $isPdf
                        ];
                    }
                }
            }
            if($request->has('from_approval') && $request->from_approval == true){
                return view('partials.purchase.purchase-order-request.component-tax-details-approval')->with(compact('date','purchaseOrderRequestComponentData','purchaseOrderRequestComponent'));
            }else{
                return view('partials.purchase.purchase-order-request.purchase-order-request-component-tax-details')->with(compact('date','purchaseOrderRequestComponentData','purchaseOrderRequestComponent'));
            }
        }catch (\Exception $e){
            $data = [
                'action' => 'Get Purchase Order Request Component tax details',
                'purchase_order_request_component' => $purchaseOrderRequestComponent,
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            $status = 500;
            $response = [
                'message' => 'Something went wrong'
            ];
            return response()->json($response, $status);
        }
    }

    public function editPurchaseOrderRequest(Request $request, $purchaseOrderRequest){
        try{
            $user = Auth::user();
            if($request->has('data')){
                foreach($request['data'] as $purchaseOrderRequestComponentId => $componentData){
                    $purchaseOrderRequestComponent = PurchaseOrderRequestComponent::findOrFail($purchaseOrderRequestComponentId);
                    if($componentData['rate_per_unit'] == '-'){
                        $componentData['rate_per_unit'] = 0;
                    }
                    $date = explode('/',$componentData['expected_delivery_date']);
                    $expectedDeliveryDate  = $date[2].'-'.$date[1].'-'.$date[0];
                    $purchaseOrderRequestComponentData = [
                        'rate_per_unit' => $componentData['rate_per_unit'],
                        'quantity' => $componentData['quantity'],
                        'unit_id' => $componentData['unit_id'],
                        'hsn_code' => $componentData['hsn_code'],
                        'expected_delivery_date' => date('Y-m-d H:i:s',strtotime($expectedDeliveryDate)),
                        'cgst_percentage' => $componentData['cgst_percentage'],
                        'sgst_percentage' => $componentData['sgst_percentage'],
                        'igst_percentage' => $componentData['igst_percentage'],
                        'cgst_amount' => $componentData['cgst_amount'],
                        'sgst_amount' => $componentData['sgst_amount'],
                        'igst_amount' => $componentData['igst_amount'],
                        'total' => $componentData['total'],
                        'category_id' => $componentData['category_id'],
                        'transportation_amount' => $componentData['transportation_amount'],
                        'transportation_cgst_percentage' => $componentData['transportation_cgst_percentage'],
                        'transportation_sgst_percentage' => $componentData['transportation_sgst_percentage'],
                        'transportation_igst_percentage' => $componentData['transportation_igst_percentage']
                    ];
                    $purchaseOrderRequestComponent->update($purchaseOrderRequestComponentData);
                    if(array_key_exists('client_images',$componentData)) {
                        $mainDirectoryName = sha1($purchaseOrderRequest->id);
                        $componentDirectoryName = sha1($purchaseOrderRequestComponent->id);
                        $userDirectoryName = sha1($user['id']);
                        $tempImageUploadPath = public_path() . env('PURCHASE_ORDER_REQUEST_TEMP_IMAGE_UPLOAD') . DIRECTORY_SEPARATOR . $userDirectoryName;
                        $imageUploadPath = public_path() . env('PURCHASE_ORDER_REQUEST_IMAGE_UPLOAD') . DIRECTORY_SEPARATOR . $mainDirectoryName . DIRECTORY_SEPARATOR . 'client_approval_images' . DIRECTORY_SEPARATOR . $componentDirectoryName;
                        foreach ($componentData['client_images'] as $image) {

                            $imageName = basename($image);
                            $newTempImageUploadPath = $tempImageUploadPath . '/' . $imageName;
                            $imageData = [
                                'purchase_order_request_component_id' => $purchaseOrderRequestComponent['id'],
                                'name' => $imageName,
                                'caption' => '',
                                'is_vendor_approval' => false
                            ];
                            PurchaseOrderRequestComponentImage::create($imageData);
                            if (!file_exists($imageUploadPath)) {
                                File::makeDirectory($imageUploadPath, $mode = 0777, true, true);
                            }
                            if (File::exists($newTempImageUploadPath)) {
                                $imageUploadNewPath = $imageUploadPath . DIRECTORY_SEPARATOR . $imageName;
                                File::move($newTempImageUploadPath, $imageUploadNewPath);
                            }
                        }
                        if (count(scandir($tempImageUploadPath)) <= 2) {
                            rmdir($tempImageUploadPath);
                        }
                    }
                    if(array_key_exists('vendor_images',$componentData)) {
                        $mainDirectoryName = sha1($purchaseOrderRequest->id);
                        $componentDirectoryName = sha1($purchaseOrderRequestComponent->id);
                        $userDirectoryName = sha1($user['id']);
                        $tempImageUploadPath = public_path() . env('PURCHASE_ORDER_REQUEST_TEMP_IMAGE_UPLOAD') . DIRECTORY_SEPARATOR . $userDirectoryName;
                        $imageUploadPath = public_path() . env('PURCHASE_ORDER_REQUEST_IMAGE_UPLOAD') . DIRECTORY_SEPARATOR . $mainDirectoryName . DIRECTORY_SEPARATOR . 'vendor_quotation_images' . DIRECTORY_SEPARATOR . $componentDirectoryName;
                        foreach ($componentData['vendor_images'] as $image) {
                            $imageName = basename($image);
                            $newTempImageUploadPath = $tempImageUploadPath . '/' . $imageName;
                            $imageData = [
                                'purchase_order_request_component_id' => $purchaseOrderRequestComponent['id'],
                                'name' => $imageName,
                                'caption' => '',
                                'is_vendor_approval' => true
                            ];
                            PurchaseOrderRequestComponentImage::create($imageData);
                            if (!file_exists($imageUploadPath)) {
                                File::makeDirectory($imageUploadPath, $mode = 0777, true, true);
                            }
                            if (File::exists($newTempImageUploadPath)) {
                                $imageUploadNewPath = $imageUploadPath . DIRECTORY_SEPARATOR . $imageName;
                                File::move($newTempImageUploadPath, $imageUploadNewPath);
                            }
                        }
                        if (count(scandir($tempImageUploadPath)) <= 2) {
                            rmdir($tempImageUploadPath);
                        }
                    }
                }
            }
            $request->session()->flash('success',"Purchase Order Request edited successfully !!");
            return redirect('/purchase/purchase-order-request/edit/'.$purchaseOrderRequest->id);
        }catch (\Exception $e){
            $data = [
                'action' => 'Edit Purchase Order Request',
                'purchase_order_request' => $purchaseOrderRequest,
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function makeReadyToApprove(Request $request, $purchaseOrderRequest){
        try{
            $purchaseOrderRequest->update(['ready_to_approve' => true]);
            $request->session()->flash('success','Purchase Order Request is ready to approve !!');
            return redirect('/purchase/purchase-order-request/approve/'.$purchaseOrderRequest->id);
        }catch (\Exception $e){
            $data = [
                'action' => 'Make Ready To approve Purchase Order Request',
                'purchase_order_request' => $purchaseOrderRequest,
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function disapproveComponent(Request $request, $purchaseOrderRequest, $purchaseRequestComponent){
        try{
            $user = Auth::user();
            $purchaseOrderRequestComponentId = PurchaseOrderRequestComponent::join('purchase_request_component_vendor_relation','purchase_request_component_vendor_relation.id','=','purchase_order_request_components.purchase_request_component_vendor_relation_id')
                                                ->where('purchase_order_request_components.purchase_order_request_id', $purchaseOrderRequest->id)
                                                ->where('purchase_request_component_vendor_relation.purchase_request_component_id', $purchaseRequestComponent->id)
                                                ->pluck('purchase_order_request_components.id');
            PurchaseOrderRequestComponent::whereIn('id', $purchaseOrderRequestComponentId)->update(['is_approved' => false,'approve_disapprove_by_user' => $user['id']]);
            $request->session()->flash('success', "Material / Asset removed successfully !!");
            return redirect('/purchase/purchase-order-request/approve/'.$purchaseOrderRequest->id);
        }catch(\Exception $e){
            $data = [
                'action' => 'Disapprove Purchase Order Request Component',
                'params' => $request->all(),
                'purchase_order' => $purchaseOrderRequest,
                'purchase_request_component' => $purchaseRequestComponent,
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }
}
