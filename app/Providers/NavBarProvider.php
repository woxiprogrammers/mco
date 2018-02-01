<?php

namespace App\Providers;

use App\MaterialRequests;
use App\PurchaseRequest;
use App\UserLastLogin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;

class NavBarProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer('partials.common.navbar', function($view){
            try{
                $loggedInUser = Auth::user();
                $materialRequestCreateCount = $materialRequestDisapprovedCount = 0;
                $purchaseRequestCreateCount = $purchaseRequestDisapprovedCount = 0;

                if(Session::has('global_project_site')){
                    $projectSiteId = Session::get('global_project_site');
                    if(!in_array($loggedInUser->roles[0]->role->slug, ['admin','superadmin'])){
                        if($loggedInUser->customHasPermission('approve-material-request')){
                            $materialRequestCreateCount = MaterialRequests::join('material_request_components','material_requests.id','=','material_request_components.material_request_id')
                                ->join('purchase_request_component_statuses','purchase_request_component_statuses.id','=','material_request_components.component_status_id')
                                ->join('user_project_site_relation','user_project_site_relation.project_site_id','=','material_requests.project_site_id')
                                ->where('user_project_site_relation.user_id',$loggedInUser->id)
                                ->where('purchase_request_component_statuses.slug','pending')
                                ->where('material_requests.project_site_id', $projectSiteId)
                                ->count();
                        }
                        if($loggedInUser->customHasPermission('approve-purchase-request')){
                            $purchaseRequestCreateCount = PurchaseRequest::join('purchase_request_components','purchase_request_components.purchase_request_id','=','purchase_requests.id')
                                ->join('material_request_components','purchase_request_components.material_request_component_id','=','material_request_components.id')
                                ->join('purchase_request_component_statuses','purchase_request_component_statuses.id','=','material_request_components.component_status_id')
                                ->join('user_project_site_relation','user_project_site_relation.project_site_id','=','purchase_requests.project_site_id')
                                ->where('user_project_site_relation.user_id',$loggedInUser->id)
                                ->where('purchase_request_component_statuses.slug','pending')
                                ->where('purchase_requests.project_site_id', $projectSiteId)
                                ->count();
                        }
                        if($loggedInUser->customHasPermission('create-material-request') || $loggedInUser->customHasPermission('approve-material-request')){
                            $lastLogin = UserLastLogin::join('modules','modules.id','=','user_last_logins.module_id')
                                ->where('modules.slug','material-request')
                                ->where('user_last_logins.user_id',$loggedInUser->id)
                                ->pluck('user_last_logins.last_login')
                                ->first();
                            if($lastLogin == null){
                                $materialRequestDisapprovedCount = MaterialRequests::join('material_request_components','material_requests.id','=','material_request_components.material_request_id')
                                    ->join('material_request_component_history_table','material_request_component_history_table.material_request_component_id','=','material_request_components.id')
                                    ->join('purchase_request_component_statuses','purchase_request_component_statuses.id','=','material_request_components.component_status_id')
                                    ->whereIn('purchase_request_component_statuses.slug',['manager-disapproved','admin-disapproved'])
                                    ->where('material_requests.on_behalf_of',$loggedInUser->id)
                                    ->where('material_requests.project_site_id', $projectSiteId)
                                    ->count('material_request_components.id');
                            }else{
                                $materialRequestDisapprovedCount = MaterialRequests::join('material_request_components','material_requests.id','=','material_request_components.material_request_id')
                                    ->join('material_request_component_history_table','material_request_component_history_table.material_request_component_id','=','material_request_components.id')
                                    ->join('purchase_request_component_statuses','purchase_request_component_statuses.id','=','material_request_components.component_status_id')
                                    ->whereIn('purchase_request_component_statuses.slug',['manager-disapproved','admin-disapproved'])
                                    ->where('material_requests.on_behalf_of',$loggedInUser->id)
                                    ->where('material_requests.project_site_id', $projectSiteId)
                                    ->where('material_request_component_history_table.created_at','>=',$lastLogin)
                                    ->count('material_request_component_history_table.id');
                            }
                        }
                        if($loggedInUser->customHasPermission('create-material-request') || $loggedInUser->customHasPermission('approve-material-request') || $loggedInUser->customHasPermission('create-purchase-request') || $loggedInUser->customHasPermission('approve-purchase-request')){
                            $lastLogin = UserLastLogin::join('modules','modules.id','=','user_last_logins.module_id')
                                ->where('modules.slug','purchase-request')
                                ->where('user_last_logins.user_id',$loggedInUser->id)
                                ->pluck('user_last_logins.last_login')
                                ->first();
                            if($lastLogin == null){
                                $purchaseRequestDisapprovedCount = MaterialRequests::join('material_request_components','material_requests.id','=','material_request_components.material_request_id')
                                    ->join('purchase_request_components','purchase_request_components.material_request_component_id','=','material_request_components.id')
                                    ->join('purchase_requests','purchase_requests.id','=','purchase_request_components.purchase_request_id')
                                    ->join('material_request_component_history_table','material_request_component_history_table.material_request_component_id','=','material_request_components.id')
                                    ->join('purchase_request_component_statuses','purchase_request_component_statuses.id','=','material_request_components.component_status_id')
                                    ->whereIn('purchase_request_component_statuses.slug',['manager-disapproved','admin-disapproved'])
                                    ->where(function($query) use ($loggedInUser){
                                        $query->where('material_requests.on_behalf_of',$loggedInUser->id)
                                            ->orWhere('purchase_requests.behalf_of_user_id',$loggedInUser->id);
                                    })
                                    ->where('material_requests.project_site_id', $projectSiteId)
                                    ->count('purchase_request_components.id');
                            }else{
                                $purchaseRequestDisapprovedCount = MaterialRequests::join('material_request_components','material_requests.id','=','material_request_components.material_request_id')
                                    ->join('purchase_request_components','purchase_request_components.material_request_component_id','=','material_request_components.id')
                                    ->join('purchase_requests','purchase_requests.id','=','purchase_request_components.purchase_request_id')
                                    ->join('material_request_component_history_table','material_request_component_history_table.material_request_component_id','=','material_request_components.id')
                                    ->join('purchase_request_component_statuses','purchase_request_component_statuses.id','=','material_request_components.component_status_id')
                                    ->whereIn('purchase_request_component_statuses.slug',['p-r-manager-disapproved','p-r-admin-disapproved'])
                                    ->where(function($query) use ($loggedInUser){
                                        $query->where('material_requests.on_behalf_of',$loggedInUser->id)
                                            ->orWhere('purchase_requests.behalf_of_user_id',$loggedInUser->id);
                                    })
                                    ->where('material_request_component_history_table.created_at','>=',$lastLogin)
                                    ->where('material_requests.project_site_id', $projectSiteId)
                                    ->count();
                            }
                        }
                    }
                    $purchaseRequestNotificationCount = $purchaseRequestDisapprovedCount + $purchaseRequestCreateCount;
                    $materialRequestNotificationCount = $materialRequestDisapprovedCount + $materialRequestCreateCount;
                }
                $view->with(compact('purchaseRequestNotificationCount','materialRequestNotificationCount'));
            }catch(\Exception $e){
                $data = [
                    'action' => 'Nav bar Service Provider',
                    'exception' => $e->getMessage()
                ];
                Log::critical(json_encode($data));
                abort(500);
            }

        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
