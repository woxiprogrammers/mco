<?php

namespace App\Http\Controllers\Purchase;

use App\Client;
use App\Helper\UnitHelper;
use App\Http\Controllers\CustomTraits\Notification\NotificationTrait;
use App\Http\Controllers\CustomTraits\Purchase\MaterialRequestTrait;
use App\InventoryComponent;
use App\InventoryComponentTransfers;
use App\InventoryComponentTransferStatus;
use App\Material;
use App\MaterialRequestComponentHistory;
use App\MaterialRequestComponents;
use App\MaterialRequestComponentTypes;
use App\MaterialRequestComponentVersion;
use App\MaterialRequests;
use App\Project;
use App\ProjectSite;
use App\PurchaseOrder;
use App\PurchaseOrderRequest;
use App\PurchaseOrderRequestComponent;
use App\PurchaseRequest;
use App\PurchaseRequestComponent;
use App\PurchaseRequestComponentStatuses;
use App\PurchaseRequestComponentVendorMailInfo;
use App\PurchaseRequestComponentVendorRelation;
use App\Quotation;
use App\QuotationMaterial;
use App\QuotationStatus;
use App\Role;
use App\Unit;
use App\User;
use App\UserHasPermission;
use App\UserHasRole;
use App\Vendor;
use App\VendorMaterialRelation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class PurchaseRequestController extends Controller
{
    use MaterialRequestTrait;
    public function __construct()
    {
        $this->middleware('custom.auth');
    }
    public function getManageView(Request $request){
        $approvedQuotationStatus = QuotationStatus::where('slug','approved')->first();
        $projectSiteIds = Quotation::where('quotation_status_id',$approvedQuotationStatus['id'])->pluck('project_site_id')->toArray();
        $projectIds = ProjectSite::whereIn('id',$projectSiteIds)->pluck('project_id')->toArray();
        $clientIds = Project::whereIn('id',$projectIds)->pluck('client_id')->toArray();
        $clients = Client::whereIn('id',$clientIds)->where('is_active',true)->orderBy('id','asc')->get()->toArray();
        $purchaseStatus = PurchaseRequestComponentStatuses::whereIn('slug',['purchase-requested','p-r-manager-approved','p-r-manager-disapproved','p-r-admin-approved','p-r-admin-disapproved'])->get()->toArray();
        return view('purchase/purchase-request/manage')->with(compact('clients','purchaseStatus'));
    }

    public function getCreateView(Request $request){
        try{

            $user = Auth::user();
            $userData = array(
                "id" => $user['id'],
                "username" => $user['first_name']." ".$user['last_name']
            );
            if(Session::has('global_project_site')){
                $projectSiteId = Session::get('global_project_site');
            }else{
                $projectSiteId = null;
            }
            $nosUnitId = Unit::where('slug','nos')->pluck('id')->first();
            $units = Unit::select('id','name')->get()->toArray();
            $inIndentStatusId = PurchaseRequestComponentStatuses::where('slug','in-indent')->pluck('id')->first();
            $iterator = 0;
            $materialRequestList = array();
            $materialRequestIds = MaterialRequests::where('project_site_id',$projectSiteId)->pluck('id');
            if(count($materialRequestIds) > 0){
                $materialRequestIds = $materialRequestIds->toArray();
                $materialRequestComponents = MaterialRequestComponents::whereIn('material_request_id',$materialRequestIds)->where('component_status_id',$inIndentStatusId)->get();
                foreach($materialRequestComponents as $index => $materialRequestComponent){
                    $materialRequestList[$iterator]['material_request_component_id'] = $materialRequestComponent->id;
                    $materialRequestList[$iterator]['name'] = $materialRequestComponent->name;
                    $materialRequestList[$iterator]['quantity'] = $materialRequestComponent->quantity;
                    $materialRequestList[$iterator]['unit_id'] = $materialRequestComponent->unit_id;
                    $materialRequestList[$iterator]['unit'] = $materialRequestComponent->unit->name;
                    $materialRequestList[$iterator]['component_type_id'] = $materialRequestComponent->component_type_id;
                    $materialRequestList[$iterator]['component_type'] = $materialRequestComponent->materialRequestComponentTypes->name;
                    $materialRequestList[$iterator]['component_status_id'] = $materialRequestComponent->component_status_id;
                    $materialRequestList[$iterator]['component_status'] = $materialRequestComponent->purchaseRequestComponentStatuses->name;
                    $iterator++;
                }
            }
            $unitOptions = '';
            foreach($units as $unit){
                $unitOptions .= '<option value="'.$unit['id'].'">'.$unit['name'].'</option>';
            }
            return view('purchase/purchase-request/create')->with(compact('materialRequestList','nosUnitId','units','unitOptions','userData'));
        }catch(\Exception $e){
            $data = [
                'action' => 'Get Purchase Request create view',
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getEditView(Request $request,$status,$id){
        try{
            $user = Auth::user();
            $userRole = $user->roles[0]->role->slug;
            if($status == "p-r-admin-approved"){
                $purchaseRequest = PurchaseRequest::where('id',$id)->first();
                $materialRequestComponentIds = PurchaseRequestComponent::where('purchase_request_id',$id)->pluck('material_request_component_id');
                $materialRequestComponentDetails = MaterialRequestComponents::whereIn('id',$materialRequestComponentIds)->orderBy('id','asc')->get();
                $materialRequestComponentID = MaterialRequestComponentTypes::where('slug','quotation-material')->pluck('id')->first();
                $allVendors = Vendor::where('is_active','true')->orderBy('company','asc')->select('id','company')->get()->toArray();
                $iterator = 0;
                $assignedVendorData = array();
                foreach($materialRequestComponentDetails as $key => $materialRequestComponent){
                    $assignedVendorData[$materialRequestComponent->id] = PurchaseRequestComponentVendorRelation::join('purchase_request_components','purchase_request_components.id','=','purchase_request_component_vendor_relation.purchase_request_component_id')
                                                                            ->join('material_request_components','material_request_components.id','=','purchase_request_components.material_request_component_id')
                                                                            ->where('material_request_components.id',$materialRequestComponent->id)
                                                                            ->pluck('purchase_request_component_vendor_relation.vendor_id')->toArray();
                    $assignedClientData[$materialRequestComponent->id] = PurchaseRequestComponentVendorRelation::join('purchase_request_components','purchase_request_components.id','=','purchase_request_component_vendor_relation.purchase_request_component_id')
                                                                            ->join('material_request_components','material_request_components.id','=','purchase_request_components.material_request_component_id')
                                                                            ->where('material_request_components.id',$materialRequestComponent->id)
                                                                            ->whereNotNull('purchase_request_component_vendor_relation.client_id')
                                                                            ->pluck('purchase_request_component_vendor_relation.client_id')->toArray();
                    if($materialRequestComponentID == $materialRequestComponent->component_type_id){
                        $material_id = Material::where('name','ilike',$materialRequestComponent->name)->pluck('id')->first();
                        $vendorAssignedIds = VendorMaterialRelation::where('material_id',$material_id)->pluck('vendor_id');
                        if(count($vendorAssignedIds) > 0){
                            $materialRequestComponentDetails[$iterator]['vendors'] = Vendor::whereIn('id',$vendorAssignedIds)->orderBy('company','asc')->select('id','company')->get()->toArray();
                        }else{
                            $materialRequestComponentDetails[$iterator]['vendors'] = $allVendors;
                        }
                        $isClientSupplied = QuotationMaterial::where('quotation_id',$purchaseRequest->quotation_id)->where('material_id',$material_id)->where('is_client_supplied', true)->first();
                        if($isClientSupplied != null){
                            $materialRequestComponentDetails[$iterator]['vendors'] = array_merge($materialRequestComponentDetails[$iterator]['vendors'],[[
                                'id' => $purchaseRequest->projectSite->project->client_id,
                                'company' => $purchaseRequest->projectSite->project->client->company,
                                'is_client' => true
                            ]]);
                        }
                    }else{
                        $materialRequestComponentDetails[$iterator]['vendors'] = $allVendors;
                    }
                    $materialRequestComponentDetails[$iterator]['disapproved_by_user_name'] = '-';
                    $purchaseOrderRequestComponentData = PurchaseOrderRequestComponent::join('purchase_request_component_vendor_relation','purchase_request_component_vendor_relation.id','=','purchase_order_request_components.purchase_request_component_vendor_relation_id')
                        ->join('purchase_request_components','purchase_request_components.id','=','purchase_request_component_vendor_relation.purchase_request_component_id')
                        ->where('purchase_request_components.material_request_component_id',$materialRequestComponent['id'])->orderBy('id','desc')->select('purchase_order_request_components.id','purchase_order_request_components.is_approved','purchase_order_request_components.approve_disapprove_by_user','purchase_order_request_components.purchase_request_component_vendor_relation_id')->get();
                    $purchaseOrderRequestComponentNullIds = PurchaseOrderRequestComponent::join('purchase_request_component_vendor_relation','purchase_request_component_vendor_relation.id','=','purchase_order_request_components.purchase_request_component_vendor_relation_id')
                        ->join('purchase_request_components','purchase_request_components.id','=','purchase_request_component_vendor_relation.purchase_request_component_id')
                        ->where('purchase_request_components.material_request_component_id',$materialRequestComponent['id'])->orderBy('id','desc')->whereNull('is_approved')->pluck('purchase_order_request_components.id');
                    if(count($purchaseOrderRequestComponentData) > 0){
                        $disapprovedCount = $purchaseOrderRequestComponentData->where('is_approved',false)->whereNotIn('id',$purchaseOrderRequestComponentNullIds)->count();
                        if($disapprovedCount == count($purchaseOrderRequestComponentData)){
                            $disapprovedUser = $purchaseOrderRequestComponentData->first()->user;
                            $materialRequestComponentDetails[$iterator]['disapproved_by_user_name'] = $disapprovedUser['first_name'].' '.$disapprovedUser['last_name'];
                        }

                    }
                    $iterator++;
                }
                return view('purchase/purchase-request/edit-approved')->with(compact('purchaseRequest','materialRequestComponentDetails','userRole','assignedVendorData','assignedClientData'));
            }else{
                return view('purchase/purchase-request/edit-draft');
            }
        }catch(\Exception $e){
            $data = [
                'action' => 'Get Purchase Request Edit View',
                'params' => $request->all(),
                'status' => $status,
                'id' => $id,
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
        }
    }

    use NotificationTrait;
    public function create(Request $request){
        try{
            $user = Auth::user();
            $requestData = $request->all();
            if($request->has('item_list')){
                $materialRequestComponentId = $this->createMaterialRequest($request->except('material_request_component_ids'),$user,true);
                if($materialRequestComponentId == null){
                    $request->session()->flash('error', 'Something Went Wrong');
                    return redirect('purchase/purchase-request/create');
                }else{
                    if($request->has('material_request_component_ids')){
                        $materialRequestComponentIds = array_merge($materialRequestComponentId,$request['material_request_component_ids']);
                    }else{
                        $materialRequestComponentIds = $materialRequestComponentId;
                    }
                }
            }else{
                $materialRequestComponentIds = $request['material_request_component_ids'];
            }
            $purchaseRequestData = array();
            $quotationId = Quotation::where('project_site_id',$requestData['project_site_id'])->first();
            if($quotationId != null){
                $purchaseRequestData['quotation_id'] = $quotationId['id'];
            }
            $purchaseRequestData['project_site_id'] = $request['project_site_id'];
            $purchaseRequestData['user_id'] = $user['id'];
            $purchaseRequestData['behalf_of_user_id'] = $requestData['user_id'];
            $purchaseRequestData['assigned_to'] = Role::join('user_has_roles','roles.id','=','user_has_roles.role_id')
                ->join('users','users.id','=','user_has_roles.user_id')
                ->where('roles.slug','superadmin')
                ->pluck('users.id')->first();
            $purchaseRequestedStatus = PurchaseRequestComponentStatuses::where('slug','purchase-requested')->first();
            $purchaseRequestData['purchase_component_status_id'] = $purchaseRequestedStatus->id;
            $today = date('Y-m-d');
            $purchaseRequestCount = PurchaseRequest::whereDate('created_at',$today)->count();
            $purchaseRequestData['serial_no'] = ($purchaseRequestCount+1);
            $purchaseRequestData['format_id'] = $this->getPurchaseIDFormat('purchase-request',$requestData['project_site_id'],Carbon::now(),$purchaseRequestData['serial_no']);
            $purchaseRequest = PurchaseRequest::create($purchaseRequestData);
            $userTokens = User::join('user_has_permissions','users.id','=','user_has_permissions.user_id')
                ->join('permissions','permissions.id','=','user_has_permissions.permission_id')
                ->join('user_project_site_relation','users.id','=','user_project_site_relation.user_id')
                ->whereIn('permissions.name',['approve-purchase-request','create-purchase-order'])
                ->where('user_project_site_relation.project_site_id',$request['project_site_id'])
                ->select('users.web_fcm_token as web_fcm_token', 'users.mobile_fcm_token as mobile_fcm_token')
                ->get()
                ->toArray();
            $webTokens = array_column($userTokens,'web_fcm_token');
            $mobileTokens = array_column($userTokens,'mobile_fcm_token');
            $notificationString = '2 -'.$purchaseRequest->projectSite->project->name.' '.$purchaseRequest->projectSite->name;
            $notificationString .= ' '.$user['first_name'].' '.$user['last_name'].'Purchase Request Created.';
            $this->sendPushNotification('Manisha Construction',$notificationString,$webTokens,$mobileTokens,'c-p-r');
            foreach ($materialRequestComponentIds as $materialRequestComponentId) {
                PurchaseRequestComponent::create([
                    'purchase_request_id' => $purchaseRequest['id'],
                    'material_request_component_id' => $materialRequestComponentId
                ]);
            }
            $PRAssignedStatusId = PurchaseRequestComponentStatuses::where('slug','p-r-assigned')->pluck('id')->first();
            if($request->has('material_request_component_ids')) {
                MaterialRequestComponents::whereIn('id', $request['material_request_component_ids'])->update(['component_status_id' => $PRAssignedStatusId]);
            }
            $materialComponentHistoryData = array();
            $materialComponentHistoryData['remark'] = '';
            $materialComponentHistoryData['user_id'] = $user['id'];
            $materialComponentHistoryData['component_status_id'] = $PRAssignedStatusId;
            if($request->has('material_request_component_ids')) {
                foreach ($request['material_request_component_ids'] as $materialRequestComponentId) {
                    $materialRequestComponent = MaterialRequestComponents::where('id',$materialRequestComponentId)->first();
                    $materialComponentHistoryData['material_request_component_id'] = $materialRequestComponentId;
                    MaterialRequestComponentHistory::create($materialComponentHistoryData);
                    $materialRequestComponentVersionData = [
                        'material_request_component_id' => $materialRequestComponentId,
                        'component_status_id' => $PRAssignedStatusId,
                        'user_id' => $user['id'],
                        'quantity' => $materialRequestComponent['quantity'],
                        'unit_id' => $materialRequestComponent['unit_id'],
                        'remark' => ''
                    ];
                    $materialRequestComponentVersion = MaterialRequestComponentVersion::create($materialRequestComponentVersionData);
                }
            }
            $request->session()->flash('success', 'Purchase Request created successfully.');
        }catch (\Exception $e){
            $data = [
                'action' => 'Create Purchase Request',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
        }
        return redirect('/purchase/purchase-request/manage');
    }

    public function purchaseRequestListing(Request $request){
        try{
            $skip = $request->start;
            $take = $request->length;
            $postdata = null;
            $status = 0;
            $site_id = 0;
            $month = 0;
            $year = 0;
            $pr_count = 0;
            $pr_name = null;
            $totalrecordsCount = 0;
            $postDataArray = array();
            if ($request->has('pr_name')) {
                if ($request['pr_name'] != "") {
                    $pr_name = $request['pr_name'];
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
                $pr_count = $postDataArray['pr_count'];
            }
            if($request->has('site_id')){
                $site_id = $request->site_id;
            }
            $response = array();
            $responseStatus = 200;
            $purchaseRequests = array();

            if(Session::has('global_project_site')){
                $projectSiteId = Session::get('global_project_site');
            }else{
                $projectSiteId = null;
            }
            $ids = PurchaseRequest::where('project_site_id','=',$projectSiteId)->pluck('id');
            $filterFlag = true;
            if ($site_id != 0 && $filterFlag == true) {
                $ids = PurchaseRequest::whereIn('id',$ids)->where('project_site_id', $site_id)->pluck('id');
                if(count($ids) <= 0) {
                    $filterFlag = false;
                }
            }
            if ($year != 0 && $filterFlag == true) {
                $ids = PurchaseRequest::whereIn('id',$ids)->whereYear('created_at', $year)->pluck('id');
                if(count($ids) <= 0) {
                    $filterFlag = false;
                }
            }
            if ($month != 0 && $filterFlag == true) {
                $ids = PurchaseRequest::whereIn('id',$ids)->whereMonth('created_at', $month)->pluck('id');
                if(count($ids) <= 0) {
                    $filterFlag = false;
                }
            }
            if ($status != 0 && $filterFlag == true) {
                $ids = PurchaseRequest::whereIn('id',$ids)->where('purchase_component_status_id', $status)->pluck('id');
                if(count($ids) <= 0) {
                    $filterFlag = false;
                }
            }
            if ($pr_count != 0 && $filterFlag == true) {
                $ids = PurchaseRequest::whereIn('id',$ids)->where('serial_no', $pr_count)->pluck('id');
                if(count($ids) <= 0) {
                    $filterFlag = false;
                }
            }

            if ($pr_name != "" && $pr_name != null && $filterFlag == true) {
                $ids = PurchaseRequest::whereIn('id',$ids)
                    ->where('format_id','ilike', '%'.$pr_name.'%')->pluck('id');
                if(count($ids) <= 0) {
                    $filterFlag = false;
                }
            }

            if ($filterFlag) {
                $totalrecordsCount = PurchaseRequest::whereIn('id',$ids)->count();
                $purchaseRequests = PurchaseRequest::whereIn('id',$ids)
                                    ->skip($skip)->take($take)
                                    ->orderBy('created_at','desc')->get();
            }

            $iTotalRecords = count($purchaseRequests);
            $records = array();
            $records['data'] = array();
            $user = Auth::user();
            $end = $request->length < 0 ? count($purchaseRequests) : $request->length;
            for($iterator = 0,$pagination = 0; $iterator < $end && $pagination < count($purchaseRequests); $iterator++,$pagination++ ){
                $txnInfo = PurchaseRequestComponentStatuses::where('id',$purchaseRequests[$pagination]['purchase_component_status_id'])->select('slug','name')->first()->toArray();
                switch ($txnInfo['slug']){
                    case 'purchase-requested':
                        $status = "<span class=\"btn btn-xs btn-warning\"> ".$txnInfo['name']." </span>";
                        $action = '<div class="btn-group">
                            <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                Actions
                                <i class="fa fa-angle-down"></i>
                            </button>
                            <ul class="dropdown-menu pull-left" role="menu">';
                        if($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin' || $user->customHasPermission('approve-purchase-request') || $user->customHasPermission('edit-purchase-request')){
                            $action .= '<!--<li>'
                                .'<a href="/purchase/purchase-request/edit/'.$txnInfo['slug'].'/'.$purchaseRequests[$pagination]['id'].'">'.
                                '<i class="icon-docs"></i> Edit 
                                    </a>
                                </li>-->';
                        }
                        if($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin' || $user->customHasPermission('approve-purchase-request')){
                            $action .= '<li>
                                    <a href="javascript:void(0);" onclick="openApproveModal('.$purchaseRequests[$pagination]['id'].')">
                                        <i class="icon-tag"></i> Approve / Disapprove 
                                    </a>
                                </li>';
                        }
                        $action .='</ul>
                            </div>';
                        break;
                    case 'p-r-admin-approved':
                        $status = "<span class=\"btn btn-xs green-meadow\"> ".$txnInfo['name']." </span>";
                        $action = '<div class="btn-group">
                            <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                Actions
                                <i class="fa fa-angle-down"></i>
                            </button>
                            <ul class="dropdown-menu pull-left" role="menu">';
                        if($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin' || $user->customHasPermission('approve-purchase-request') || $user->customHasPermission('edit-purchase-request')){
                            $action .= '<li>'
                                    .'<a href="/purchase/purchase-request/edit/'.$txnInfo['slug'].'/'.$purchaseRequests[$pagination]['id'].'">'.
                                    '<i class="icon-docs"></i> Edit
                                    </a>
                                    </li>';
                        }
                        $action .='</ul>
                            </div>';
                        break;
                    case 'p-r-manager-approved':
                        $status = "<span class=\"btn btn-xs green-meadow\"> ".$txnInfo['name']." </span>";
                        $action = '<div class="btn-group">
                            <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                Actions
                                <i class="fa fa-angle-down"></i>
                            </button>
                            <ul class="dropdown-menu pull-left" role="menu">';
                        if($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin' || $user->customHasPermission('approve-purchase-request') || $user->customHasPermission('edit-purchase-request')){
                            $action .= '<li>'
                                .'<a href="/purchase/purchase-request/edit/p-r-admin-approved/'.$purchaseRequests[$pagination]['id'].'">'.
                                '<i class="icon-docs"></i> Edit
                                    </a>
                                    </li>';
                        }
                        $action .='</ul>
                            </div>';
                        break;

                    case 'p-r-manager-disapproved':
                    case 'p-r-admin-disapproved':
                        $status = "<span class=\"btn btn-xs btn-danger\"> ".$txnInfo['name']." </span>";
                        $action = '<div class="btn-group">
                            <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                Actions
                                <i class="fa fa-angle-down"></i>
                            </button>
                            <ul class="dropdown-menu pull-left" role="menu">';
                        if($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin' || $user->customHasPermission('approve-purchase-request') || $user->customHasPermission('edit-purchase-request')){
                            $action .= '<!--<li>'
                                .'<a href="/purchase/purchase-request/edit/'.$txnInfo['slug'].'">'.
                                '<i class="icon-docs"></i> Edit 
                                    </a>
                                </li>-->';
                        }
                        $action .='</ul>
                        </div>';
                        break;

                    default:
                        $status = "<span class=\"btn btn-xs btn-success\"> ".$txnInfo['name']." </span>";
                        $action = '<div class="btn-group">
                            <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                Actions
                                <i class="fa fa-angle-down"></i>
                            </button>
                            <ul class="dropdown-menu pull-left" role="menu">';
                        if($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin' || $user->customHasPermission('approve-purchase-request') || $user->customHasPermission('edit-purchase-request')){
                            $action .= '<!--<li>'
                                .'<a href="/purchase/purchase-request/edit/'.$txnInfo['slug'].'">'.
                                '<i class="icon-docs"></i> Edit 
                                    </a>
                                </li>-->';
                        }
                        $action .='</ul>
                        </div>';
                        break;
                }
                $isPurchaseOrderCreated = PurchaseOrder::where('purchase_request_id',$purchaseRequests[$pagination]['id'])->count();
                $purchaseRequestComponentIds = $purchaseRequests[$pagination]->purchaseRequestComponents->pluck('id')->toArray();
                $vendorAssignedCount = PurchaseRequestComponentVendorRelation::whereIn('purchase_request_component_id',$purchaseRequestComponentIds)->count();
                $purchaseOrderRequestCount = PurchaseOrderRequest::where('purchase_request_id',$purchaseRequests[$pagination]['id'])->count();
                if($isPurchaseOrderCreated > 0){
                $materialStatus = "<span class=\"btn btn-xs btn-warning\"> Purchase Order Created </span>";
                }elseif($purchaseOrderRequestCount > 0){
                    $materialStatus = "<span class=\"btn btn-xs btn-warning\"> Purchase Order Requested </span>";
                }elseif($vendorAssignedCount > 0){
                    $materialStatus = "<span class=\"btn btn-xs btn-warning\"> Vendor Assigned </span>";
                }else{
                    $materialStatus = "<span class=\"btn btn-xs btn-warning\"> Purchase Request Created </span>";
                }
                $projectdata = ProjectSite::join('projects','projects.id','=','project_sites.project_id')
                    ->join('clients','clients.id','=','projects.client_id')
                    ->where('project_sites.id','=',$purchaseRequests[$pagination]['project_site_id'])
                    ->select('project_sites.name as site_name','projects.name as proj_name', 'clients.company as company')->first()->toArray();
                $formatId  = '<a href="javascript:void(0);" onclick="openDetails('.$purchaseRequests[$pagination]['id'].')"  >
                        '.$this->getPurchaseIDFormat('purchase-request', $purchaseRequests[$pagination]['project_site_id'], $purchaseRequests[$pagination]['created_at'], $purchaseRequests[$pagination]['serial_no']).'
                    </a>';
                foreach($purchaseRequestComponentIds as $purchaseRequestComponentId){
                    $purchaseOrderRequestComponentData = PurchaseOrderRequestComponent::join('purchase_request_component_vendor_relation','purchase_request_component_vendor_relation.id','=','purchase_order_request_components.purchase_request_component_vendor_relation_id')
                        ->join('purchase_request_components','purchase_request_components.id','=','purchase_request_component_vendor_relation.purchase_request_component_id')
                        ->where('purchase_request_components.id',$purchaseRequestComponentId)->orderBy('id','desc')->select('purchase_order_request_components.id','purchase_order_request_components.is_approved','purchase_order_request_components.approve_disapprove_by_user','purchase_order_request_components.purchase_request_component_vendor_relation_id')->get();
                    $purchaseOrderRequestComponentNullIds = PurchaseOrderRequestComponent::join('purchase_request_component_vendor_relation','purchase_request_component_vendor_relation.id','=','purchase_order_request_components.purchase_request_component_vendor_relation_id')
                        ->join('purchase_request_components','purchase_request_components.id','=','purchase_request_component_vendor_relation.purchase_request_component_id')
                        ->where('purchase_request_components.id',$purchaseRequestComponentId)->orderBy('id','desc')->whereNull('is_approved')->pluck('purchase_order_request_components.id');
                    if(count($purchaseOrderRequestComponentData) > 0){
                        $disapprovedCount = $purchaseOrderRequestComponentData->where('is_approved',false)->whereNotIn('id',$purchaseOrderRequestComponentNullIds)->count();
                        if($disapprovedCount == count($purchaseOrderRequestComponentData)){
                            $formatId = '<a href="javascript:void(0);" onclick="openDetails('.$purchaseRequests[$pagination]['id'].')" style="color: red">
                                            '.$this->getPurchaseIDFormat('purchase-request', $purchaseRequests[$pagination]['project_site_id'], $purchaseRequests[$pagination]['created_at'], $purchaseRequests[$pagination]['serial_no']).'
                                         </a>';
                            break;
                        }
                    }
                }

                $records['data'][$iterator] = [
                    $formatId,
                    $projectdata['company'],
                    $projectdata['proj_name']." - ".$projectdata['site_name'],
                    date('d M Y', strtotime($purchaseRequests[$pagination]['created_at'])),
                    $status,
                    $materialStatus,
                    $action
                ];
            }
            $records["draw"] = intval($request->draw);
            $records["recordsTotal"] = $totalrecordsCount;
            $records["recordsFiltered"] = $totalrecordsCount;
        }catch (\Exception $e){
            $data = [
                'action' => 'Purchase Requests listing',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            $responseStatus = 500;
            $records = array();
        }
        return response()->json($records,$responseStatus);
    }

    public function changePurchaseRequestStatus(Request $request,$newStatus,$purchaseRequestId = null){
        try{
            if($purchaseRequestId == null){
                $purchaseRequestId = $request->purchaseRequestId;
            }
            $user = Auth::user();
            $materialComponentHistoryData = array();
            $materialComponentHistoryData['remark'] = $request->remark;
            $materialComponentHistoryData['user_id'] = $user->id;
            switch ($newStatus){
                case 'approved':
                    if($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin'){
                        $approveStatusId = PurchaseRequestComponentStatuses::where('slug','p-r-admin-approved')->pluck('id')->first();
                    }else{
                        $approveStatusId = PurchaseRequestComponentStatuses::where('slug','p-r-manager-approved')->pluck('id')->first();
                    }
                    $purchaseRequest = PurchaseRequest::where('id',$purchaseRequestId)->first();
                    $purchaseRequest->update([
                                        'purchase_component_status_id' => $approveStatusId
                                    ]);
                    $projectSiteId = $purchaseRequest['project_site_id'];
                    $vendorAssignmentAclUserToken = UserHasPermission::join('permissions','permissions.id','=','user_has_permissions.permission_id')
                                                    ->join('users','users.id','=','user_has_permissions.user_id')
                                                    ->join('user_project_site_relation','user_project_site_relation.user_id','users.id')
                                                    ->where('permissions.name','create-vendor-assignment')
                                                    ->where('user_project_site_relation.project_site_id',$projectSiteId)
                                                    ->select('users.web_fcm_token as web_fcm_function','users.mobile_fcm_token as mobile_fcm_function')
                                                    ->get()->toArray();
                    $webTokens = array_column($vendorAssignmentAclUserToken,'web_fcm_function');
                    $mobileTokens = array_column($vendorAssignmentAclUserToken,'mobile_fcm_function');
                    $notificationString = '3 -'.$purchaseRequest->projectSite->project->name.' '.$purchaseRequest->projectSite->name;
                    $notificationString .= ' '.$user['first_name'].' '.$user['last_name'].'Purchase Request Approved.';
                    $notificationString .= 'PR number: '.$purchaseRequest->format_id;
                    $this->sendPushNotification('Manisha Construction',$notificationString,array_unique($webTokens),array_unique($mobileTokens),'p-r-a');
                    $materialComponentIds = PurchaseRequestComponent::where('purchase_request_id',$purchaseRequestId)->pluck('material_request_component_id')->toArray();
                    MaterialRequestComponents::whereIn('id',$materialComponentIds)->update(['component_status_id' => $approveStatusId]);
                    $materialComponentHistoryData['component_status_id'] = $approveStatusId;
                    foreach($materialComponentIds as $materialComponentId) {
                        $materialRequestComponentData = MaterialRequestComponents::where('id',$materialComponentId)->first();
                        $materialComponentHistoryData['material_request_component_id'] = $materialComponentId;
                        MaterialRequestComponentHistory::create($materialComponentHistoryData);
                        $materialRequestComponentVersionData = [
                            'material_request_component_id' => $materialComponentId,
                            'component_status_id' => $approveStatusId,
                            'quantity' => $materialRequestComponentData['quantity'],
                            'unit_id' => $materialRequestComponentData['unit_id'],
                            'user_id' => $user['id'],
                            'remark' => $request->remark
                        ];
                        $materialRequestComponentVersion = MaterialRequestComponentVersion::create($materialRequestComponentVersionData);
                    }
                    break;

                case 'disapproved':
                    if($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin'){
                        $disapproveStatusId = PurchaseRequestComponentStatuses::where('slug','p-r-admin-disapproved')->pluck('id')->first();
                    }else{
                        $disapproveStatusId = PurchaseRequestComponentStatuses::where('slug','p-r-manager-disapproved')->pluck('id')->first();
                    }
                    PurchaseRequest::where('id',$purchaseRequestId)->update([
                        'purchase_component_status_id' => $disapproveStatusId
                    ]);
                    $purchaseRequest = PurchaseRequest::findOrFail($purchaseRequestId);
                    $webTokens = [$purchaseRequest->onBehalfOfUser->web_fcm_token];
                    $mobileTokens = [$purchaseRequest->onBehalfOfUser->mobile_fcm_token];
                    $MRcreatedUsersTokens = User::join('material_requests','material_requests.on_behalf_of','=','users.id')
                                            ->join('material_request_components','material_request_components.material_request_id','=','material_requests.id')
                                            ->join('purchase_request_components','purchase_request_components.material_request_component_id','=','material_request_components.id')
                                            ->join('purchase_requests','purchase_requests.id','=','purchase_request_components.purchase_request_id')
                                            ->where('purchase_requests.id', $purchaseRequest->id)
                                            ->select('users.mobile_fcm_token','users.web_fcm_token')
                                            ->get()
                                            ->toArray();
                    $webTokens = array_merge($webTokens,array_column($MRcreatedUsersTokens,'web_fcm_token'));
                    $mobileTokens = array_merge($mobileTokens,array_column($MRcreatedUsersTokens,'mobile_fcm_token'));
                    $notificationString = '2D -'.$purchaseRequest->projectSite->project->name.' '.$purchaseRequest->projectSite->name;
                    $notificationString .= ' '.$user['first_name'].' '.$user['last_name'].'Material Disapproved.';
                    $notificationString .= ' '.$request->remark;
                    $this->sendPushNotification('Manisha Construction',$notificationString,$webTokens,$mobileTokens,'d-p-r');
                    $materialComponentIds = PurchaseRequestComponent::where('purchase_request_id',$purchaseRequestId)->pluck('material_request_component_id')->toArray();
                    MaterialRequestComponents::whereIn('id',$materialComponentIds)->update(['component_status_id' => $disapproveStatusId]);
                    $materialComponentHistoryData['component_status_id'] = $disapproveStatusId;
                    foreach($materialComponentIds as $materialComponentId) {
                        $materialRequestComponentData = MaterialRequestComponents::where('id',$materialComponentId)->first();
                        $materialComponentHistoryData['material_request_component_id'] = $materialComponentId;
                        MaterialRequestComponentHistory::create($materialComponentHistoryData);
                        $materialRequestComponentVersionData = [
                            'material_request_component_id' => $materialComponentId,
                            'component_status_id' => $disapproveStatusId,
                            'quantity' => $materialRequestComponentData['quantity'],
                            'user_id' => $user['id'],
                            'unit_id' => $materialRequestComponentData['unit_id'],
                            'remark' => $request->remark,
                        ];
                        $materialRequestComponentVersion = MaterialRequestComponentVersion::create($materialRequestComponentVersionData);
                    }
                    break;

                default:
                    break;
            }
            $request->session()->flash('success', 'Purchase Request status changed successfully.');
            return redirect('/purchase/purchase-request/manage');
        }catch (\Exception $e){
            $data = [
                'action' => 'Change Purchase request status',
                'params' => $request->all(),
                'newStatus' => $newStatus,
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function assignVendors(Request $request){
        try{
            $data = $request->all();
            foreach($data['vendor_materials'] as $vendorId => $materialRequestComponentIds){
                $vendorIdArray = explode('_',$vendorId);
                if(count($vendorIdArray) == 2){
                    /*client Supplied*/
                    $clientId = $vendorIdArray[1];
                    $purchaseRequestComponentIds = PurchaseRequestComponent::whereIn('material_request_component_id',$materialRequestComponentIds)->pluck('id')->toArray();
                    $purchaseRequestFormat = PurchaseRequest::join('purchase_request_components', 'purchase_request_components.purchase_request_id','=','purchase_requests.id')
                        ->where('purchase_request_components.id', $purchaseRequestComponentIds[0])
                        ->pluck('purchase_requests.format_id')->first();
                    if(count($purchaseRequestComponentIds) > 0){
                        $purchaseRequestId = PurchaseRequestComponent::where('id', $purchaseRequestComponentIds[0])->pluck('purchase_request_id')->first();
                        $alreadyCreatedPurchaseRequestVendorRelationIds = PurchaseRequestComponentVendorRelation::join('purchase_request_components','purchase_request_components.id','=','purchase_request_component_vendor_relation.purchase_request_component_id')
                            ->where('purchase_request_components.purchase_request_id', $purchaseRequestId)
                            ->where('purchase_request_component_vendor_relation.client_id', $clientId)
                            ->whereNotIn('purchase_request_component_vendor_relation.purchase_request_component_id',$purchaseRequestComponentIds)
                            ->pluck('purchase_request_component_vendor_relation.id')
                            ->toArray();
                        PurchaseRequestComponentVendorRelation::whereIn('id', $alreadyCreatedPurchaseRequestVendorRelationIds)->delete();
                    }
                    if(array_key_exists('checked_vendor_materials',$data)){
                        if(array_key_exists($vendorId,$data['checked_vendor_materials'])){
                            $vendorInfo = Client::findOrFail($clientId)->toArray();
                            $vendorInfo['materials'] = array();
                        }
                    }
                    $purchaseVendorAssignData = array();
                    $purchaseVendorAssignData['client_id'] = $clientId;
                    $purchaseVendorAssignData['is_client'] = true;
                    $iterator = 0;
                    $jIterator = 0;
                    $mailInfoData = array();
                    foreach($materialRequestComponentIds as $materialRequestComponentId){
                        $materialRequestComponent = MaterialRequestComponents::findOrFail($materialRequestComponentId);
                        $purchaseVendorAssignData['is_email_sent'] = false;
                        $purchaseVendorAssignData['purchase_request_component_id'] = $materialRequestComponent->purchaseRequestComponent->id;
                        // create
                        $purchaseComponentVendorRelation = PurchaseRequestComponentVendorRelation::where('client_id',$clientId)->where('is_client', true)->where('purchase_request_component_id',$purchaseVendorAssignData['purchase_request_component_id'])->first();
                        if($purchaseComponentVendorRelation == null){
                            $purchaseComponentVendorRelation = PurchaseRequestComponentVendorRelation::create($purchaseVendorAssignData);
                        }
                        $projectSiteInfo = array();
                        $projectSiteInfo['project_name'] = $materialRequestComponent->materialRequest->projectSite->project->name;
                        $projectSiteInfo['project_site_name'] = $materialRequestComponent->materialRequest->projectSite->name;
                        $projectSiteInfo['project_site_address'] = $materialRequestComponent->materialRequest->projectSite->address;
                        if($materialRequestComponent->materialRequest->projectSite->city_id == null){
                            $projectSiteInfo['project_site_city'] = '';
                        }else{
                            $projectSiteInfo['project_site_city'] = $materialRequestComponent->materialRequest->projectSite->city->name;
                        }
			            $projectSiteInfo['delivery_address'] = $projectSiteInfo['project_name'].', '.$projectSiteInfo['project_site_name'].', '.$projectSiteInfo['project_site_address'].', '.$projectSiteInfo['project_site_city'];
                        if(array_key_exists('checked_vendor_materials',$data)){
                            if(array_key_exists($vendorId,$data['checked_vendor_materials'])){
                                $mailInfoData[$jIterator] = [
                                    'user_id' => Auth::user()->id,
                                    'type_slug' => 'for-quotation',
                                    'is_client' => true,
                                    'client_id' => $clientId,
                                    'created_at' => Carbon::now(),
                                    'updated_at' => Carbon::now()
                                ];
                                if(in_array($materialRequestComponentId,$data['checked_vendor_materials'][$vendorId])){
                                    $vendorInfo['materials'][$iterator]['item_name'] = $materialRequestComponent->name;
                                    $vendorInfo['materials'][$iterator]['quantity'] = $materialRequestComponent->quantity;
                                    $vendorInfo['materials'][$iterator]['unit'] = $materialRequestComponent->unit->name;
                                    $iterator++;
                                    $jIterator++;
                                }
                            }
                        }
                    }
                }else{
                    $purchaseRequestComponentIds = PurchaseRequestComponent::whereIn('material_request_component_id',$materialRequestComponentIds)->pluck('id')->toArray();
                    $purchaseRequestFormat = PurchaseRequest::join('purchase_request_components', 'purchase_request_components.purchase_request_id','=','purchase_requests.id')
                                                ->where('purchase_request_components.id', $purchaseRequestComponentIds[0])
                                                ->pluck('purchase_requests.format_id')->first();
                    if(count($purchaseRequestComponentIds) > 0){
                        $purchaseRequestId = PurchaseRequestComponent::where('id', $purchaseRequestComponentIds[0])->pluck('purchase_request_id')->first();
                        $alreadyCreatedPurchaseRequestVendorRelationIds = PurchaseRequestComponentVendorRelation::join('purchase_request_components','purchase_request_components.id','=','purchase_request_component_vendor_relation.purchase_request_component_id')
                            ->where('purchase_request_components.purchase_request_id', $purchaseRequestId)
                            ->where('purchase_request_component_vendor_relation.vendor_id', $vendorId)
                            ->whereNotIn('purchase_request_component_vendor_relation.purchase_request_component_id',$purchaseRequestComponentIds)
                            ->pluck('purchase_request_component_vendor_relation.id')
                            ->toArray();
                        PurchaseRequestComponentVendorRelation::whereIn('id', $alreadyCreatedPurchaseRequestVendorRelationIds)->delete();
                    }
                    if(array_key_exists('checked_vendor_materials',$data)){
                        if(array_key_exists($vendorId,$data['checked_vendor_materials'])){
                            $vendorInfo = Vendor::findOrFail($vendorId)->toArray();
                            $vendorInfo['materials'] = array();
                        }
                    }
                    $purchaseVendorAssignData = array();
                    $purchaseVendorAssignData['vendor_id'] = $vendorId;
                    $iterator = 0;
                    $jIterator = 0;
                    $mailInfoData = array();
                    foreach($materialRequestComponentIds as $materialRequestComponentId){
                        $materialRequestComponent = MaterialRequestComponents::findOrFail($materialRequestComponentId);
                        $purchaseRequestId = $materialRequestComponent->purchaseRequestComponent->purchaseRequest->id;
                        $purchaseVendorAssignData['is_email_sent'] = false;
                        $purchaseVendorAssignData['purchase_request_component_id'] = $materialRequestComponent->purchaseRequestComponent->id;
                        // create
                        $purchaseComponentVendorRelation = PurchaseRequestComponentVendorRelation::where('vendor_id',$vendorId)->where('purchase_request_component_id',$purchaseVendorAssignData['purchase_request_component_id'])->first();
                        if($purchaseComponentVendorRelation == null){
                            $purchaseComponentVendorRelation = PurchaseRequestComponentVendorRelation::create($purchaseVendorAssignData);
                        }
                        $projectSiteInfo = array();
                        $projectSiteInfo['project_name'] = $materialRequestComponent->materialRequest->projectSite->project->name;
                        $projectSiteInfo['project_site_name'] = $materialRequestComponent->materialRequest->projectSite->name;
                        $projectSiteInfo['project_site_address'] = $materialRequestComponent->materialRequest->projectSite->address;
                        if($materialRequestComponent->materialRequest->projectSite->city_id == null){
                            $projectSiteInfo['project_site_city'] = '';
                        }else{
                            $projectSiteInfo['project_site_city'] = $materialRequestComponent->materialRequest->projectSite->city->name;
                        }
			            $projectSiteInfo['delivery_address'] = $projectSiteInfo['project_name'].', '.$projectSiteInfo['project_site_name'].', '.$projectSiteInfo['project_site_address'].', '.$projectSiteInfo['project_site_city'];
                        if(array_key_exists('checked_vendor_materials',$data)){
                            if(array_key_exists($vendorId,$data['checked_vendor_materials'])){
                                $mailInfoData[$jIterator] = [
                                    'user_id' => Auth::user()->id,
                                    'type_slug' => 'for-quotation',
                                    'vendor_id' => $vendorId,
                                    'is_client' => false,
                                    'reference_id' => $purchaseRequestId,
                                    'created_at' => Carbon::now(),
                                    'updated_at' => Carbon::now()
                                ];
                                if(in_array($materialRequestComponentId,$data['checked_vendor_materials'][$vendorId])){
                                    $vendorInfo['materials'][$iterator]['item_name'] = $materialRequestComponent->name;
                                    $vendorInfo['materials'][$iterator]['quantity'] = $materialRequestComponent->quantity;
                                    $vendorInfo['materials'][$iterator]['unit'] = $materialRequestComponent->unit->name;
                                    $iterator++;
                                    $jIterator++;
                                }
                            }
                        }
                    }
                }
                if(isset($vendorInfo)){
                    $now = date('j_M_Y_His');
                    $pdfTitle = "Purchase Request";
                    $pdfName = $purchaseRequestFormat."_".$now;
                    $pdf = App::make('dompdf.wrapper');
                    $formatId = $purchaseRequestFormat;
                    $pdf->loadHTML(view('purchase.purchase-request.pdf.vendor-quotation')->with(compact('vendorInfo','projectSiteInfo','pdfTitle','formatId')));
                    $pdfDirectoryPath = env('PURCHASE_VENDOR_ASSIGNMENT_PDF_FOLDER');
                    $pdfFileName = sha1($vendorId).'.pdf';
                    $pdfUploadPath = public_path().$pdfDirectoryPath.'/'.$pdfFileName;
                    $pdfContent = $pdf->stream($pdfName);
                    if($data['is_mail'] == 1){
                        if(file_exists($pdfUploadPath)){
                            unlink($pdfUploadPath);
                        }
                        if (!file_exists($pdfDirectoryPath)) {
                            File::makeDirectory(public_path().$pdfDirectoryPath, $mode = 0777, true, true);
                        }
                        file_put_contents($pdfUploadPath,$pdfContent);
                        $mailData = ['path' => $pdfUploadPath, 'toMail' => $vendorInfo['email']];
                        $mailMessage = 'Please send the quotation of materials listed in the attachment.';
                        Mail::send('purchase.purchase-request.email.vendor-quotation', ['mailMessage' => $mailMessage], function($message) use ($mailData,$purchaseRequestFormat){
                            $message->subject('Quotation Requirement ('.$purchaseRequestFormat.')');
                            $message->to($mailData['toMail']);
                            $message->from(env('MAIL_USERNAME'));
                            $message->attach($mailData['path']);
                        });
                        PurchaseRequestComponentVendorMailInfo::insert($mailInfoData);
                        PurchaseRequestComponentVendorRelation::whereIn('id',array_column($mailInfoData,'purchase_request_component_vendor_relation_id'))->update(['is_email_sent' => true]);
                        unlink($pdfUploadPath);
                    }else{
                        return $pdf->stream();
                    }
                }
            }
            $request->session()->flash('success','Vendors assigned successfully');
            return redirect('/purchase/purchase-request/edit/p-r-admin-approved/'.$data['purchase_request_id']);
        }catch (\Exception $e){
            $data = [
                'action' => 'Purchase Assign Vendors',
                'exception' => $e->getMessage(),
                'params' => $request->all()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getPurchaseRequestDetails(Request $request,$purchaseRequestId){
        try{
            $purchaseRequest = PurchaseRequest::where('id',$purchaseRequestId)->first();
            return view('partials.purchase.purchase-request.detail')->with(compact('purchaseRequest'));
        }catch(\Exception $e){
            $data = [
                'action' => 'Get Purchase Request Details',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            return response()->json([],500);
        }
    }

    public function getMaterialInventoryQuantity(Request $request){
        try{
            $status = 200;
            $response = array();
            $materialName = MaterialRequestComponents::where('id',$request->material_request_component_id)->pluck('name')->first();
            $inventoryComponents = InventoryComponent::where('name','ilike',$materialName)->get();
            $projectSiteInfo = array();
            if(count($inventoryComponents) > 0){
                foreach($inventoryComponents as $inventoryComponent){
                    if($inventoryComponent->is_material == true){
                        $materialUnit = Material::where('id',$inventoryComponent['reference_id'])->pluck('unit_id')->first();
                        $inTransferQuantities = InventoryComponentTransfers::join('inventory_transfer_types','inventory_transfer_types.id','=','inventory_component_transfers.transfer_type_id')
                            ->where('inventory_transfer_types.type','ilike','in')
                            ->where('inventory_component_transfers.inventory_component_id',$inventoryComponent->id)
                            ->where('inventory_component_transfers.inventory_component_transfer_status_id',InventoryComponentTransferStatus::where('slug','approved')->pluck('id')->first())
                            ->select('inventory_component_transfers.quantity as quantity','inventory_component_transfers.unit_id as unit_id')
                            ->get();
                        $outTransferQuantities = InventoryComponentTransfers::join('inventory_transfer_types','inventory_transfer_types.id','=','inventory_component_transfers.transfer_type_id')
                            ->where('inventory_transfer_types.type','ilike','out')
                            ->where('inventory_component_transfers.inventory_component_id',$inventoryComponent->id)
                            ->where('inventory_component_transfers.inventory_component_transfer_status_id',InventoryComponentTransferStatus::where('slug','approved')->pluck('id')->first())
                            ->select('inventory_component_transfers.quantity as quantity','inventory_component_transfers.unit_id as unit_id')
                            ->get();
                        $inQuantity = $outQuantity = 0;
                        foreach($inTransferQuantities as $inTransferQuantity){
                            $unitConversionQuantity = UnitHelper::unitQuantityConversion($inTransferQuantity['unit_id'],$materialUnit,$inTransferQuantity['quantity']);
                            if(!is_array($unitConversionQuantity)){
                                $inQuantity += $unitConversionQuantity;
                            }
                        }
                        foreach($outTransferQuantities as $outTransferQuantity){
                            $unitConversionQuantity = UnitHelper::unitQuantityConversion($outTransferQuantity['unit_id'],$materialUnit,$outTransferQuantity['quantity']);
                            if(!is_array($unitConversionQuantity)){
                                $outQuantity += $unitConversionQuantity;
                            }
                        }
                    }else{
                        $inQuantity = InventoryComponentTransfers::join('inventory_transfer_types','inventory_transfer_types.id','=','inventory_component_transfers.transfer_type_id')
                            ->where('inventory_transfer_types.type','ilike','in')
                            ->where('inventory_component_transfers.inventory_component_id',$inventoryComponent->id)
                            ->where('inventory_component_transfers.inventory_component_transfer_status_id',InventoryComponentTransferStatus::where('slug','approved')->pluck('id')->first())
                            ->sum('inventory_component_transfers.quantity');
                        $outQuantity = InventoryComponentTransfers::join('inventory_transfer_types','inventory_transfer_types.id','=','inventory_component_transfers.transfer_type_id')
                            ->where('inventory_transfer_types.type','ilike','out')
                            ->where('inventory_component_transfers.inventory_component_id',$inventoryComponent->id)
                            ->where('inventory_component_transfers.inventory_component_transfer_status_id',InventoryComponentTransferStatus::where('slug','approved')->pluck('id')->first())
                            ->sum('inventory_component_transfers.quantity');
                    }
                    $availableQuantity = $inQuantity - $outQuantity;
                    $projectSiteInfo[] = [
                        'quantity' => $availableQuantity,
                        'project' => $inventoryComponent->projectSite->project->name,
                        'project_site' => $inventoryComponent->projectSite->name
                    ];
                }
                return view('partials.purchase.purchase-request.inventory-quantity')->with(compact('projectSiteInfo'));
            }else{
                return response()->json(['message' => 'Material is not available at any other site'] , 201);
            }
        }catch (\Exception $e){
            $data = [
                'action' => 'Get material inventory quantity',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            return response()->json([], 500);
        }
    }

    public function editComponentQuantity(Request $request){
        try{
            $user = Auth::user();
            $materialRequestComponent = MaterialRequestComponents::findOrFail($request->material_request_component_id);
            $materialRequestComponent->update(['quantity' => $request->quantity]);
            $materialRequestComponentVersionData = [
                'material_request_component_id' => $materialRequestComponent->id,
                'component_status_id' => $materialRequestComponent->component_status_id,
                'quantity' => $request->quantity,
                'unit_id' => $materialRequestComponent['unit_id'],
                'user_id' => $user['id'],
                'remark' => $request->remark,
                'show_p_r_detail' => true
            ];
            $materialRequestComponentVersion = MaterialRequestComponentVersion::create($materialRequestComponentVersionData);
            $status = 200;
            $response = [
                'message' => 'Quantity for component edited successfully',
                'quantity' => $request->quantity
            ];
        }catch (\Exception $e){
            $data = [
                'action' => 'Edit Purchase Request Component quantity',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            $status = 500;
            $response = [
                'message' => 'Something went wrong.'
            ];
            Log::critical(json_encode($data));
        }
        return response()->json($response, $status);
    }

}
