<?php

namespace App\Http\Controllers\Purchase;

use App\Client;
use App\Http\Controllers\CustomTraits\Notification\NotificationTrait;
use App\Http\Controllers\CustomTraits\Purchase\MaterialRequestTrait;
use App\Material;
use App\MaterialRequestComponentHistory;
use App\MaterialRequestComponents;
use App\MaterialRequestComponentTypes;
use App\MaterialRequestComponentVersion;
use App\MaterialRequests;
use App\Project;
use App\ProjectSite;
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
            return view('purchase/purchase-request/create')->with(compact('materialRequestList','nosUnitId','units'));
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
                $allVendors = Vendor::where('is_active','true')->select('id','company')->get()->toArray();
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
                            $materialRequestComponentDetails[$iterator]['vendors'] = Vendor::whereIn('id',$vendorAssignedIds)->select('id','company')->get()->toArray();
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
                ->whereNotNull('users.web_fcm_token')
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
        return redirect('purchase/purchase-request/create');
    }

    public function purchaseRequestListing(Request $request){
        try{
            $postdata = null;
            $status = 0;
            $site_id = 0;
            $month = 0;
            $year = 0;
            $pr_count = 0;
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
            $ids = PurchaseRequest::all()->pluck('id');
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
            if ($filterFlag) {
                $purchaseRequests = PurchaseRequest::whereIn('id',$ids)->orderBy('created_at','desc')->get()->toArray();
            }
            $iTotalRecords = count($purchaseRequests);
            $records = array();
            $records['data'] = array();
            $user = Auth::user();
            $end = $request->length < 0 ? count($purchaseRequests) : $request->length;
            for($iterator = 0,$pagination = $request->start; $iterator < $end && $pagination < count($purchaseRequests); $iterator++,$pagination++ ){
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
                $projectdata = ProjectSite::join('projects','projects.id','=','project_sites.project_id')
                    ->join('clients','clients.id','=','projects.client_id')
                    ->where('project_sites.id','=',$purchaseRequests[$pagination]['project_site_id'])
                    ->select('project_sites.name as site_name','projects.name as proj_name', 'clients.company as company')->first()->toArray();
                $records['data'][$iterator] = [
                    $this->getPurchaseIDFormat('purchase-request', $purchaseRequests[$pagination]['project_site_id'], $purchaseRequests[$pagination]['created_at'], $purchaseRequests[$pagination]['serial_no']),
                    $projectdata['company'],
                    $projectdata['proj_name']." - ".$projectdata['site_name'],
                    date('d M Y', strtotime($purchaseRequests[$pagination]['created_at'])),
                    $status,
                    $action
                ];
            }
            $records["draw"] = intval($request->draw);
            $records["recordsTotal"] = $iTotalRecords;
            $records["recordsFiltered"] = $iTotalRecords;
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
                    PurchaseRequest::where('id',$purchaseRequestId)->update([
                                        'purchase_component_status_id' => $approveStatusId
                                    ]);
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
                    PurchaseRequestComponentVendorRelation::where('client_id',$clientId)->whereNotIn('purchase_request_component_id',$purchaseRequestComponentIds)->delete();
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
                    PurchaseRequestComponentVendorRelation::where('vendor_id',$vendorId)->whereNotIn('purchase_request_component_id',$purchaseRequestComponentIds)->delete();
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
                        if(array_key_exists('checked_vendor_materials',$data)){
                            if(array_key_exists($vendorId,$data['checked_vendor_materials'])){
                                $mailInfoData[$jIterator] = [
                                    'user_id' => Auth::user()->id,
                                    'type_slug' => 'for-quotation',
                                    'vendor_id' => $vendorId,
                                    'is_client' => false,
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
                    $pdf = App::make('dompdf.wrapper');
                    $pdf->loadHTML(view('purchase.purchase-request.pdf.vendor-quotation')->with(compact('vendorInfo','projectSiteInfo')));
                    $pdfDirectoryPath = env('PURCHASE_VENDOR_ASSIGNMENT_PDF_FOLDER');
                    $pdfFileName = sha1($vendorId).'.pdf';
                    $pdfUploadPath = public_path().$pdfDirectoryPath.'/'.$pdfFileName;
                    $pdfContent = $pdf->stream();
                    if($data['is_mail'] == 1){
                        if(file_exists($pdfUploadPath)){
                            unlink($pdfUploadPath);
                        }
                        if (!file_exists($pdfDirectoryPath)) {
                            File::makeDirectory(public_path().$pdfDirectoryPath, $mode = 0777, true, true);
                        }
                        file_put_contents($pdfUploadPath,$pdfContent);
                        $mailData = ['path' => $pdfUploadPath, 'toMail' => $vendorInfo['email']];
                        Mail::send('purchase.purchase-request.email.vendor-quotation', [], function($message) use ($mailData){
                            $message->subject('Testing with attachment');
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
}
