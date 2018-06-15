<?php

namespace App\Http\Controllers\Inventory;

use App\Asset;
use App\AssetType;
use App\Client;
use App\Employee;
use App\EmployeeType;
use App\FuelAssetReading;
use App\GRNCount;
use App\Helper\UnitHelper;
use App\Http\Controllers\CustomTraits\Inventory\InventoryTrait;
use App\Http\Controllers\CustomTraits\Notification\NotificationTrait;
use App\InventoryComponent;
use App\InventoryComponentTransferImage;
use App\InventoryComponentTransfers;
use App\InventoryComponentTransferStatus;
use App\InventoryTransferTypes;
use App\Material;
use App\MaterialRequestComponents;
use App\Module;
use App\Project;
use App\ProjectSite;
use App\ProjectSiteUserCheckpoint;
use App\PurchaseOrderComponent;
use App\PurchaseRequestComponentStatuses;
use App\Quotation;
use App\QuotationMaterial;
use App\Unit;
use App\UnitConversion;
use App\UserLastLogin;
use App\Vendor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class InventoryManageController extends Controller
{
    use InventoryTrait;
    use NotificationTrait;
    public function __construct(){
        $this->middleware('custom.auth');
    }
    public function getManageView(Request $request){
        try{
            $projectSites  = ProjectSite::join('projects','projects.id','=','project_sites.project_id')
                ->where('project_sites.name','!=',env('OFFICE_PROJECT_SITE_NAME'))->where('projects.is_active',true)->select('project_sites.id','project_sites.name','projects.name as project_name')->get()->toArray();
            return view('inventory/manage')->with(compact('projectSites'));
        }catch(\Exception $e){
            $data = [
                'action' => 'Inventory manage',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
        }
    }

    public function getTransferManageView(Request $request){
        try{
            return view('inventory/transfer/manage');
        }catch(\Exception $e){
            $data = [
                'action' => 'Inventory Request Transfer manage view',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
        }
    }

    public function getSiteTransferRequestListing(Request $request){
        try{
            $user = Auth::user();
            $status = 200;
            if($request->has('search_name')){
                // Inventory listing search
            }else{
                $siteOutTransferTypeID = InventoryTransferTypes::where('slug','site')->where('type','OUT')->pluck('id')->first();
                $inventoryTransferData = InventoryComponentTransfers::where('transfer_type_id',$siteOutTransferTypeID)->orderBy('created_at','desc')->get();
            }
            $iTotalRecords = count($inventoryTransferData);
            $records = array();
            $records['data'] = array();
            $end = $request->length < 0 ? count($inventoryTransferData) : $request->length;
            for($iterator = 0,$pagination = $request->start; $iterator < $end && $pagination < count($inventoryTransferData); $iterator++,$pagination++ ){
                if($inventoryTransferData[$pagination]->inventoryComponentTransferStatus->slug == 'approved'){
                    $actionDropDown =  '<div id="sample_editable_1_new" class="btn btn-small blue">
                                            <a href="/inventory/pdf/'.$inventoryTransferData[$pagination]['id'].'" style="color: white"> 
                                                PDF <i class="fa fa-download" aria-hidden="true"></i>
                                            </a>
                                        </div>';
                }elseif($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin' || $user->customHasPermission('approve-asset-maintenance-approval')){
                    $actionDropDown =  '<button class="btn btn-xs blue"> 
                                            <form action="/inventory/transfer/change-status/approved/'.$inventoryTransferData[$pagination]->id.'" method="post">
                                                <a href="javascript:void(0);" onclick="changeStatus(this)" style="color: white">
                                                     Approve 
                                                </a>
                                                <input type="hidden" name="_token">
                                            </form> 
                                        </button>
                                        <button class="btn btn-xs default "> 
                                            <form action="/inventory/transfer/change-status/disapproved/'.$inventoryTransferData[$pagination]->id.'" method="post">
                                                <a href="javascript:void(0);" onclick="changeStatus(this)" style="color: grey">
                                                    Disapprove 
                                                </a>
                                                <input type="hidden" name="_token">
                                            </form>
                                        </button>';
                }else{
                    $actionDropDown = '';
                }
                $records['data'][$iterator] = [
                    $inventoryTransferData[$pagination]->inventoryComponent->projectSite->project->name,
                    $inventoryTransferData[$pagination]->source_name,
                    $inventoryTransferData[$pagination]->inventoryComponent->name,
                    $inventoryTransferData[$pagination]->quantity,
                    $inventoryTransferData[$pagination]->unit->name,
                    $inventoryTransferData[$pagination]->inventoryComponentTransferStatus->name,
                    $actionDropDown
                ];
            }
            $records["draw"] = intval($request->draw);
            $records["recordsTotal"] = $iTotalRecords;
            $records["recordsFiltered"] = $iTotalRecords;
        }catch (\Exception $e){
            $data = [
                'action' => 'Request Component listing',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            $status = 500;
            $response = array();
        }
        return response()->json($records,$status);
    }

    public function changeStatus(Request $request,$status,$inventoryTransferId){
        try{
            InventoryComponentTransfers::where('id',$inventoryTransferId)->update([
                'inventory_component_transfer_status_id' => InventoryComponentTransferStatus::where('slug',$status)->pluck('id')->first()
            ]);
            if($status == 'approved'){
                $siteOutTransferTypeId = InventoryTransferTypes::where('slug','site')->where('type','ilike','out')->pluck('id')->first();
                $inventoryTransfer = InventoryComponentTransfers::findOrFail($inventoryTransferId);
                if($inventoryTransfer->transfer_type_id == $siteOutTransferTypeId){
                    $webTokens = [$inventoryTransfer->user->web_fcm_token];
                    $mobileTokens = [$inventoryTransfer->user->mobile_fcm_token];
                    $notificationString = $inventoryTransfer->inventoryComponent->projectSite->project->name.'-'.$inventoryTransfer->inventoryComponent->projectSite->name.' ';
                    $notificationString .= 'Stock transferred to '.$inventoryTransfer->source_name.' Approved ';
                    $notificationString .= $inventoryTransfer->inventoryComponent->name.' - '.$inventoryTransfer->quantity.' and '.$inventoryTransfer->unit->name;
                    $this->sendPushNotification('Manish Construction',$notificationString,$webTokens,$mobileTokens,'c-m-s-t-a');
                }
            }
            return view('/inventory/transfer/manage');
        }catch(\Exception $e){
            $data = [
                'action' => 'Change Status',
                'exception' => $e->getMessage(),
                'request' => $request->all()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function createInventoryComponent(Request $request){
        try{
            $projectSiteId = Session::get('global_project_site');
            $newInventoryComponent = InventoryComponent::where('project_site_id',$projectSiteId)->where('name','ilike',trim($request['name']))->first();
            if($newInventoryComponent == null){
                $inventoryComponentData['name'] = $request['name'];
                $inventoryComponentData['is_material'] = ($request['inventory_type'] == 'material') ? true : false;
                $inventoryComponentData['project_site_id'] = $projectSiteId;
                $inventoryComponentData['opening_stock'] = ($request->has('opening_stock')) ? $request['opening_stock'] : 0;
                $inventoryComponentData['reference_id'] = $request['reference_id'];
                InventoryComponent::create($inventoryComponentData);
            }
            return redirect('/inventory/manage');
        }catch (\Exception $e){
            $data = [
                'action' => 'Create Inventory Component',
                'exception' => $e->getMessage(),
                'request' => $request->all()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getComponentManageView(Request $request,$inventoryComponent){
        try{
            $projectInfo = [
                'project' => $inventoryComponent->projectSite->project->name,
                'client' => $inventoryComponent->projectSite->project->client->company,
                'project_site' => $inventoryComponent->projectSite->name,
            ];
            $user = Auth::user();
            if($inventoryComponent->is_material == true){
                $isReadingApplicable = false;
            }else{
                if($inventoryComponent->asset->assetTypes->slug != 'other'){
                    $isReadingApplicable = true;
                }else{
                    $isReadingApplicable = false;
                }
            }
            if($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin'){
                $clients = Client::join('projects','projects.client_id','=','clients.id')
                    ->join('project_sites','project_sites.project_id','=','projects.id')
                    ->join('quotations','quotations.project_site_id','=','project_sites.id')
                    ->select('clients.company as name','clients.id as id')
                    ->distinct('name')
                    ->get();
            }else{
                $clients = Client::join('projects','projects.client_id','=','clients.id')
                    ->join('project_sites','project_sites.project_id','=','projects.id')
                    ->join('user_project_site_relation','user_project_site_relation.project_site_id','=','project_sites.id')
                    ->join('quotations','quotations.project_site_id','=','project_sites.id')
                    ->where('user_project_site_relation.user_id',$user->id)
                    ->select('clients.company as name','clients.id as id')
                    ->distinct('name')
                    ->get();
            }
            if($inventoryComponent->is_material == true){
                $unit1Array = UnitConversion::join('units', 'units.id', '=', 'unit_conversions.unit_2_id')
                    ->where('unit_conversions.unit_1_id', $inventoryComponent->material->unit_id)
                    ->select('units.id as id', 'units.name as name')
                    ->get()
                    ->toArray();
                $units2Array = UnitConversion::join('units', 'units.id', '=', 'unit_conversions.unit_1_id')
                    ->where('unit_conversions.unit_2_id', $inventoryComponent->material->unit_id)
                    ->whereNotIn('unit_conversions.unit_1_id', array_column($unit1Array, 'id'))
                    ->select('units.id as id', 'units.name as name')
                    ->get()
                    ->toArray();
                $units = array_merge($unit1Array, $units2Array);
                $units[] = [
                    'id' => $inventoryComponent->material->unit->id,
                    'name' => $inventoryComponent->material->unit->name,
                ];

                $amount = PurchaseOrderComponent::join('purchase_request_components','purchase_request_components.id','=','purchase_order_components.purchase_request_component_id')
                    ->join('material_request_components','material_request_components.id','=','purchase_request_components.material_request_component_id')
                    ->join('material_requests','material_requests.id','=','material_request_components.material_request_id')
                    ->where('material_requests.project_site_id',$inventoryComponent['project_site_id'])
                    ->where('material_request_components.name','ilike',$inventoryComponent['name'])
                    ->orderBy('purchase_order_components.id','desc')
                    ->select('purchase_order_components.rate_per_unit','purchase_order_components.cgst_percentage as cgst_percentage','purchase_order_components.cgst_amount as cgst_amount','purchase_order_components.sgst_percentage as sgst_percentage','purchase_order_components.sgst_amount as sgst_amount','purchase_order_components.igst_percentage as igst_percentage','purchase_order_components.igst_amount as igst_amount')
                    ->first();
            }else{
                $amount = Asset::where('name',$inventoryComponent['name'])->pluck('rent_per_day')->first();
                $units = Unit::where('slug','nos')->select('id','name')->get();
            }
            $nosUnitId = Unit::where('slug','nos')->pluck('id')->first();
            $inTransfers = InventoryTransferTypes::where('slug','site')->where('type','ilike','IN')->get();
            $inTransferTypes = '<option value=""> -- Select Transfer Type -- </option>';
            foreach($inTransfers as $transfer){
                $inTransferTypes .= '<option value="'.$transfer->slug.'">'.$transfer->name.'</option>';
            }
            $outTransfers = InventoryTransferTypes::whereIn('slug',['site','user'])->where('type','ilike','OUT')->get();
            $outTransferTypes = '<option value=""> -- Select Transfer Type -- </option>';
            foreach($outTransfers as $transfer){
                $outTransferTypes .= '<option value="'.$transfer->slug.'">'.$transfer->name.'</option>';
            }
            $asset_type = Asset::join('asset_types','assets.asset_types_id','=','asset_types.id')
                            ->where('assets.id',$inventoryComponent['reference_id'])->select('assets.asset_types_id','asset_types.name','asset_types.slug')->first();
            $transportationVendors = Vendor::where('is_active',true)->where('for_transportation',true)->select('id','name')->get()->toArray();
            $sourceName = $inventoryComponent->projectSite->project->name.'-'.$inventoryComponent->projectSite->name;
            $siteOutGrns = InventoryComponentTransfers::join('inventory_components','inventory_components.id','=','inventory_component_transfers.inventory_component_id')
                ->where('transfer_type_id',InventoryTransferTypes::where('slug','site')->where('type','ilike','out')->pluck('id')->first())
                ->where('inventory_component_transfers.inventory_component_transfer_status_id',InventoryComponentTransferStatus::where('slug','approved')->pluck('id')->first())
                ->where('inventory_component_transfers.source_name',$sourceName)
                ->where('inventory_components.name',$inventoryComponent['name'])
                ->where('inventory_components.project_site_id','!=',$inventoryComponent['project_site_id'])
                ->whereNull('related_transfer_id')->select('inventory_component_transfers.id','inventory_component_transfers.grn')->get();
            return view('inventory/component-manage')->with(compact('inventoryComponent','inTransferTypes','outTransferTypes','units','clients','isReadingApplicable','nosUnitId','projectInfo','asset_type','amount','transportationVendors','siteOutGrns'));
        }catch(\Exception $e){
            $data = [
                'action' => 'Inventory manage',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
        }
    }

    public function checkInventoryAvailableQuantity($requestData){
        try{
            $inventoryComponent = InventoryComponent::where('id',$requestData['inventoryComponentId'])->first();
            if($inventoryComponent->is_material == true){
                $materialUnit = Material::where('id',$inventoryComponent['reference_id'])->pluck('unit_id')->first();
                $unitID = Unit::where('id',$materialUnit)->pluck('id')->first();
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
                $unitID = Unit::where('slug','nos')->pluck('id')->first();
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
            if($unitID != $requestData['unitId']){
                $availableQuantity = UnitHelper::unitQuantityConversion($requestData['unitId'],$unitID,$availableQuantity);
            }
            if($requestData['quantity'] <= $availableQuantity){
                $show_validation = false;
            }else{
                $show_validation = true;
            }
        }catch (\Exception $e){
            $show_validation = false;
            $data = [
                'action' => 'Check Inventory Available Quantity',
                'params' => $requestData,
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
        }
        $response = [
            'show_validation' => $show_validation,
            'available_quantity' => $availableQuantity
        ];
        return $response;
    }

    public function checkAvailableQuantity(Request $request){
        try{
            $responseData = $this->checkInventoryAvailableQuantity($request);
        }catch (\Exception $e){
            $responseData['show_validation'] = false;
            $data = [
                'action' => 'Check Available Quantity',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
        }
        $response = [
            'show_validation' => $responseData['show_validation'],
            'available_quantity' => $responseData['available_quantity']
        ];
        return $response;
    }

    public function inventoryListing(Request $request){
        try{
            $status = 200;
            if(Session::has('global_project_site')){
                $projectSiteId = Session::get('global_project_site');
                $inventoryData = InventoryComponent::where('project_site_id', $projectSiteId)->orderBy('created_at','desc')->get();
            }else{
                $inventoryData = InventoryComponent::orderBy('created_at','desc')->get();
            }
            $iTotalRecords = count($inventoryData);
            $records = array();
            $records['data'] = array();
            $end = $request->length < 0 ? count($inventoryData) : $request->length;
            for($iterator = 0,$pagination = $request->start; $iterator < $end && $pagination < count($inventoryData); $iterator++,$pagination++ ){
                $projectName = $inventoryData[$pagination]->projectSite->project->name.' - '.$inventoryData[$pagination]->projectSite->name.' ('.$inventoryData[$pagination]->projectSite->project->client->company.')';
                if($inventoryData[$pagination]->is_material == true){
                    $materialUnit = Material::where('id',$inventoryData[$iterator]['reference_id'])->pluck('unit_id')->first();
                    if($materialUnit == null){
                        $materialUnit = Material::where('name','ilike',$inventoryData[$iterator]['name'])->pluck('unit_id')->first();
                    }
                    $unitName = Unit::where('id',$materialUnit)->pluck('name')->first();
                    $inTransferQuantities = InventoryComponentTransfers::join('inventory_transfer_types','inventory_transfer_types.id','=','inventory_component_transfers.transfer_type_id')
                        ->where('inventory_transfer_types.type','ilike','in')
                        ->where('inventory_component_transfers.inventory_component_id',$inventoryData[$pagination]->id)
                        ->where('inventory_component_transfers.inventory_component_transfer_status_id',InventoryComponentTransferStatus::where('slug','approved')->pluck('id')->first())
                        ->select('inventory_component_transfers.quantity as quantity','inventory_component_transfers.unit_id as unit_id')
                        ->get();
                    $outTransferQuantities = InventoryComponentTransfers::join('inventory_transfer_types','inventory_transfer_types.id','=','inventory_component_transfers.transfer_type_id')
                        ->where('inventory_transfer_types.type','ilike','out')
                        ->where('inventory_component_transfers.inventory_component_id',$inventoryData[$pagination]->id)
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
                    $unitName = Unit::where('slug','nos')->pluck('name')->first();
                    $inQuantity = InventoryComponentTransfers::join('inventory_transfer_types','inventory_transfer_types.id','=','inventory_component_transfers.transfer_type_id')
                        ->where('inventory_transfer_types.type','ilike','in')
                        ->where('inventory_component_transfers.inventory_component_id',$inventoryData[$pagination]->id)
                        ->where('inventory_component_transfers.inventory_component_transfer_status_id',InventoryComponentTransferStatus::where('slug','approved')->pluck('id')->first())
                        ->sum('inventory_component_transfers.quantity');
                    $outQuantity = InventoryComponentTransfers::join('inventory_transfer_types','inventory_transfer_types.id','=','inventory_component_transfers.transfer_type_id')
                        ->where('inventory_transfer_types.type','ilike','out')
                        ->where('inventory_component_transfers.inventory_component_id',$inventoryData[$pagination]->id)
                        ->where('inventory_component_transfers.inventory_component_transfer_status_id',InventoryComponentTransferStatus::where('slug','approved')->pluck('id')->first())
                        ->sum('inventory_component_transfers.quantity');
                }
                $availableQuantity = $inQuantity - $outQuantity;
                $records['data'][$iterator] = [
                    $projectName,
                    $inventoryData[$pagination]->name,
                    $inQuantity.' '.$unitName,
                    $outQuantity.' '.$unitName,
                    $availableQuantity.' '.$unitName,
                    '<div class="btn btn-xs green">
                        <a href="/inventory/component/manage/'.$inventoryData[$pagination]->id.'" style="color: white">
                             Manage
                        </a>
                    </div>'
                ];
            }
            $records["draw"] = intval($request->draw);
            $records["recordsTotal"] = $iTotalRecords;
            $records["recordsFiltered"] = $iTotalRecords;
            $user = Auth::user();
            $userLastLogin = UserLastLogin::join('modules','modules.id','=','user_last_logins.module_id')
                ->where('modules.slug','component-transfer')
                ->where('user_last_logins.user_id',$user->id)
                ->pluck('user_last_logins.id as user_last_login_id')
                ->first();
            if($userLastLogin != null){
                UserLastLogin::where('id', $userLastLogin)->update(['last_login' => Carbon::now()]);
            }else{
                UserLastLogin::create([
                    'user_id' => $user->id,
                    'module_id' => Module::where('slug','component-transfer')->pluck('id')->first(),
                    'last_login' => Carbon::now()
                ]);
            }
        }catch(\Exception $e){
            $data = [
                'action' => 'Inventory listing',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            $status = 500;
            $response = array();
        }
        return response()->json($records,$status);
    }

    public function inventoryComponentListing(Request $request,$inventoryComponent){
        try{
            $inventoryComponentTransfers = ($inventoryComponent->inventoryComponentTransfers->sortByDesc('id'));
            $status = 200;
            $iTotalRecords = count($inventoryComponentTransfers);
            $records = array();
            $records['data'] = array();
            $end = $request->length < 0 ? count($inventoryComponentTransfers) : $request->length;
            for($iterator = 0,$pagination = $request->start; $iterator < $end && $pagination < count($inventoryComponentTransfers); $iterator++,$pagination++ ){

                if($inventoryComponentTransfers[$pagination]->transferType->type == 'IN'){
                    $transferStatus = 'IN - From '.$inventoryComponentTransfers[$pagination]->transferType->name;
                    if($inventoryComponentTransfers[$pagination]->transferType->slug == 'site' && $inventoryComponentTransfers[$pagination]->inventoryComponentTransferStatus->slug == 'grn-generated'){
                        $action = '<a href="javascript:void(0);" class="btn btn-xs green dropdown-toggle" type="button" aria-expanded="true" onclick="changeStatus('.$inventoryComponentTransfers[$pagination]->id.')">
                                        Update
                                    </a>
                                    <a href="javascript:void(0);" class="btn btn-xs green dropdown-toggle" type="button" aria-expanded="true" onclick="openDetails('.$inventoryComponentTransfers[$pagination]->id.')">
                                        Details
                                    </a>
                                   <!-- <a href="javascript:void(0);" class="btn btn-xs green dropdown-toggle" type="button" aria-expanded="true">
                                        Make Payment
                                    </a>-->';
                    }elseif ($inventoryComponentTransfers[$pagination]->transferType->slug == 'site'){
                        $action = '<a href="javascript:void(0);" class="btn btn-xs green dropdown-toggle" type="button" aria-expanded="true" onclick="openDetails('.$inventoryComponentTransfers[$pagination]->id.')">
                                        Details
                                    </a>
                                    <!--<a href="javascript:void(0);" class="btn btn-xs green dropdown-toggle" type="button" aria-expanded="true">
                                        Make Payment
                                    </a>-->';
                    }else{
                        $action = '<a href="javascript:void(0);" class="btn btn-xs green dropdown-toggle" type="button" aria-expanded="true" onclick="openDetails('.$inventoryComponentTransfers[$pagination]->id.')">
                                        Details
                                    </a>
                                    ';
                    }
                }else{
                    $transferStatus = 'OUT - To '.$inventoryComponentTransfers[$pagination]->transferType->name;
                    $action = '<a href="javascript:void(0);" class="btn btn-xs green dropdown-toggle" type="button" aria-expanded="true" onclick="openDetails('.$inventoryComponentTransfers[$pagination]->id.')">
                                        Details
                                    </a>';
                }
                $records['data'][$iterator] = [
                    $inventoryComponentTransfers[$pagination]['grn'],
                    $inventoryComponentTransfers[$pagination]['quantity'],
                    $inventoryComponentTransfers[$pagination]->unit->name,
                    $transferStatus,
                    $inventoryComponentTransfers[$pagination]->inventoryComponentTransferStatus->name,
                    $action
                ];
            }
            $records["draw"] = intval($request->draw);
            $records["recordsTotal"] = $iTotalRecords;
            $records["recordsFiltered"] = $iTotalRecords;
        }catch(\Exception $e){
            $data = [
                'action' => 'Inventory Component listing',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            $status = 500;
            $records = array();
        }
        return response()->json($records,$status);
    }

    public function editOpeningStock(Request $request){
        try{
            InventoryComponent::where('id',$request->inventory_component_id)->update(['opening_stock' => $request->opening_stock]);
            $status = 200;
            $response = [
                'message' => 'Opening stock saved Successfully !!'
            ];
        }catch(\Exception $e){
            $data = [
                'action' => 'Inventory Component listing',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            $status = 500;
            $response = array();
        }
        return response()->json($response,$status);
    }

    public function getInventoryComponentTransferDetail(Request $request,$inventoryComponentTransfer,$forSlug){
        try{
            if(count($inventoryComponentTransfer->images) > 0){
                $inventoryComponentTransferImages = $this->getTransferImages($inventoryComponentTransfer);
            }else{
                $inventoryComponentTransferImages = array();
            }
            $transportation_cgst_amount = ($inventoryComponentTransfer['transportation_amount'] * $inventoryComponentTransfer['transportation_cgst_percent']) / 100;
            $transportation_sgst_amount = ($inventoryComponentTransfer['transportation_amount'] * $inventoryComponentTransfer['transportation_sgst_percent']) / 100;
            $transportation_igst_amount = ($inventoryComponentTransfer['transportation_amount'] * $inventoryComponentTransfer['transportation_igst_percent']) / 100;
            $inventoryComponentTransfer['company_name'] = Vendor::where('id',$inventoryComponentTransfer['vendor_id'])->pluck('company')->first();
            $inventoryComponentTransfer['transportation_tax_amount'] = $transportation_cgst_amount + $transportation_sgst_amount + $transportation_igst_amount;
            if($forSlug == 'for-detail'){
                return view('partials.inventory.inventory-component-transfer-detail')->with(compact('inventoryComponentTransfer','inventoryComponentTransferImages'));
            }else{
                $unit = $inventoryComponentTransfer->unit->name;
                $relatedTransferGRN = InventoryComponentTransfers::where('id',$inventoryComponentTransfer['related_transfer_id'])->pluck('grn')->first();
                return view('partials.inventory.inventory-component-transfer-approve')->with(compact('inventoryComponentTransfer','inventoryComponentTransferImages','relatedTransferGRN','unit'));
            }
        }catch(\Exception $e){
            $data = [
                'action' => 'Get inventory component transfer details',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            return response()->json([],500);
        }
    }

    public function uploadTempImages(Request $request,$inventoryComponent){
        try{
            Log::info('inside upload images');
            $user = Auth::user();
            $userDirectoryName = sha1($user->id);
            $inventoryComponentDirectoryName = sha1($inventoryComponent->id);
            $tempUploadPath = public_path().env('INVENTORY_COMPONENT_TEMP_IMAGE_UPLOAD');
            $tempImageUploadPath = $tempUploadPath.DIRECTORY_SEPARATOR.$inventoryComponentDirectoryName.DIRECTORY_SEPARATOR.$userDirectoryName;
            /* Create Upload Directory If Not Exists*/
            if (!file_exists($tempImageUploadPath)) {
                File::makeDirectory($tempImageUploadPath, $mode = 0777, true, true);
            }
            $extension = $request->file('file')->getClientOriginalExtension();
            $filename = mt_rand(1,10000000000).sha1(time()).".{$extension}";
            $request->file('file')->move($tempImageUploadPath,$filename);
            $path = env('INVENTORY_COMPONENT_TEMP_IMAGE_UPLOAD').DIRECTORY_SEPARATOR.$inventoryComponentDirectoryName.DIRECTORY_SEPARATOR.$userDirectoryName.DIRECTORY_SEPARATOR.$filename;
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

    public function displayTempImages(Request $request){
        try{
            $path = $request->path;
            $count = $request->count;
            $random = mt_rand(1,10000000000);
        }catch (\Exception $e){
            $path = null;
            $count = null;
        }
        return view('partials.quotation.work-order-images')->with(compact('path','count','random'));
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

    public function preGrnImageUpload(Request $request){
        try{
            $user = Auth::user();
            $relatedInventoryComponentTransferData = InventoryComponentTransfers::where('id', $request['related_inventory_component_transfer_id'])->first();
            $inventoryComponentId = $request['inventory_component_id'];
            $projectSite = ProjectSite::where('id',$relatedInventoryComponentTransferData->inventoryComponent->project_site_id)->first();
            $sourceName = $projectSite->project->name.'-'.$projectSite->name;
            $currentDate = Carbon::now();
            $monthlyGrnGeneratedCount = GRNCount::where('month',$currentDate->month)->where('year',$currentDate->year)->pluck('count')->first();
            if($monthlyGrnGeneratedCount != null){
                $serialNumber = $monthlyGrnGeneratedCount + 1;
            }else{
                $serialNumber = 1;
            }
            $grn = "GRN".date('Ym').($serialNumber);
            $inventoryComponentTransfer = InventoryComponentTransfers::create([
                'inventory_component_id' => $inventoryComponentId,
                'transfer_type_id' => InventoryTransferTypes::where('slug','site')->where('type','ilike','IN')->pluck('id')->first(),
                'quantity' => $request['quantity'],
                'unit_id' => $relatedInventoryComponentTransferData['unit_id'],
                'source_name' => $sourceName,
                'bill_number' => $relatedInventoryComponentTransferData['bill_number'],
                'bill_amount' => $relatedInventoryComponentTransferData['bill_amount'],
                'vehicle_number' => $relatedInventoryComponentTransferData['vehicle_number'],
                'in_time' => $currentDate,
                'user_id' => $user['id'],
                'grn' => $grn,
                'inventory_component_transfer_status_id' => InventoryComponentTransferStatus::where('slug','grn-generated')->pluck('id')->first(),
                'rate_per_unit' => $relatedInventoryComponentTransferData['rate_per_unit'],
                'cgst_percentage' => $relatedInventoryComponentTransferData['cgst_percentage'],
                'sgst_percentage' => $relatedInventoryComponentTransferData['sgst_percentage'],
                'igst_percentage' => $relatedInventoryComponentTransferData['igst_percentage'],
                'cgst_amount' => $relatedInventoryComponentTransferData['cgst_amount'],
                'sgst_amount' => $relatedInventoryComponentTransferData['sgst_amount'],
                'igst_amount' => $relatedInventoryComponentTransferData['igst_amount'],
                'total' => $relatedInventoryComponentTransferData['total'],
                'vendor_id' => $relatedInventoryComponentTransferData['vendor_id'],
                'transportation_amount' => $relatedInventoryComponentTransferData['transportation_amount'],
                'transportation_cgst_percent' => $relatedInventoryComponentTransferData['transportation_cgst_percent'],
                'transportation_sgst_percent' => $relatedInventoryComponentTransferData['transportation_sgst_percent'],
                'transportation_igst_percent' => $relatedInventoryComponentTransferData['transportation_igst_percent'],
                'driver_name' => $relatedInventoryComponentTransferData['driver_name'],
                'mobile' => $relatedInventoryComponentTransferData['mobile'],
                'related_transfer_id' => $relatedInventoryComponentTransferData['id']
            ]);

            $relatedInventoryComponentTransferData->update(['related_transfer_id' => $inventoryComponentTransfer['id']]);
            if ($monthlyGrnGeneratedCount != null) {
                GRNCount::where('month', $currentDate->month)->where('year', $currentDate->year)->update(['count' => $serialNumber]);
            } else {
                GRNCount::create(['month' => $currentDate->month, 'year' => $currentDate->year, 'count' => $serialNumber]);
            }
            if($request->has('imageArray')){
                $sha1InventoryComponentId = sha1($inventoryComponentId);
                $sha1InventoryTransferId = sha1($inventoryComponentTransfer['id']);
                $imageUploadPath = public_path().env('INVENTORY_COMPONENT_IMAGE_UPLOAD') . DIRECTORY_SEPARATOR . $sha1InventoryComponentId . DIRECTORY_SEPARATOR . 'transfers' . DIRECTORY_SEPARATOR . $sha1InventoryTransferId;
                if (!file_exists($imageUploadPath)) {
                    File::makeDirectory($imageUploadPath, $mode = 0777, true, true);
                }
                $imageArray = explode(';',$request['imageArray']);
                $image = explode(',',$imageArray[1])[1];
                $pos  = strpos($request['imageArray'], ';');
                $type = explode(':', substr($request['imageArray'], 0, $pos))[1];
                $extension = explode('/',$type)[1];
                $filename = mt_rand(1,10000000000).sha1(time()).".{$extension}";
                $fileFullPath = $imageUploadPath.DIRECTORY_SEPARATOR.$filename;
                file_put_contents($fileFullPath,base64_decode($image));
                InventoryComponentTransferImage::create(['name' => $filename, 'inventory_component_transfer_id' => $inventoryComponentTransfer['id']]);
            }
            $response = [
                'inventory_component_transfer_id' => $inventoryComponentTransfer['id'],
                'grn' => $grn
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

    public function addComponentTransfer(Request $request,$inventoryComponent){
        try{
            $user = Auth::user();
            if($request['transfer_type'] == 'site' && $request['in_or_out'] == 'on'){
                $inventoryComponentTransfer = InventoryComponentTransfers::where('id',$request['inventory_component_transfer_id'])->first();
                $now = Carbon::now();
                $inventoryComponentTransfer->update([
                    'out_time' => $now,
                    'date' => $now,
                    'inventory_component_transfer_status_id' => InventoryComponentTransferStatus::where('slug','approved')->pluck('id')->first(),
                    'remark' => $request['remark']
                ]);
                $sha1InventoryComponentId = sha1($inventoryComponentTransfer['inventory_component_id']);
                $sha1InventoryTransferId = sha1($inventoryComponentTransfer['id']);
                $imageUploadPath = public_path() . env('INVENTORY_COMPONENT_IMAGE_UPLOAD') . DIRECTORY_SEPARATOR . $sha1InventoryComponentId . DIRECTORY_SEPARATOR . 'transfers' . DIRECTORY_SEPARATOR . $sha1InventoryTransferId;
                if (!file_exists($imageUploadPath)) {
                    File::makeDirectory($imageUploadPath, $mode = 0777, true, true);
                }
                if ($request->has('post_grn_image') && count($request->post_grn_image) > 0) {
                    foreach ($request['post_grn_image'] as $key1 => $imageName) {
                        $imageArray = explode(';',$imageName);
                        $image = explode(',',$imageArray[1])[1];
                        $pos  = strpos($imageName, ';');
                        $type = explode(':', substr($imageName, 0, $pos))[1];
                        $extension = explode('/',$type)[1];
                        $filename = mt_rand(1,10000000000).sha1(time()).".{$extension}";
                        $fileFullPath = $imageUploadPath.DIRECTORY_SEPARATOR.$filename;
                        file_put_contents($fileFullPath,base64_decode($image));
                        InventoryComponentTransferImage::create(['name' => $filename, 'inventory_component_transfer_id' => $inventoryComponentTransfer['id']]);
                    }
                }
                $fromProjectSitesArray = explode('-', $inventoryComponentTransfer->source_name);
                $projectSiteId = ProjectSite::join('projects','projects.id','=','project_sites.project_id')
                    ->where('project_sites.name','ilike', trim($fromProjectSitesArray[1]))
                    ->where('projects.name','ilike', trim($fromProjectSitesArray[0]))
                    ->pluck('project_sites.id')->first();
                $fromInventoryComponentId = InventoryComponent::where('project_site_id', $projectSiteId)
                    ->where('name','ilike', $inventoryComponentTransfer->inventoryComponent->name)
                    ->pluck('id')->first();
                $siteOutTransferTypeId = InventoryTransferTypes::where('slug','site')->where('type','ilike','out')->pluck('id')->first();
                $lastOutInventoryComponentTransfer = InventoryComponentTransfers::where('inventory_component_id', $fromInventoryComponentId)
                    ->where('transfer_type_id', $siteOutTransferTypeId)
                    ->where('source_name','ilike',$inventoryComponentTransfer->inventoryComponent->projectSite->project->name.'-'.$inventoryComponentTransfer->inventoryComponent->projectSite->name)
                    ->orderBy('created_at', 'desc')
                    ->first();
                $webTokens = [$lastOutInventoryComponentTransfer->user->web_fcm_token];
                $mobileTokens = [$lastOutInventoryComponentTransfer->user->mobile_fcm_token];
                $notificationString = 'From '.$inventoryComponentTransfer->source_name.' stock received to ';
                $notificationString .= $inventoryComponentTransfer->inventoryComponent->projectSite->project->name.' - '.$inventoryComponentTransfer->inventoryComponent->projectSite->name.' ';
                $notificationString .= $inventoryComponentTransfer->inventoryComponent->name.' - '.$inventoryComponentTransfer->quantity.' and '.$inventoryComponentTransfer->unit->name;
                $this->sendPushNotification('Manisha Construction', $notificationString,$webTokens,$mobileTokens,'c-m-s-i-t');
                $request->session()->flash('success','Inventory Component Transfer Saved Successfully!!');
                return redirect('/inventory/component/manage/'.$inventoryComponent->id);
            }else{
                $data = $request->except(['_token','work_order_images','project_site_id','grn']);
                $data['inventory_component_id'] = $inventoryComponent->id;
                $data['user_id'] = $user['id'];
                $data['in_time'] = $data['out_time'] = $data['date'] = Carbon::now();
                $checkAvailableQuantity['inventoryComponentId'] = $inventoryComponent->id;
                $checkAvailableQuantity['quantity'] = $request['quantity'];
                $checkAvailableQuantity['unitId'] = $request['unit_id'];
                $quantityCheck = $this->checkInventoryAvailableQuantity($checkAvailableQuantity);
                if($quantityCheck['show_validation'] == true){
                    $request->session()->flash('success','Insufficient Quantity for this transaction. Available quantity is '.$quantityCheck['available_quantity']);
                    return redirect('/inventory/component/manage/'.$inventoryComponent->id);
                }
                if($request->has('project_site_id') && $request->transfer_type =='site'){
                    $projectSite = ProjectSite::where('id',$request['project_site_id'])->first();
                    $data['source_name'] = $projectSite->project->name.'-'.$projectSite->name;
                    if($request->has('in_or_out')){
                        $data['inventory_component_transfer_status_id'] = InventoryComponentTransferStatus::where('slug','approved')->pluck('id')->first();
                        $baseInventoryComponentTransfer = InventoryComponentTransfers::where('grn',$request['grn'])->first();
                        if($inventoryComponent['is_material'] == true){
                            $data['rate_per_unit'] = $baseInventoryComponentTransfer['rate_per_unit'];
                            $data['cgst_percentage'] = $baseInventoryComponentTransfer['cgst_percentage'];
                            $data['sgst_percentage'] = $baseInventoryComponentTransfer['sgst_percentage'];
                            $data['igst_percentage'] = $baseInventoryComponentTransfer['igst_percentage'];
                            $subtotal = $data['quantity'] * $data['rate_per_unit'];
                            $data['cgst_amount'] = $subtotal * ($data['cgst_percentage'] / 100) ;
                            $data['sgst_amount'] = $subtotal * ($data['sgst_percentage'] / 100) ;
                            $data['igst_amount'] = $subtotal * ($data['igst_percentage'] / 100) ;
                            $data['total'] = $subtotal + $data['cgst_amount'] + $data['sgst_amount'] + $data['igst_amount'];
                        }else{
                            $data['rate_per_unit'] = $baseInventoryComponentTransfer['rate_per_unit'];
                        }
                    }else{
                        $data = array_merge($data,$request->only('rate_per_unit','cgst_percentage','sgst_percentage','igst_percentage','cgst_amount','sgst_amount','igst_amount','total','transfer_type'));
                        $data['inventory_component_transfer_status_id'] = InventoryComponentTransferStatus::where('slug','requested')->pluck('id')->first();
                        $data['rate_per_unit'] = $request['rate_per_unit'];
                    }
                }elseif($request->transfer_type =='user'){
                    $data['inventory_component_transfer_status_id'] = InventoryComponentTransferStatus::where('slug','approved')->pluck('id')->first();
                   // $data['rate_per_unit'] = $request['rent'];
                }else{
                    $data['inventory_component_transfer_status_id'] = InventoryComponentTransferStatus::where('slug','approved')->pluck('id')->first();
                }

                $inventoryComponentTransfer = $this->createInventoryComponentTransfer($data);
                if($request->has('work_order_images')){
                    $imageUploads = $this->uploadInventoryComponentTransferImages($request->work_order_images,$inventoryComponent->id,$inventoryComponentTransfer->id);
                }
                if($request->has('project_site_id') && $request->transfer_type =='site'){
                    if($request->has('in_or_out')) {
                        $request->session()->flash('success','Inventory Component Transfer Saved Successfully!!');
                        return redirect('/inventory/component/manage/'.$inventoryComponent->id);
                    }else{
                        $request->session()->flash('success', 'Inventory Component Transfer Saved Successfully!!');
                        return redirect('/inventory/transfer/manage');
                    }
                }else{
                    $request->session()->flash('success','Inventory Component Transfer Saved Successfully!!');
                    return redirect('/inventory/component/manage/'.$inventoryComponent->id);
                }
            }


        }catch(\Exception $e){
            $data = [
                'action' => 'Add Inventory Component Transfer',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getTransferImages($inventoryComponentTransfer){
        try{
            $paths = array();
            $imageUploadPath = env('INVENTORY_COMPONENT_IMAGE_UPLOAD');
            $inventoryComponentDirectoryName = sha1($inventoryComponentTransfer->inventoryComponent->id);
            $inventoryComponentTransferDirectoryName = sha1($inventoryComponentTransfer->id);
            $imageUploadDirectoryPath = $imageUploadPath.DIRECTORY_SEPARATOR.$inventoryComponentDirectoryName.DIRECTORY_SEPARATOR.'transfers'.DIRECTORY_SEPARATOR.$inventoryComponentTransferDirectoryName;
            foreach($inventoryComponentTransfer->images as $image){
                $paths[] = $imageUploadDirectoryPath.DIRECTORY_SEPARATOR.$image->name;
            }
            return $paths;
        }catch(\Exception $e){
            $data = [
                'action' => 'Get Inventory Component Transfer Images',
                'component' => $inventoryComponentTransfer,
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getProjectSites(Request $request){
        try{
            $projectId = $request->project_id;
            $projectSites = ProjectSite::where('project_id',$projectId)->select('id','name')->get();
            $response = array();
            if(count($projectSites) <= 0)
            {
                $response[] = '<option value=" " style="text-color:red">Project Site Not Available</option>';
            }else{
                foreach ($projectSites as $projectSite) {
                    $response[] = '<option value="' . $projectSite->id . '">' . $projectSite->name . '</option> ';
                }
            }
            $status = 200;
        }catch(\Exception $e){
            $data = [
                'action' => 'Get Projects',
                'param' => $request->all(),
                'exception' => $e->getMessage()
            ];
            $status = 500;
            $response = array();
            Log::critical(json_encode($data));
        }
        return response()->json($response,$status);
    }

    public function inventoryComponentReadingListing(Request $request,$inventoryComponent){
        try{
            $status = 200;
            $inventoryComponentFuelReadingData = FuelAssetReading::where('inventory_component_id',$inventoryComponent->id)->orderBy('created_at','desc')->get();
            $iTotalRecords = count($inventoryComponentFuelReadingData);
            $records = array();
            $records['data'] = array();
            $end = $request->length < 0 ? count($inventoryComponentFuelReadingData) : $request->length;
            for($iterator = 0,$pagination = $request->start; $iterator < $end && $pagination < count($inventoryComponentFuelReadingData); $iterator++,$pagination++ ){
                $unitsUsed = $inventoryComponentFuelReadingData[$pagination]->stop_reading - $inventoryComponentFuelReadingData[$pagination]->start_reading;
                $fuelConsumed = '-';
                $electricityConsumed = '-';
                if($inventoryComponentFuelReadingData[$pagination]->fuel_per_unit != null){
                    $fuelConsumed = $unitsUsed * $inventoryComponentFuelReadingData[$pagination]->fuel_per_unit;
                }
                if($inventoryComponentFuelReadingData[$pagination]->electricity_per_unit != null){
                    $electricityConsumed = $unitsUsed * $inventoryComponentFuelReadingData[$pagination]->electricity_per_unit;
                }
                $records['data'][$iterator] = [
                    $inventoryComponentFuelReadingData[$pagination]->start_reading,
                    $inventoryComponentFuelReadingData[$pagination]->stop_reading,
                    $inventoryComponentFuelReadingData[$pagination]->start_time,
                    $inventoryComponentFuelReadingData[$pagination]->stop_time,
                    $unitsUsed,
                    $inventoryComponentFuelReadingData[$pagination]->fuel_per_unit,
                    $inventoryComponentFuelReadingData[$pagination]->electricity_per_unit,
                    $fuelConsumed,
                    $electricityConsumed,
                    $inventoryComponentFuelReadingData[$pagination]->top_up,
                    $inventoryComponentFuelReadingData[$pagination]->top_up_time,
                ];
            }
            $records["draw"] = intval($request->draw);
            $records["recordsTotal"] = $iTotalRecords;
            $records["recordsFiltered"] = $iTotalRecords;
        }catch(\Exception $e){
            $data = [
                'action' => 'Inventory component Fuel reading listing',
                'param' => $request->all(),
                'exception' => $e->getMessage()
            ];
            $status = 500;
            $records = array();
            Log::critical(json_encode($data));
        }
        return response()->json($records,$status);
    }

    public function addInventoryComponentReading(Request $request,$inventoryComponent){
        try{
            $data = $request->except(['_token']);
            $user = Auth::user();
            $data['inventory_component_id'] = $inventoryComponent->id;
            $data['user_id'] = $user->id;
            if(array_key_exists('top_up',$data)){
                $inTransferIds = InventoryTransferTypes::where('type','ilike','IN')->pluck('id')->toArray();
                $outTransferIds = InventoryTransferTypes::where('type','ilike','OUT')->pluck('id')->toArray();
                $dieselcomponentId = InventoryComponent::join('materials','materials.id','=','inventory_components.reference_id')
                                                    ->where('inventory_components.project_site_id',$inventoryComponent->project_site_id)
                                                    ->where('materials.slug','diesel')
                                                    ->pluck('inventory_components.id')
                                                    ->first();
                if($dieselcomponentId == null){
                    $request->session()->flash('error','Diesel is not assigned to this site. Please add diesel in inventory first.');
                    return redirect('/inventory/component/manage/'.$inventoryComponent->id);
                }

                $inQuantity = InventoryComponentTransfers::where('inventory_component_id',$dieselcomponentId)->whereIn('transfer_type_id',$inTransferIds)->sum('quantity');
                $outQuantity = InventoryComponentTransfers::where('inventory_component_id',$dieselcomponentId)->whereIn('transfer_type_id',$outTransferIds)->sum('quantity');
                $availableQuantity = $inQuantity - $outQuantity;
                if($availableQuantity < $data['top_up']){
                    $request->session()->flash('error','Diesel top-up\'s required quantity is not available on site');
                    return redirect('/inventory/component/manage/'.$inventoryComponent->id);
                }
            }
            $inventoryComponentReading = FuelAssetReading::create($data);
            if(array_key_exists('top_up',$data) && $data['top_up'] != null){
                $inventoryTransferData = [
                    'inventory_component_id' => $dieselcomponentId,
                    'transfer_type_id' => InventoryTransferTypes::where('slug','user')->where('type','ilike','OUT')->pluck('id')->first(),
                    'quantity' => $data['top_up'],
                    'unit_id' => Unit::where('slug','litre')->pluck('id')->first(),
                    'source_name' => $user->first_name.' '.$user->last_name,
                    'user_id' => $user->id,
                    'inventory_component_transfer_status_id' => InventoryComponentTransferStatus::where('slug','approved')->pluck('id')->first()
                ];
                $inventoryComponentTransfer = $this->createInventoryComponentTransfer($inventoryTransferData);
            }
            $request->session()->flash('success','Asset Reading saved successfully.');
            return redirect('/inventory/component/manage/'.$inventoryComponent->id);
        }catch(\Exception $e){
            $data = [
                'action' => 'Add Inventory component asset Reading',
                'param' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function autoSuggest(Request $request,$type,$keyword){
        try{
            $projectSiteId = Session::get('global_project_site');
            $response = array();
            $alreadyExitMaterialsIds = InventoryComponent::where('project_site_id',$projectSiteId)->pluck('reference_id');
            if($type == 'material'){
                $response = InventoryComponent::where('name','ilike','%'.$keyword.'%')->where('is_material',true)->whereNotIn('reference_id',$alreadyExitMaterialsIds)->distinct('name')->select('name','reference_id')->get();
            }else{
                $response = InventoryComponent::where('name','ilike','%'.$keyword.'%')->where('is_material',false)->whereNotIn('reference_id',$alreadyExitMaterialsIds)->distinct('name')->select('name','reference_id')->get();
            }
            $status = 200;
        }catch(\Exception $e){
            $response = array();
            $data = [
                'action' => 'Inventory Transfer Auto-suggest',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            $status = 500;
        }
        return response()->json($response,$status);
    }

    public function employeeAutoSuggest(Request $request,$keyword){
        try{
            $employeeDetails = Employee::where('employee_id','ilike','%'.$keyword.'%')->orWhere('name','ilike','%'.$keyword.'%')->whereIn('employee_type_id',EmployeeType::whereIn('slug',['labour','staff','partner'])->pluck('id'))->where('is_active',true)->get()->toArray();
            $data = array();
            $iterator = 0;
            foreach($employeeDetails as $key => $employeeDetail){
                $data[$iterator]['employee_id'] = $employeeDetail['id'];
                $data[$iterator]['employee_name'] = $employeeDetail['name'] .' - '. $employeeDetail['employee_id'];
            }
            $status = 200;
        }catch(\Exception $e){
            $data = [
                'action' => 'Inventory Transfer Employee Auto-suggest',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            $status = 500;
        }
        return response()->json($data,$status);
    }

    public function getInventoryComponentTransferPDF(Request $request,$inventoryComponentTransferID){
        try{
            $data = array();
            $inventoryComponentTransfer = InventoryComponentTransfers::where('id',$inventoryComponentTransferID)->first();
            $inventoryComponent = $inventoryComponentTransfer->inventoryComponent;
            $projectSiteFrom = $inventoryComponent->projectSite;
            $data['project_site_from'] = $projectSiteFrom->project->name.'-'.$projectSiteFrom->name;
            $data['project_site_from_address'] = $projectSiteFrom->address;
            $data['project_site_to'] = $inventoryComponentTransfer['source_name'];
            $project_site_data = explode('-',$inventoryComponentTransfer['source_name']);
            $data['project_site_to_address'] = ProjectSite::join('projects','projects.id','=','project_sites.project_id')
                                ->where('projects.name',$project_site_data[0])->where('project_sites.name',$project_site_data[1])->pluck('project_sites.address')->first();
            $data['grn'] = $inventoryComponentTransfer['grn'];
            $data['component_name'] = $inventoryComponent['name'];
            $data['quantity'] = $inventoryComponentTransfer['quantity'];
            $data['rate_per_unit'] = $inventoryComponentTransfer['rate_per_unit'];
            $data['cgst_percentage'] = $inventoryComponentTransfer['cgst_percentage'];
            $data['sgst_percentage'] = $inventoryComponentTransfer['sgst_percentage'];
            $data['igst_percentage'] = $inventoryComponentTransfer['igst_percentage'];
            $data['cgst_amount'] = $inventoryComponentTransfer['cgst_amount'];
            $data['sgst_amount'] = $inventoryComponentTransfer['sgst_amount'];
            $data['igst_amount'] = $inventoryComponentTransfer['igst_amount'];
            $data['total'] = $inventoryComponentTransfer['total'];
            $data['unit'] = $inventoryComponentTransfer->unit->name;
            $data['is_material'] = $inventoryComponentTransfer->inventoryComponent->is_material;
            $data['transportation_amount'] = $inventoryComponentTransfer->transportation_amount;
            $data['transportation_cgst_percent'] = $inventoryComponentTransfer->transportation_cgst_percent;
            $data['transportation_cgst_amount'] = ($inventoryComponentTransfer->transportation_amount * $inventoryComponentTransfer->transportation_cgst_percent) / 100;
            $data['transportation_sgst_percent'] = $inventoryComponentTransfer->transportation_sgst_percent;
            $data['transportation_sgst_amount'] = ($inventoryComponentTransfer->transportation_amount * $inventoryComponentTransfer->transportation_sgst_percent) / 100;
            $data['transportation_igst_percent'] = $inventoryComponentTransfer->transportation_igst_percent;
            $data['transportation_igst_amount'] = ($inventoryComponentTransfer->transportation_amount * $inventoryComponentTransfer->transportation_igst_percent) / 100;
            $data['driver_name'] = $inventoryComponentTransfer->driver_name;
            $data['mobile'] = $inventoryComponentTransfer->mobile;
            $data['vehicle_number'] = $inventoryComponentTransfer->vehicle_number;
            $data['company_name'] = Vendor::where('id',$inventoryComponentTransfer->vendor_id)->pluck('company')->first();

            if($data['is_material'] == true){
                $data['rate'] = null;
                $data['tax'] = null;
                $data['total_amount'] = null;
            }else{
                $data['rent'] = $inventoryComponentTransfer->bill_amount;
            }
            $pdf = App::make('dompdf.wrapper');
            $pdf->loadHTML(view('inventory.transfer.request-pdf',$data));
            return $pdf->stream();
        }catch(\Exception $e){
            $data = [
                'actions' => 'Generate Inventory Component Transfer PDF',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500,$e->getMessage());
        }
    }

    public function getGRNDetails(Request $request){
        try{
            $inventoryComponentTransfer= InventoryComponentTransfers::where('id',$request['inventory_component_transfer_id'])->first();
            $transportation_cgst_amount = ($inventoryComponentTransfer['transportation_amount'] * $inventoryComponentTransfer['transportation_cgst_percent']) / 100;
            $transportation_sgst_amount = ($inventoryComponentTransfer['transportation_amount'] * $inventoryComponentTransfer['transportation_sgst_percent']) / 100;
            $transportation_igst_amount = ($inventoryComponentTransfer['transportation_amount'] * $inventoryComponentTransfer['transportation_igst_percent']) / 100;
            $response['inventory_component_transfer'] = $inventoryComponentTransfer;
            $response['inventory_component_transfer']['unit'] = Unit::where('id',$inventoryComponentTransfer['unit_id'])->pluck('name')->first();
            $response['inventory_component_transfer']['transportation_tax_amount'] = $transportation_cgst_amount + $transportation_sgst_amount + $transportation_igst_amount;
            $response['inventory_component_transfer']['company_name'] = Vendor::where('id',$inventoryComponentTransfer['vendor_id'])->pluck('company')->first();
            $status = 200;
        }catch(\Exception $e){
            $data = [
                'action' => 'Get GRN Details',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            $status = 500;
            $response = array();
            Log::critical(json_encode($data));
        }
        return response()->json($response,$status);
    }
}
