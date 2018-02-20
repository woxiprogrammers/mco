<?php

namespace App\Http\Controllers\Admin;

use App\Category;
use App\CategoryMaterialRelation;
use App\Helper\MaterialProductHelper;
use App\MaterialRequests;
use App\Project;
use App\ProjectSite;
use App\PurchaseRequest;
use App\Quotation;
use App\Http\Controllers\Controller;
use App\UserLastLogin;
use ConsoleTVs\Charts\Facades\Charts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

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
            $purchaseRequestCreateCount = $purchaseRequestDisapprovedCount = 0;
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
                }
            }
            $projectSiteData[$iterator]['modules'] = [
                [
                  'name' => 'Purchase',
                  'slug' => 'purchase',
                  'notification_count' => $materialRequestCreateCount + $purchaseRequestCreateCount + $materialRequestDisapprovedCount + $purchaseRequestDisapprovedCount
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
