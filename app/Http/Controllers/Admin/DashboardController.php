<?php

namespace App\Http\Controllers\Admin;

use App\Category;
use App\CategoryMaterialRelation;
use App\Helper\MaterialProductHelper;
use App\InventoryComponentTransfers;
use App\InventoryComponentTransferStatus;
use App\InventoryTransferTypes;
use App\MaterialRequests;
use App\PeticashRequestedSalaryTransaction;
use App\Project;
use App\ProjectSite;
use App\PurchaseOrder;
use App\PurchaseOrderRequest;
use App\PurchaseOrderTransaction;
use App\PurchaseRequest;
use App\PurchaseRequestComponentStatuses;
use App\PurchaseRequestComponentVendorRelation;
use App\Quotation;
use App\Http\Controllers\Controller;
use App\UserLastLogin;
use ConsoleTVs\Charts\Facades\Charts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class DashboardController extends Controller
{
    public function __construct(){
        $this->middleware('custom.auth');
    }

    public function index()
    {
        /*
         * Quotation Status Wise Chart
         */
        $quotationApprovedCount = Quotation::where('quotation_status_id', 2)->count();
        $quotationDraftCount = Quotation::where('quotation_status_id', 1)->count();
        $quotationDisapprovedCount = Quotation::where('quotation_status_id', 3)->count();
        $quotationStatus = Charts::multi('bar', 'material')
            // Setup the chart settings
            ->title("Projects Status")
            // A dimension of 0 means it will take 100% of the space
            ->dimensions(0, 400) // Width x Height
            // This defines a preset of colors already done:)
            ->template("material")
            // You could always set them manually
            // ->colors(['#2196F3', '#F44336', '#FFC107'])
            // Setup the diferent datasets (this is a multi chart)
            ->dataset('Approved', [$quotationApprovedCount])
            ->dataset('Disaproved', [$quotationDisapprovedCount])
            ->dataset('Draft', [$quotationDraftCount])
            // Setup what the values mean
            ->labels(['Projects']);
        /*
         * Category Wise Materials
         */
        $categoryData = Category::orderBy('id','asc')->get(['name','id'])->toArray();
        $categorymatData = CategoryMaterialRelation::get()->toArray();
        $category = array();
        $materialCounts = array();
        $colors = array();
        foreach ($categoryData as $cat) {
            $category[] = $cat['name'];
            $matCount = 0;
            foreach ($categorymatData as $catMat) {
                if($cat['id'] == $catMat['category_id']) {
                    $matCount++;
                }
            }
            $materialCounts[] = $matCount;
            $colors[] = $this->generateRandomString(6);
        }
        $totalCategory = count($category);
        $totalMaterials = count($categorymatData);
        $categorywiseMaterialCount = Charts::create('line', 'highcharts')
            ->title('Categorywise Material Count')
            ->labels($category)
            ->values($materialCounts)
            ->colors($colors)
            ->dimensions(0,400)
            ->type('pie');
        $user = Auth::user();
        if($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin'){
            $allProjectSites = ProjectSite::join('projects','projects.id','=','project_sites.project_id')
                ->where('projects.is_active', true)
                ->select('projects.name as project_name','project_sites.id as project_site_id','project_sites.name as project_site_name')
                ->orderBy('project_site_id','desc')
                ->get();
        }else{
            $allProjectSites = ProjectSite::join('projects','projects.id','=','project_sites.project_id')
                ->join('user_project_site_relation','user_project_site_relation.project_site_id','=','project_sites.id')
                ->where('projects.is_active', true)
                ->where('user_project_site_relation.user_id', $user->id)
                ->select('projects.name as project_name','project_sites.id as project_site_id','project_sites.name as project_site_name')
                ->orderBy('project_site_id','desc')
                ->get();
        }
        $projectSiteData = array();
        $iterator = 0;
        foreach ($allProjectSites as $projectSite){
            $projectSiteData[$iterator] = [
                'project_site_id' => $projectSite['project_site_id'],
                'project_site_name' => $projectSite['project_name'].' - '.$projectSite['project_site_name'],
                'modules' => array()
            ];
            /*Purchase Module Notification counts*/
            $materialRequestCreateCount = $materialRequestDisapprovedCount = 0;
            $purchaseRequestCreateCount = $purchaseRequestDisapprovedCount = $purchaseRequestApprovedCount = 0;
            $purchaseOrderCreatedCount = $purchaseOrderBillCreateCount = 0;
            $purchaseOrderRequestCreateCount = $materialSiteOutTransferCreateCount = 0;
            $materialSiteOutTransferApproveCount = $checklistAssignedCount = 0;
            $checklistAssignedCount = $reviewChecklistCount = 0;
            $peticashSalaryRequestCount = $peticashSalaryApprovedCount = 0;
            $projectSiteId = $projectSite['project_site_id'];
            if(!in_array($user->roles[0]->role->slug, ['admin','superadmin'])){
                if($user->customHasPermission('approve-material-request')){
                    $materialRequestCreateCount = MaterialRequests::join('material_request_components','material_requests.id','=','material_request_components.material_request_id')
                        ->join('purchase_request_component_statuses','purchase_request_component_statuses.id','=','material_request_components.component_status_id')
                        ->join('user_project_site_relation','user_project_site_relation.project_site_id','=','material_requests.project_site_id')
                        ->where('user_project_site_relation.user_id',$user->id)
                        ->where('purchase_request_component_statuses.slug','pending')
                        ->where('material_requests.project_site_id', $projectSite['project_site_id'])
                        ->count();
                }
                if($user->customHasPermission('approve-purchase-request')){
                    $purchaseRequestCreateCount = PurchaseRequest::join('purchase_request_components','purchase_request_components.purchase_request_id','=','purchase_requests.id')
                        ->join('material_request_components','purchase_request_components.material_request_component_id','=','material_request_components.id')
                        ->join('purchase_request_component_statuses','purchase_request_component_statuses.id','=','material_request_components.component_status_id')
                        ->join('user_project_site_relation','user_project_site_relation.project_site_id','=','purchase_requests.project_site_id')
                        ->where('user_project_site_relation.user_id',$user->id)
                        ->where('purchase_request_component_statuses.slug','pending')
                        ->where('purchase_requests.project_site_id', $projectSite['project_site_id'])
                        ->count();
                }
                if($user->customHasPermission('create-material-request') || $user->customHasPermission('approve-material-request')){
                    $lastLogin = UserLastLogin::join('modules','modules.id','=','user_last_logins.module_id')
                        ->where('modules.slug','material-request')
                        ->where('user_last_logins.user_id',$user->id)
                        ->pluck('user_last_logins.last_login')
                        ->first();
                    if($lastLogin == null){
                        $materialRequestDisapprovedCount = MaterialRequests::join('material_request_components','material_requests.id','=','material_request_components.material_request_id')
                            ->join('material_request_component_history_table','material_request_component_history_table.material_request_component_id','=','material_request_components.id')
                            ->join('purchase_request_component_statuses','purchase_request_component_statuses.id','=','material_request_components.component_status_id')
                            ->whereIn('purchase_request_component_statuses.slug',['manager-disapproved','admin-disapproved'])
                            ->where('material_requests.on_behalf_of',$user->id)
                            ->where('material_requests.project_site_id', $projectSite['project_site_id'])
                            ->count('material_request_components.id');
                    }else{
                        $materialRequestDisapprovedCount = MaterialRequests::join('material_request_components','material_requests.id','=','material_request_components.material_request_id')
                            ->join('material_request_component_history_table','material_request_component_history_table.material_request_component_id','=','material_request_components.id')
                            ->join('purchase_request_component_statuses','purchase_request_component_statuses.id','=','material_request_components.component_status_id')
                            ->whereIn('purchase_request_component_statuses.slug',['manager-disapproved','admin-disapproved'])
                            ->where('material_requests.on_behalf_of',$user->id)
                            ->where('material_requests.project_site_id', $projectSite['project_site_id'])
                            ->where('material_request_component_history_table.created_at','>=',$lastLogin)
                            ->count('material_request_component_history_table.id');
                    }
                }
                if($user->customHasPermission('create-vendor-assignment') ){
                    $lastLogin = UserLastLogin::join('modules','modules.id','=','user_last_logins.module_id')
                        ->where('modules.slug','purchase-request')
                        ->where('user_last_logins.user_id',$user->id)
                        ->pluck('user_last_logins.last_login')
                        ->first();
                    if($lastLogin == null){
                        $purchaseRequestIds = PurchaseRequestComponentVendorRelation::join('purchase_request_components','purchase_request_components.id','=','purchase_request_component_vendor_relation.purchase_request_component_id')
                            ->join('purchase_requests','purchase_requests.id','=','purchase_request_components.purchase_request_id')
                            ->where('purchase_requests.project_site_id',$projectSite['project_site_id'])
                            ->distinct('purchase_requests.id')
                            ->pluck('purchase_requests.id');
                        $purchaseRequestApprovedCount = PurchaseRequest::whereIn('purchase_component_status_id',PurchaseRequestComponentStatuses::whereIn('slug',['p-r-manager-approved','p-r-admin-approved'])->pluck('id'))
                            ->whereNotIn('id',$purchaseRequestIds)
                            ->count();
                    }else{
                        $purchaseRequestIds = PurchaseRequestComponentVendorRelation::join('purchase_request_components','purchase_request_components.id','=','purchase_request_component_vendor_relation.purchase_request_component_id')
                            ->join('purchase_requests','purchase_requests.id','=','purchase_request_components.purchase_request_id')
                            ->where('purchase_requests.project_site_id',$projectSite['project_site_id'])
                            ->where('purchase_request_component_vendor_relation.created_at','>=',$lastLogin)
                            ->distinct('purchase_requests.id')
                            ->pluck('purchase_requests.id');
                        $purchaseRequestApprovedCount = PurchaseRequest::whereIn('purchase_component_status_id',PurchaseRequestComponentStatuses::whereIn('slug',['p-r-manager-approved','p-r-admin-approved'])->pluck('id'))
                            ->whereNotIn('id',$purchaseRequestIds)
                            ->count();
                    }
                }
                if($user->customHasPermission('create-material-request') || $user->customHasPermission('approve-material-request') || $user->customHasPermission('create-purchase-request') || $user->customHasPermission('approve-purchase-request')){
                    $lastLogin = UserLastLogin::join('modules','modules.id','=','user_last_logins.module_id')
                        ->where('modules.slug','purchase-request')
                        ->where('user_last_logins.user_id',$user->id)
                        ->pluck('user_last_logins.last_login')
                        ->first();
                    if($lastLogin == null){
                        $purchaseRequestDisapprovedCount = MaterialRequests::join('material_request_components','material_requests.id','=','material_request_components.material_request_id')
                            ->join('purchase_request_components','purchase_request_components.material_request_component_id','=','material_request_components.id')
                            ->join('purchase_requests','purchase_requests.id','=','purchase_request_components.purchase_request_id')
                            ->join('material_request_component_history_table','material_request_component_history_table.material_request_component_id','=','material_request_components.id')
                            ->join('purchase_request_component_statuses','purchase_request_component_statuses.id','=','material_request_components.component_status_id')
                            ->whereIn('purchase_request_component_statuses.slug',['manager-disapproved','admin-disapproved'])
                            ->where(function($query) use ($user){
                                $query->where('material_requests.on_behalf_of',$user->id)
                                    ->orWhere('purchase_requests.behalf_of_user_id',$user->id);
                            })
                            ->where('material_requests.project_site_id', $projectSite['project_site_id'])
                            ->count('purchase_request_components.id');
                    }else{
                        $purchaseRequestDisapprovedCount = MaterialRequests::join('material_request_components','material_requests.id','=','material_request_components.material_request_id')
                            ->join('purchase_request_components','purchase_request_components.material_request_component_id','=','material_request_components.id')
                            ->join('purchase_requests','purchase_requests.id','=','purchase_request_components.purchase_request_id')
                            ->join('material_request_component_history_table','material_request_component_history_table.material_request_component_id','=','material_request_components.id')
                            ->join('purchase_request_component_statuses','purchase_request_component_statuses.id','=','material_request_components.component_status_id')
                            ->whereIn('purchase_request_component_statuses.slug',['p-r-manager-disapproved','p-r-admin-disapproved'])
                            ->where(function($query) use ($user){
                                $query->where('material_requests.on_behalf_of',$user->id)
                                    ->orWhere('purchase_requests.behalf_of_user_id',$user->id);
                            })
                            ->where('material_request_component_history_table.created_at','>=',$lastLogin)
                            ->where('material_requests.project_site_id', $projectSite['project_site_id'])
                            ->count();
                    }
                }
            }else{
                if($user->customHasPermission('approve-material-request')){
                    $materialRequestCreateCount = MaterialRequests::join('material_request_components','material_requests.id','=','material_request_components.material_request_id')
                        ->join('purchase_request_component_statuses','purchase_request_component_statuses.id','=','material_request_components.component_status_id')
                        ->where('purchase_request_component_statuses.slug','pending')
                        ->where('material_requests.project_site_id', $projectSite['project_site_id'])
                        ->count();
                }
                if($user->customHasPermission('approve-purchase-request')){
                    $purchaseRequestCreateCount = PurchaseRequest::join('purchase_request_components','purchase_request_components.purchase_request_id','=','purchase_requests.id')
                        ->join('material_request_components','purchase_request_components.material_request_component_id','=','material_request_components.id')
                        ->join('purchase_request_component_statuses','purchase_request_component_statuses.id','=','material_request_components.component_status_id')
                        ->where('purchase_request_component_statuses.slug','pending')
                        ->where('purchase_requests.project_site_id', $projectSite['project_site_id'])
                        ->count();
                }
                if($user->customHasPermission('create-material-request') || $user->customHasPermission('approve-material-request')){
                    $lastLogin = UserLastLogin::join('modules','modules.id','=','user_last_logins.module_id')
                        ->where('modules.slug','material-request')
                        ->where('user_last_logins.user_id',$user->id)
                        ->pluck('user_last_logins.last_login')
                        ->first();
                    if($lastLogin == null){
                        $materialRequestDisapprovedCount = MaterialRequests::join('material_request_components','material_requests.id','=','material_request_components.material_request_id')
                            ->join('material_request_component_history_table','material_request_component_history_table.material_request_component_id','=','material_request_components.id')
                            ->join('purchase_request_component_statuses','purchase_request_component_statuses.id','=','material_request_components.component_status_id')
                            ->whereIn('purchase_request_component_statuses.slug',['manager-disapproved','admin-disapproved'])
                            ->where('material_requests.project_site_id', $projectSite['project_site_id'])
                            ->count('material_request_components.id');
                    }else{
                        $materialRequestDisapprovedCount = MaterialRequests::join('material_request_components','material_requests.id','=','material_request_components.material_request_id')
                            ->join('material_request_component_history_table','material_request_component_history_table.material_request_component_id','=','material_request_components.id')
                            ->join('purchase_request_component_statuses','purchase_request_component_statuses.id','=','material_request_components.component_status_id')
                            ->whereIn('purchase_request_component_statuses.slug',['manager-disapproved','admin-disapproved'])
                            ->where('material_requests.project_site_id', $projectSite['project_site_id'])
                            ->where('material_request_component_history_table.created_at','>=',$lastLogin)
                            ->count('material_request_component_history_table.id');
                    }
                }
                if($user->customHasPermission('create-material-request') || $user->customHasPermission('approve-material-request') || $user->customHasPermission('create-purchase-request') || $user->customHasPermission('approve-purchase-request')){
                    $lastLogin = UserLastLogin::join('modules','modules.id','=','user_last_logins.module_id')
                        ->where('modules.slug','purchase-request')
                        ->where('user_last_logins.user_id',$user->id)
                        ->pluck('user_last_logins.last_login')
                        ->first();
                    if($lastLogin == null){
                        $purchaseRequestDisapprovedCount = MaterialRequests::join('material_request_components','material_requests.id','=','material_request_components.material_request_id')
                            ->join('purchase_request_components','purchase_request_components.material_request_component_id','=','material_request_components.id')
                            ->join('purchase_requests','purchase_requests.id','=','purchase_request_components.purchase_request_id')
                            ->join('material_request_component_history_table','material_request_component_history_table.material_request_component_id','=','material_request_components.id')
                            ->join('purchase_request_component_statuses','purchase_request_component_statuses.id','=','material_request_components.component_status_id')
                            ->whereIn('purchase_request_component_statuses.slug',['manager-disapproved','admin-disapproved'])
                            ->where('material_requests.project_site_id', $projectSite['project_site_id'])
                            ->count('purchase_request_components.id');
                    }else{
                        $purchaseRequestDisapprovedCount = MaterialRequests::join('material_request_components','material_requests.id','=','material_request_components.material_request_id')
                            ->join('purchase_request_components','purchase_request_components.material_request_component_id','=','material_request_components.id')
                            ->join('purchase_requests','purchase_requests.id','=','purchase_request_components.purchase_request_id')
                            ->join('material_request_component_history_table','material_request_component_history_table.material_request_component_id','=','material_request_components.id')
                            ->join('purchase_request_component_statuses','purchase_request_component_statuses.id','=','material_request_components.component_status_id')
                            ->whereIn('purchase_request_component_statuses.slug',['p-r-manager-disapproved','p-r-admin-disapproved'])
                            ->where('material_request_component_history_table.created_at','>=',$lastLogin)
                            ->where('material_requests.project_site_id', $projectSite['project_site_id'])
                            ->count();
                    }
                    $lastLoginForPO = UserLastLogin::join('modules','modules.id','=','user_last_logins.module_id')
                        ->whereIn('modules.slug',['material-request','purchase-request'])
                        ->where('user_last_logins.user_id',$user->id)
                        ->orderBy('user_last_logins.updated_at','desc')
                        ->pluck('user_last_logins.last_login')
                        ->first();
                    if($lastLoginForPO == null){
                        $purchaseOrderCreatedCount = PurchaseOrder::join('purchase_requests','purchase_requests.id','=','purchase_orders.purchase_request_id')
                            ->join('purchase_request_components','purchase_request_components.purchase_request_id','=','purchase_requests.id')
                            ->join('material_request_components','material_request_components.id','=','purchase_request_components.material_request_component_id')
                            ->join('material_requests','material_requests.id','=','material_request_components.material_request_id')
                            ->where(function($query) use ($user){
                                $query->where('material_requests.on_behalf_of', $user->id)
                                    ->orWhere('purchase_requests.behalf_of_user_id', $user->id);
                            })
                            ->where('material_requests.project_site_id', $projectSiteId)
                            ->count('purchase_orders.id');
                        $purchaseOrderBillCreateCount = PurchaseOrderTransaction::join('purchase_orders','purchase_orders.id','=','purchase_order_transactions.purchase_order_id')
                            ->join('purchase_requests','purchase_requests.id','=','purchase_orders.purchase_request_id')
                            ->join('purchase_request_components','purchase_request_components.purchase_request_id','=','purchase_requests.id')
                            ->join('material_request_components','material_request_components.id','=','purchase_request_components.material_request_component_id')
                            ->join('material_requests','material_requests.id','=','material_request_components.material_request_id')
                            ->join('material_request_component_history_table','material_request_component_history_table.material_request_component_id','=','material_request_components.id')
                            ->join('purchase_request_component_statuses','purchase_request_component_statuses.id','=','material_request_component_history_table.component_status_id')
                            ->where(function($query) use ($user){
                                $query->where('material_requests.on_behalf_of', $user->id)
                                    ->orWhere(function($innerQuery) use ($user){
                                        $innerQuery->whereIn('purchase_request_component_statuses.slug',['p-r-manager-approved','p-r-admin-approved'])
                                            ->where('material_request_component_history_table.user_id', $user->id);
                                    });
                            })
                            ->where('material_requests.project_site_id', $projectSiteId)
                            ->count('purchase_order_transactions.id');
                    }else{
                        $purchaseOrderCreatedCount = PurchaseOrder::join('purchase_requests','purchase_requests.id','=','purchase_orders.purchase_request_id')
                            ->join('purchase_request_components','purchase_request_components.purchase_request_id','=','purchase_requests.id')
                            ->join('material_request_components','material_request_components.id','=','purchase_request_components.material_request_component_id')
                            ->join('material_requests','material_requests.id','=','material_request_components.material_request_id')
                            ->where(function($query) use ($user){
                                $query->where('material_requests.on_behalf_of', $user->id)
                                    ->orWhere('purchase_requests.behalf_of_user_id', $user->id);
                            })
                            ->where('material_requests.project_site_id', $projectSiteId)
                            ->where('purchase_orders.created_at','>=',$lastLoginForPO)
                            ->count('purchase_orders.id');

                        $purchaseOrderBillCreateCount = PurchaseOrderTransaction::join('purchase_orders','purchase_orders.id','=','purchase_order_transactions.purchase_order_id')
                            ->join('purchase_requests','purchase_requests.id','=','purchase_orders.purchase_request_id')
                            ->join('purchase_request_components','purchase_request_components.purchase_request_id','=','purchase_requests.id')
                            ->join('material_request_components','material_request_components.id','=','purchase_request_components.material_request_component_id')
                            ->join('material_requests','material_requests.id','=','material_request_components.material_request_id')
                            ->join('material_request_component_history_table','material_request_component_history_table.material_request_component_id','=','material_request_components.id')
                            ->join('purchase_request_component_statuses','purchase_request_component_statuses.id','=','material_request_component_history_table.component_status_id')
                            ->where(function($query) use ($user){
                                $query->where('material_requests.on_behalf_of', $user->id)
                                    ->orWhere(function($innerQuery) use ($user){
                                        $innerQuery->whereIn('purchase_request_component_statuses.slug',['p-r-manager-approved','p-r-admin-approved'])
                                            ->where('material_request_component_history_table.user_id', $user->id);
                                    });
                            })
                            ->where('material_requests.project_site_id', $projectSiteId)
                            ->where('purchase_order_transactions.created_at', '>=',$lastLoginForPO)
                            ->count('purchase_order_transactions.id');
                    }
                }
                if($user->customHasPermission('create-vendor-assignment') ){
                    $lastLogin = UserLastLogin::join('modules','modules.id','=','user_last_logins.module_id')
                        ->where('modules.slug','purchase-request')
                        ->where('user_last_logins.user_id',$user->id)
                        ->pluck('user_last_logins.last_login')
                        ->first();
                    if($lastLogin == null){
                        $purchaseRequestIds = PurchaseRequestComponentVendorRelation::join('purchase_request_components','purchase_request_components.id','=','purchase_request_component_vendor_relation.purchase_request_component_id')
                                                            ->join('purchase_requests','purchase_requests.id','=','purchase_request_components.purchase_request_id')
                                                            ->where('purchase_requests.project_site_id',$projectSite['project_site_id'])
                                                            ->distinct('purchase_requests.id')
                                                            ->pluck('purchase_requests.id');
                        $purchaseRequestApprovedCount = PurchaseRequest::whereIn('purchase_component_status_id',PurchaseRequestComponentStatuses::whereIn('slug',['p-r-manager-approved','p-r-admin-approved'])->pluck('id'))
                                                            ->whereNotIn('id',$purchaseRequestIds)
                                                            ->count();
                    }else{
                        $purchaseRequestIds = PurchaseRequestComponentVendorRelation::join('purchase_request_components','purchase_request_components.id','=','purchase_request_component_vendor_relation.purchase_request_component_id')
                            ->join('purchase_requests','purchase_requests.id','=','purchase_request_components.purchase_request_id')
                            ->where('purchase_requests.project_site_id',$projectSite['project_site_id'])
                            ->where('purchase_request_component_vendor_relation.created_at','>=',$lastLogin)
                            ->distinct('purchase_requests.id')
                            ->pluck('purchase_requests.id');
                        $purchaseRequestApprovedCount = PurchaseRequest::whereIn('purchase_component_status_id',PurchaseRequestComponentStatuses::whereIn('slug',['p-r-manager-approved','p-r-admin-approved'])->pluck('id'))
                            ->whereNotIn('id',$purchaseRequestIds)
                            ->count();
                    }
                }
                if($user->customHasPermission('approve-purchase-order-request')){
                    $lastLogin = UserLastLogin::join('modules','modules.id','=','user_last_logins.module_id')
                        ->where('modules.slug','purchase-order-request')
                        ->where('user_last_logins.user_id',$user->id)
                        ->pluck('user_last_logins.last_login')
                        ->first();
                    if($lastLogin == null){
                        $purchaseOrderRequestCreateCount = PurchaseOrderRequest::join('purchase_requests','purchase_requests.id','=','purchase_order_requests.purchase_request_id')
                            ->join('user_project_site_relation','user_project_site_relation.project_site_id','=','purchase_requests.project_site_id')
                            ->join('user_has_permissions','user_has_permissions.user_id','=','user_project_site_relation.user_id')
                            ->join('permissions','permissions.id','=','user_has_permissions.permission_id')
                            ->where('permissions.name','approve-purchase-order-request')
                            ->where('user_project_site_relation.user_id', $user->id)
                            ->where('purchase_requests.project_site_id', $projectSiteId)
                            ->count('purchase_order_requests.id');
                    }else{
                        $purchaseOrderRequestCreateCount = PurchaseOrderRequest::join('purchase_requests','purchase_requests.id','=','purchase_order_requests.purchase_request_id')
                            ->join('user_project_site_relation','user_project_site_relation.project_site_id','=','purchase_requests.project_site_id')
                            ->join('user_has_permissions','user_has_permissions.user_id','=','user_project_site_relation.user_id')
                            ->join('permissions','permissions.id','=','user_has_permissions.permission_id')
                            ->where('permissions.name','approve-purchase-order-request')
                            ->where('user_project_site_relation.user_id', $user->id)
                            ->where('purchase_requests.project_site_id', $projectSiteId)
                            ->where('purchase_order_requests.created_at','>=', $lastLogin)
                            ->count('purchase_order_requests.id');
                    }
                }
                if($user->customHasPermission('approve-component-transfer')){
                    $lastLogin = UserLastLogin::join('modules','modules.id','=','user_last_logins.module_id')
                        ->where('modules.slug','component-transfer')
                        ->where('user_last_logins.user_id',$user->id)
                        ->pluck('user_last_logins.last_login')
                        ->first();
                    $siteOutTransferTypeId = InventoryTransferTypes::where('slug','site')->where('type','ilike','out')->pluck('id')->first();
                    $inventoryRequestedStatusId = InventoryComponentTransferStatus::where('slug','requested')->pluck('id')->first();
                    if($lastLogin == null){
                        $materialSiteOutTransferCreateCount = InventoryComponentTransfers::join('inventory_components','inventory_components.id','=','inventory_component_transfers.inventory_component_id')
                            ->join('user_project_site_relation','user_project_site_relation.project_site_id','=','inventory_components.project_site_id')
                            ->where('user_project_site_relation.user_id', $user->id)
                            ->where('inventory_components.project_site_id', $projectSiteId)
                            ->where('inventory_component_transfers.transfer_type_id', $siteOutTransferTypeId)
                            ->where('inventory_component_transfers.inventory_component_transfer_status_id', $inventoryRequestedStatusId)
                            ->count('inventory_component_transfers.id');
                    }else{
                        $materialSiteOutTransferCreateCount = InventoryComponentTransfers::join('inventory_components','inventory_components.id','=','inventory_component_transfers.inventory_component_id')
                            ->join('user_project_site_relation','user_project_site_relation.project_site_id','=','inventory_components.project_site_id')
                            ->where('user_project_site_relation.user_id', $user->id)
                            ->where('inventory_components.project_site_id', $projectSiteId)
                            ->where('inventory_component_transfers.transfer_type_id', $siteOutTransferTypeId)
                            ->where('inventory_component_transfers.inventory_component_transfer_status_id', $inventoryRequestedStatusId)
                            ->where('inventory_component_transfers.created_at','>=', $lastLogin)
                            ->count('inventory_component_transfers.id');
                    }
                }
                if($user->customHasPermission('approve-component-transfer') || $user->customHasPermission('approve-component-transfer')){
                    $lastLogin = UserLastLogin::join('modules','modules.id','=','user_last_logins.module_id')
                        ->where('modules.slug','component-transfer')
                        ->where('user_last_logins.user_id', $user->id)
                        ->pluck('user_last_logins.last_login')
                        ->first();
                    $siteOutTransferTypeId = InventoryTransferTypes::where('slug','site')->where('type','ilike','out')->pluck('id')->first();
                    $inventoryApprovedStatusId = InventoryComponentTransferStatus::where('slug','approved')->pluck('id')->first();
                    if($lastLogin == null){
                        $materialSiteOutTransferApproveCount = InventoryComponentTransfers::join('inventory_components','inventory_components.id','=','inventory_component_transfers.inventory_component_id')
                            ->join('user_project_site_relation','user_project_site_relation.project_site_id','=','inventory_components.project_site_id')
                            ->where('user_project_site_relation.user_id', $user->id)
                            ->where('inventory_components.project_site_id', $projectSiteId)
                            ->where('inventory_component_transfers.transfer_type_id', $siteOutTransferTypeId)
                            ->where('inventory_component_transfers.inventory_component_transfer_status_id', $inventoryApprovedStatusId)
                            ->where('inventory_component_transfers.user_id', $user->id)
                            ->count('inventory_component_transfers.id');
                    }else{
                        $materialSiteOutTransferApproveCount = InventoryComponentTransfers::join('inventory_components','inventory_components.id','=','inventory_component_transfers.inventory_component_id')
                            ->join('user_project_site_relation','user_project_site_relation.project_site_id','=','inventory_components.project_site_id')
                            ->where('user_project_site_relation.user_id', $user->id)
                            ->where('inventory_components.project_site_id', $projectSiteId)
                            ->where('inventory_component_transfers.transfer_type_id', $siteOutTransferTypeId)
                            ->where('inventory_component_transfers.inventory_component_transfer_status_id', $inventoryApprovedStatusId)
                            ->where('inventory_component_transfers.updated_at','>=', $lastLogin)
                            ->where('inventory_component_transfers.user_id', $user->id)
                            ->count('inventory_component_transfers.id');
                    }
                }
                /*if($user->customHasPermission('create-checklist-management')){
                    $checklistAssignedCount = ProjectSiteUserChecklistAssignment::join('checklist_statuses','checklist_statuses.id','=','project_site_user_checklist_assignments.checklist_status_id')
                        ->join('project_site_checklists','project_site_checklists.id','=','project_site_user_checklist_assignments.project_site_checklist_id')
                        ->whereIn('checklist_statuses.slug',['assigned','in-progress'])
                        ->where('project_site_user_checklist_assignments.assigned_to', $user->id)
                        ->where('project_site_checklists.project_site_id', $projectSiteId)
                        ->count('project_site_user_checklist_assignments.id');
                }
                if($user->customHasPermission('create-checklist-recheck') || $user->customHasPermission('view-checklist-recheck')){
                    $reviewChecklistCount = ProjectSiteUserChecklistAssignment::join('project_site_checklists','project_site_user_checklist_assignments.project_site_checklist_id','=','project_site_checklists.id')
                        ->join('checklist_statuses','checklist_statuses.id','=','project_site_user_checklist_assignments.checklist_status_id')
                        ->where('checklist_statuses.slug','review')
                        ->where('project_site_checklists.project_site_id', $projectSiteId)
                        ->count('project_site_user_checklist_assignments.id');
                }*/
                if($user->customHasPermission('approve-peticash-management')){
                    $peticashSalaryRequestCount = PeticashRequestedSalaryTransaction::join('peticash_statuses','peticash_statuses.id','=','peticash_requested_salary_transactions.peticash_status_id')
                        ->where('peticash_statuses.slug','pending')
                        ->where('peticash_requested_salary_transactions.project_site_id', $projectSiteId)
                        ->count('peticash_requested_salary_transactions.id');
                }
                if($user->customHasPermission('approve-peticash-management') || $user->customHasPermission('create-peticash-management')){
                    $lastLogin = UserLastLogin::join('modules','modules.id','=','user_last_logins.module_id')
                        ->where('modules.slug','peticash-management')
                        ->where('user_last_logins.user_id', $user->id)
                        ->pluck('user_last_logins.last_login')
                        ->first();
                    if($lastLogin == null){
                        $peticashSalaryApprovedCount = PeticashRequestedSalaryTransaction::join('peticash_statuses','peticash_statuses.id','=','peticash_requested_salary_transactions.peticash_status_id')
                            ->where('peticash_statuses.slug','approved')
                            ->where('peticash_requested_salary_transactions.project_site_id', $projectSiteId)
                            ->where('peticash_requested_salary_transactions.reference_user_id', $user->id)
                            ->count();
                    }else{
                        $peticashSalaryApprovedCount = PeticashRequestedSalaryTransaction::join('peticash_statuses','peticash_statuses.id','=','peticash_requested_salary_transactions.peticash_status_id')
                            ->where('peticash_statuses.slug','approved')
                            ->where('peticash_requested_salary_transactions.project_site_id', $projectSiteId)
                            ->where('peticash_requested_salary_transactions.reference_user_id', $user->id)
                            ->where('peticash_requested_salary_transactions.updated_at','>=', $lastLogin)
                            ->count();
                    }
                }
            }
            $projectSiteData[$iterator]['modules'] = [
                [
                  'name' => 'Purchase',
                  'slug' => 'purchase',
                  'notification_count' => $materialRequestCreateCount + $purchaseRequestCreateCount + $materialRequestDisapprovedCount + $purchaseRequestDisapprovedCount + $purchaseRequestApprovedCount + $purchaseOrderRequestCreateCount + $purchaseOrderCreatedCount + $purchaseOrderBillCreateCount
                ],
                [
                    'name' => 'Inventory',
                    'slug' => 'inventory',
                    'notification_count' => $materialSiteOutTransferCreateCount + $materialSiteOutTransferApproveCount
                ],
                /*[
                    'name' => 'Checklist',
                    'slug' => 'checklist',
                    'notification_count' => $checklistAssignedCount + $reviewChecklistCount
                ],*/
                [
                    'name' => 'Peticash',
                    'slug' => 'peticash',
                    'notification_count' => $peticashSalaryRequestCount + $peticashSalaryApprovedCount
                ]
            ];
            $iterator++;
        }
        return view('admin.dashboard', [
                'quotationStatus' => $quotationStatus,
                'categorywiseMaterialCount' => $categorywiseMaterialCount,
                'totalCategory' => $totalCategory,
                'totalMaterials' => $totalMaterials,
                'projectSiteData' => $projectSiteData
            ]);
    }

    function generateRandomString($length = 6) {
        return "#".substr(str_shuffle(str_repeat($x='0123456789abcdefABCDEF', ceil($length/strlen($x)) )),1,$length);
    }
}
