<?php

namespace App\Providers;

use App\InventoryComponentTransfers;
use App\InventoryComponentTransferStatus;
use App\InventoryTransferTypes;
use App\MaterialRequests;
use App\PeticashRequestedSalaryTransaction;
use App\ProjectSiteUserChecklistAssignment;
use App\PurchaseOrder;
use App\PurchaseOrderRequest;
use App\PurchaseOrderTransaction;
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
                $purchaseOrderCreatedCount = $purchaseOrderBillCreateCount = 0;
                $purchaseOrderRequestCreateCount = $materialSiteOutTransferCreateCount = 0;
                $materialSiteOutTransferApproveCount = $checklistAssignedCount = 0;
                $checklistAssignedCount = $reviewChecklistCount = 0;
                $peticashSalaryRequestCount = $peticashSalaryApprovedCount = 0;
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
                            $lastLoginForPO = UserLastLogin::join('modules','modules.id','=','user_last_logins.module_id')
                                ->whereIn('modules.slug',['material-request','purchase-request'])
                                ->where('user_last_logins.user_id',$loggedInUser->id)
                                ->orderBy('user_last_logins.updated_at','desc')
                                ->pluck('user_last_logins.last_login')
                                ->first();
                            if($lastLoginForPO == null){
                                $purchaseOrderCreatedCount = PurchaseOrder::join('purchase_requests','purchase_requests.id','=','purchase_orders.purchase_request_id')
                                    ->join('purchase_request_components','purchase_request_components.purchase_request_id','=','purchase_requests.id')
                                    ->join('material_request_components','material_request_components.id','=','purchase_request_components.material_request_component_id')
                                    ->join('material_requests','material_requests.id','=','material_request_components.material_request_id')
                                    ->where(function($query) use ($loggedInUser){
                                        $query->where('material_requests.on_behalf_of', $loggedInUser->id)
                                            ->orWhere('purchase_requests.behalf_of_user_id', $loggedInUser->id);
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
                                    ->where(function($query) use ($loggedInUser){
                                        $query->where('material_requests.on_behalf_of', $loggedInUser->id)
                                            ->orWhere(function($innerQuery) use ($loggedInUser){
                                                $innerQuery->whereIn('purchase_request_component_statuses.slug',['p-r-manager-approved','p-r-admin-approved'])
                                                    ->where('material_request_component_history_table.user_id', $loggedInUser->id);
                                            });
                                    })
                                    ->where('material_requests.project_site_id', $projectSiteId)
                                    ->count('purchase_order_transactions.id');
                            }else{
                                $purchaseOrderCreatedCount = PurchaseOrder::join('purchase_requests','purchase_requests.id','=','purchase_orders.purchase_request_id')
                                    ->join('purchase_request_components','purchase_request_components.purchase_request_id','=','purchase_requests.id')
                                    ->join('material_request_components','material_request_components.id','=','purchase_request_components.material_request_component_id')
                                    ->join('material_requests','material_requests.id','=','material_request_components.material_request_id')
                                    ->where(function($query) use ($loggedInUser){
                                        $query->where('material_requests.on_behalf_of', $loggedInUser->id)
                                            ->orWhere('purchase_requests.behalf_of_user_id', $loggedInUser->id);
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
                                    ->where(function($query) use ($loggedInUser){
                                        $query->where('material_requests.on_behalf_of', $loggedInUser->id)
                                            ->orWhere(function($innerQuery) use ($loggedInUser){
                                                $innerQuery->whereIn('purchase_request_component_statuses.slug',['p-r-manager-approved','p-r-admin-approved'])
                                                    ->where('material_request_component_history_table.user_id', $loggedInUser->id);
                                            });
                                    })
                                    ->where('material_requests.project_site_id', $projectSiteId)
                                    ->where('purchase_order_transactions.created_at', '>=',$lastLoginForPO)
                                    ->count('purchase_order_transactions.id');
                            }
                        }
                        if($loggedInUser->customHasPermission('approve-purchase-order-request')){
                            $lastLogin = UserLastLogin::join('modules','modules.id','=','user_last_logins.module_id')
                                ->where('modules.slug','purchase-order-request')
                                ->where('user_last_logins.user_id',$loggedInUser->id)
                                ->pluck('user_last_logins.last_login')
                                ->first();
                            if($lastLogin == null){
                                $purchaseOrderRequestCreateCount = PurchaseOrderRequest::join('purchase_requests','purchase_requests.id','=','purchase_order_requests.purchase_request_id')
                                    ->join('user_project_site_relation','user_project_site_relation.project_site_id','=','purchase_requests.project_site_id')
                                    ->join('user_has_permissions','user_has_permissions.user_id','=','user_project_site_relation.user_id')
                                    ->join('permissions','permissions.id','=','user_has_permissions.permission_id')
                                    ->where('permissions.name','approve-purchase-order-request')
                                    ->where('user_project_site_relation.user_id', $loggedInUser->id)
                                    ->where('purchase_requests.project_site_id', $projectSiteId)
                                    ->count('purchase_order_requests.id');
                            }else{
                                $purchaseOrderRequestCreateCount = PurchaseOrderRequest::join('purchase_requests','purchase_requests.id','=','purchase_order_requests.purchase_request_id')
                                    ->join('user_project_site_relation','user_project_site_relation.project_site_id','=','purchase_requests.project_site_id')
                                    ->join('user_has_permissions','user_has_permissions.user_id','=','user_project_site_relation.user_id')
                                    ->join('permissions','permissions.id','=','user_has_permissions.permission_id')
                                    ->where('permissions.name','approve-purchase-order-request')
                                    ->where('user_project_site_relation.user_id', $loggedInUser->id)
                                    ->where('purchase_requests.project_site_id', $projectSiteId)
                                    ->where('purchase_order_requests.created_at','>=', $lastLogin)
                                    ->count('purchase_order_requests.id');
                            }
                        }
                        if($loggedInUser->customHasPermission('approve-component-transfer')){
                            $lastLogin = UserLastLogin::join('modules','modules.id','=','user_last_logins.module_id')
                                ->where('modules.slug','component-transfer')
                                ->where('user_last_logins.user_id',$loggedInUser->id)
                                ->pluck('user_last_logins.last_login')
                                ->first();
                            $siteOutTransferTypeId = InventoryTransferTypes::where('slug','site')->where('type','ilike','out')->pluck('id')->first();
                            $inventoryRequestedStatusId = InventoryComponentTransferStatus::where('slug','requested')->pluck('id')->first();
                            if($lastLogin == null){
                                $materialSiteOutTransferCreateCount = InventoryComponentTransfers::join('inventory_components','inventory_components.id','=','inventory_component_transfers.inventory_component_id')
                                    ->join('user_project_site_relation','user_project_site_relation.project_site_id','=','inventory_components.project_site_id')
                                    ->where('user_project_site_relation.user_id', $loggedInUser->id)
                                    ->where('inventory_components.project_site_id', $projectSiteId)
                                    ->where('inventory_component_transfers.transfer_type_id', $siteOutTransferTypeId)
                                    ->where('inventory_component_transfers.inventory_component_transfer_status_id', $inventoryRequestedStatusId)
                                    ->count('inventory_component_transfers.id');
                            }else{
                                $materialSiteOutTransferCreateCount = InventoryComponentTransfers::join('inventory_components','inventory_components.id','=','inventory_component_transfers.inventory_component_id')
                                    ->join('user_project_site_relation','user_project_site_relation.project_site_id','=','inventory_components.project_site_id')
                                    ->where('user_project_site_relation.user_id', $loggedInUser->id)
                                    ->where('inventory_components.project_site_id', $projectSiteId)
                                    ->where('inventory_component_transfers.transfer_type_id', $siteOutTransferTypeId)
                                    ->where('inventory_component_transfers.inventory_component_transfer_status_id', $inventoryRequestedStatusId)
                                    ->where('inventory_component_transfers.created_at','>=', $lastLogin)
                                    ->count('inventory_component_transfers.id');
                            }
                        }
                        if($loggedInUser->customHasPermission('approve-component-transfer') || $loggedInUser->customHasPermission('approve-component-transfer')){
                            $lastLogin = UserLastLogin::join('modules','modules.id','=','user_last_logins.module_id')
                                ->where('modules.slug','component-transfer')
                                ->where('user_last_logins.user_id', $loggedInUser->id)
                                ->pluck('user_last_logins.last_login')
                                ->first();
                            $siteOutTransferTypeId = InventoryTransferTypes::where('slug','site')->where('type','ilike','out')->pluck('id')->first();
                            $inventoryApprovedStatusId = InventoryComponentTransferStatus::where('slug','approved')->pluck('id')->first();
                            if($lastLogin == null){
                                $materialSiteOutTransferApproveCount = InventoryComponentTransfers::join('inventory_components','inventory_components.id','=','inventory_component_transfers.inventory_component_id')
                                    ->join('user_project_site_relation','user_project_site_relation.project_site_id','=','inventory_components.project_site_id')
                                    ->where('user_project_site_relation.user_id', $loggedInUser->id)
                                    ->where('inventory_components.project_site_id', $projectSiteId)
                                    ->where('inventory_component_transfers.transfer_type_id', $siteOutTransferTypeId)
                                    ->where('inventory_component_transfers.inventory_component_transfer_status_id', $inventoryApprovedStatusId)
                                    ->where('inventory_component_transfers.user_id', $loggedInUser->id)
                                    ->count('inventory_component_transfers.id');
                            }else{
                                $materialSiteOutTransferApproveCount = InventoryComponentTransfers::join('inventory_components','inventory_components.id','=','inventory_component_transfers.inventory_component_id')
                                    ->join('user_project_site_relation','user_project_site_relation.project_site_id','=','inventory_components.project_site_id')
                                    ->where('user_project_site_relation.user_id', $loggedInUser->id)
                                    ->where('inventory_components.project_site_id', $projectSiteId)
                                    ->where('inventory_component_transfers.transfer_type_id', $siteOutTransferTypeId)
                                    ->where('inventory_component_transfers.inventory_component_transfer_status_id', $inventoryApprovedStatusId)
                                    ->where('inventory_component_transfers.updated_at','>=', $lastLogin)
                                    ->where('inventory_component_transfers.user_id', $loggedInUser->id)
                                    ->count('inventory_component_transfers.id');
                            }
                        }
                        /*if($loggedInUser->customHasPermission('create-checklist-management')){
                            $checklistAssignedCount = ProjectSiteUserChecklistAssignment::join('checklist_statuses','checklist_statuses.id','=','project_site_user_checklist_assignments.checklist_status_id')
                                ->join('project_site_checklists','project_site_checklists.id','=','project_site_user_checklist_assignments.project_site_checklist_id')
                                ->whereIn('checklist_statuses.slug',['assigned','in-progress'])
                                ->where('project_site_user_checklist_assignments.assigned_to', $loggedInUser->id)
                                ->where('project_site_checklists.project_site_id', $projectSiteId)
                                ->count('project_site_user_checklist_assignments.id');
                        }
                        if($loggedInUser->customHasPermission('create-checklist-recheck') || $loggedInUser->customHasPermission('view-checklist-recheck')){
                            $reviewChecklistCount = ProjectSiteUserChecklistAssignment::join('project_site_checklists','project_site_user_checklist_assignments.project_site_checklist_id','=','project_site_checklists.id')
                                ->join('checklist_statuses','checklist_statuses.id','=','project_site_user_checklist_assignments.checklist_status_id')
                                ->where('checklist_statuses.slug','review')
                                ->where('project_site_checklists.project_site_id', $projectSiteId)
                                ->count('project_site_user_checklist_assignments.id');
                        }*/
                        if($loggedInUser->customHasPermission('approve-peticash-management')){
                            $peticashSalaryRequestCount = PeticashRequestedSalaryTransaction::join('peticash_statuses','peticash_statuses.id','=','peticash_requested_salary_transactions.peticash_status_id')
                                ->where('peticash_statuses.slug','pending')
                                ->where('peticash_requested_salary_transactions.project_site_id', $projectSiteId)
                                ->count('peticash_requested_salary_transactions.id');
                        }
                        if($loggedInUser->customHasPermission('approve-peticash-management') || $loggedInUser->customHasPermission('create-peticash-management')){
                            $lastLogin = UserLastLogin::join('modules','modules.id','=','user_last_logins.module_id')
                                ->where('modules.slug','peticash-management')
                                ->where('user_last_logins.user_id', $loggedInUser->id)
                                ->pluck('user_last_logins.last_login')
                                ->first();
                            if($lastLogin == null){
                                $peticashSalaryApprovedCount = PeticashRequestedSalaryTransaction::join('peticash_statuses','peticash_statuses.id','=','peticash_requested_salary_transactions.peticash_status_id')
                                    ->where('peticash_statuses.slug','approved')
                                    ->where('peticash_requested_salary_transactions.project_site_id', $projectSiteId)
                                    ->where('peticash_requested_salary_transactions.reference_user_id', $loggedInUser->id)
                                    ->count();
                            }else{
                                $peticashSalaryApprovedCount = PeticashRequestedSalaryTransaction::join('peticash_statuses','peticash_statuses.id','=','peticash_requested_salary_transactions.peticash_status_id')
                                    ->where('peticash_statuses.slug','approved')
                                    ->where('peticash_requested_salary_transactions.project_site_id', $projectSiteId)
                                    ->where('peticash_requested_salary_transactions.reference_user_id', $loggedInUser->id)
                                    ->where('peticash_requested_salary_transactions.updated_at','>=', $lastLogin)
                                    ->count();
                            }
                        }
                    }
                    $purchaseRequestNotificationCount = $purchaseRequestDisapprovedCount + $purchaseRequestCreateCount;
                    $materialRequestNotificationCount = $materialRequestDisapprovedCount + $materialRequestCreateCount;
                    $purchaseOrderRequestNotificationCount = $purchaseOrderRequestCreateCount;
                    $purchaseOrderNotificationCount = $purchaseOrderCreatedCount + $purchaseOrderBillCreateCount;
                    $inventorySiteTransferNotificationCount = $materialSiteOutTransferApproveCount + $materialSiteOutTransferCreateCount;
                    $peticashSalaryRequestApprovalNotificationCount = $peticashSalaryApprovedCount + $peticashSalaryRequestCount;
                }
                $view->with(compact('purchaseRequestNotificationCount','materialRequestNotificationCount', 'purchaseOrderRequestNotificationCount','inventorySiteTransferNotificationCount','peticashSalaryRequestApprovalNotificationCount', 'purchaseOrderNotificationCount'));
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
