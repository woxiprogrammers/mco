<?php

namespace App\Http\Controllers\Inventory;

use App\Asset;
use App\AssetType;
use App\Client;
use App\FuelAssetReading;
use App\Helper\UnitHelper;
use App\Http\Controllers\CustomTraits\Inventory\InventoryTrait;
use App\InventoryComponent;
use App\InventoryComponentTransferImage;
use App\InventoryComponentTransfers;
use App\InventoryComponentTransferStatus;
use App\InventoryTransferTypes;
use App\Material;
use App\MaterialRequestComponents;
use App\ProjectSite;
use App\ProjectSiteUserCheckpoint;
use App\PurchaseOrderComponent;
use App\PurchaseRequestComponentStatuses;
use App\Quotation;
use App\QuotationMaterial;
use App\Unit;
use App\UnitConversion;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class InventoryManageController extends Controller
{
    use InventoryTrait;
    public function __construct(){
        $this->middleware('custom.auth');
    }
    public function getManageView(Request $request){
        try{
            $projectSites  = ProjectSite::join('projects','projects.id','=','project_sites.project_id')
                ->where('projects.is_active',true)->select('project_sites.id','project_sites.name','projects.name as project_name')->get()->toArray();
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
                }else{
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
                }
                $records['data'][$iterator] = [
                    $inventoryTransferData[$pagination]->inventoryComponent->projectSite->name,
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
            $newInventoryComponent = InventoryComponent::where('project_site_id',$request->project_site_id)->where('name','ilike',trim($request['name']))->first();
            if($newInventoryComponent == null){
                $inventoryComponentData['name'] = $request['name'];
                $inventoryComponentData['is_material'] = ($request['inventory_type'] == 'material') ? true : false;
                $inventoryComponentData['project_site_id'] = $request->project_site_id;
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
                    ->where('material_request_components.name',$inventoryComponent['name'])
                    ->orderBy('purchase_order_components.id','desc')
                    ->select('purchase_order_components.rate_per_unit','purchase_order_components.cgst_percentage','purchase_order_components.cgst_amount')
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
            $asset_types = AssetType::select('slug','name')->get()->toArray();
            return view('inventory/component-manage')->with(compact('inventoryComponent','inTransferTypes','outTransferTypes','units','clients','isReadingApplicable','nosUnitId','projectInfo','asset_types','amount'));
        }catch(\Exception $e){
            $data = [
                'action' => 'Inventory manage',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
        }
    }

    public function inventoryListing(Request $request){
        try{
            $status = 200;
            if($request->has('search_name')){
                // Inventory listing search
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
                }else{
                    $transferStatus = 'OUT - To '.$inventoryComponentTransfers[$pagination]->transferType->name;
                }
                $records['data'][$iterator] = [
                    $inventoryComponentTransfers[$pagination]['grn'],
                    $inventoryComponentTransfers[$pagination]['quantity'],
                    $inventoryComponentTransfers[$pagination]->unit->name,
                    $transferStatus,
                    $inventoryComponentTransfers[$pagination]->inventoryComponentTransferStatus->name,
                    '<a href="javascript:void(0);" class="btn btn-xs green dropdown-toggle" type="button" aria-expanded="true" onclick="openDetails('.$inventoryComponentTransfers[$pagination]->id.')">
                        Details
                    </a>'
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

    public function getInventoryComponentTransferDetail(Request $request,$inventoryComponentTransfer){
        try{
            if(count($inventoryComponentTransfer->images) > 0){
                $inventoryComponentTransferImages = $this->getTransferImages($inventoryComponentTransfer);
            }else{
                $inventoryComponentTransferImages = array();
            }
            return view('partials.inventory.inventory-component-transfer-detail')->with(compact('inventoryComponentTransfer','inventoryComponentTransferImages'));
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

    public function addComponentTransfer(Request $request,$inventoryComponent){
        try{
            $data = $request->except(['_token','work_order_images','project_site_id']);
            $data['inventory_component_id'] = $inventoryComponent->id;
            $data['date'] = Carbon::now();
            if($request->has('project_site_id') && $request->transfer_type =='site'){
                $projectSite = ProjectSite::where('id',$request['project_site_id'])->first();
                $data['source_name'] = $projectSite->project->name.'-'.$projectSite->name;
                if($request->has('in_or_out')){
                    $data['inventory_component_transfer_status_id'] = InventoryComponentTransferStatus::where('slug','approved')->pluck('id')->first();
                    $data = $request->only('rate_per_unit','cgst_percentage','sgst_percentage','igst_percentage','cgst_amount','sgst_amount','igst_amount','total');
                }else{
                    $data['inventory_component_transfer_status_id'] = InventoryComponentTransferStatus::where('slug','requested')->pluck('id')->first();
                    $data['rate_per_unit'] = $request['rate_per_unit'];
                }
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
            if(array_key_exists('top_up',$data)){
                $inventoryTransferData = [
                    'inventory_component_id' => $dieselcomponentId,
                    'transfer_type_id' => InventoryTransferTypes::where('slug','labour')->where('type','ilike','OUT')->pluck('id')->first(),
                    'quantity' => $data['top_up'],
                    'unit_id' => Unit::where('slug','litre')->pluck('id')->first(),
                    'source_name' => $user->first_name.' '.$user->last_name,
                    'user_id' => $user->id
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

    public function autoSuggest(Request $request,$projectSiteId,$type,$keyword){
        try{
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
}
