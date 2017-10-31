<?php

namespace App\Http\Controllers\Inventory;

use App\Client;
use App\InventoryComponent;
use App\InventoryComponentTransferImage;
use App\InventoryComponentTransfers;
use App\InventoryTransferTypes;
use App\Material;
use App\ProjectSite;
use App\Quotation;
use App\Unit;
use App\UnitConversion;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class InventoryManageController extends Controller
{
    public function __construct(){
        $this->middleware('custom.auth');
    }
    public function getManageView(Request $request){
        try{
            return view('inventory/manage');
        }catch(\Exception $e){
            $data = [
                'action' => 'Inventory manage',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
        }
    }

    public function getComponentManageView(Request $request,$inventoryComponent){
        try{
            $clients = Client::join('projects','projects.client_id','=','clients.id')
                            ->join('project_sites','project_sites.project_id','=','projects.id')
                            ->join('quotations','quotations.project_site_id','=','project_sites.id')
                            ->select('clients.company as name','clients.id as id')
                            ->get();
            if($inventoryComponent->is_material == true){
                $materialInfo = Material::where('name','ilike',$inventoryComponent->name)->first();
                if($materialInfo != null){
                    $unit1Ids = UnitConversion::where('unit_1_id',$materialInfo->unit_id)->pluck('unit_2_id')->toArray();
                    $unit2Ids = UnitConversion::where('unit_2_id',$materialInfo->unit_id)->whereNotIn('unit_1_id',$unit1Ids)->pluck('unit_1_id')->toArray();
                    $units = Unit::whereIn('id',$unit1Ids)->whereIn('id',$unit2Ids)->select('id','name')->orderBy('name')->get();
                }else{
                    $units = Unit::where('is_active', true)->select('id','name')->get();
                }
            }else{
                $units = Unit::where('slug','nos')->select('id','name')->get();
            }
            $inTransfers = InventoryTransferTypes::where('type','ilike','IN')->get();
            $inTransferTypes = '<option value=""> -- Select Transfer Type -- </option>';
            foreach($inTransfers as $transfer){
                $inTransferTypes .= '<option value="'.$transfer->slug.'">'.$transfer->name.'</option>';
            }
            $outTransfers = InventoryTransferTypes::where('type','ilike','OUT')->get();
            $outTransferTypes = '<option value=""> -- Select Transfer Type -- </option>';
            foreach($outTransfers as $transfer){
                $outTransferTypes .= '<option value="'.$transfer->slug.'">'.$transfer->name.'</option>';
            }
            return view('inventory/component-manage')->with(compact('inventoryComponent','inTransferTypes','outTransferTypes','units','clients'));
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
                $inQuantity = InventoryComponentTransfers::join('inventory_transfer_types','inventory_transfer_types.id','=','inventory_component_transfers.transfer_type_id')
                                                ->where('inventory_transfer_types.type','ilike','in')
                                                ->where('inventory_component_transfers.inventory_component_id',$inventoryData[$pagination]->id)
                                                ->sum('inventory_component_transfers.quantity');
                $outQuantity = InventoryComponentTransfers::join('inventory_transfer_types','inventory_transfer_types.id','=','inventory_component_transfers.transfer_type_id')
                                                ->where('inventory_transfer_types.type','ilike','out')
                                                ->where('inventory_component_transfers.inventory_component_id',$inventoryData[$pagination]->id)
                                                ->sum('inventory_component_transfers.quantity');
                $availableQuantity = $inQuantity - $outQuantity;
                $records['data'][$iterator] = [
                    $projectName,
                    $inventoryData[$pagination]->name,
                    $inQuantity,
                    $outQuantity,
                    $availableQuantity,
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
                if(strcasecmp( 'IN',$inventoryComponentTransfers[$pagination]->transferType->type)){
                    $transferStatus = 'IN - From '.$inventoryComponentTransfers[$pagination]->transferType->name;
                }else{
                    $transferStatus = 'OUT - To '.$inventoryComponentTransfers[$pagination]->transferType->name;
                }
                $records['data'][$iterator] = [
                    $inventoryComponentTransfers[$pagination]['grn'],
                    $inventoryComponentTransfers[$pagination]['quantity'],
                    $inventoryComponentTransfers[$pagination]->unit->name,
                    $transferStatus,
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
            $data = $request->except(['_token','work_order_images','transfer_type','in_or_out']);
            $data['inventory_component_id'] = $inventoryComponent->id;
            if($request->has('in_or_out')){
                $data['transfer_type_id'] = InventoryTransferTypes::where('type','ilike','IN')->where('slug',$request->transfer_type)->pluck('id')->first();
            }else{
                $data['transfer_type_id'] = InventoryTransferTypes::where('type','ilike','OUT')->where('slug',$request->transfer_type)->pluck('id')->first();
            }
            $currentYearMonthStartFormat = date('Y-m').'-01 00:00:00';
            $currentYearMonthEndFormat = date('Y-m-t',strtotime($currentYearMonthStartFormat)).' 23:59:59';
            $count = InventoryComponentTransfers::where('created_at','>=',$currentYearMonthStartFormat)
                                    ->where('created_at','<=',$currentYearMonthEndFormat)
                                    ->count();
            $data['grn'] = "GRN".date('Ym').($count+1);
            $inventoryComponentTransfer = InventoryComponentTransfers::create($data);
            if($request->has('work_order_images')){
                $imageUploadPath = public_path().env('INVENTORY_COMPONENT_IMAGE_UPLOAD');
                $inventoryComponentDirectoryName = sha1($inventoryComponent->id);
                $inventoryComponentTransferDirectoryName = sha1($inventoryComponentTransfer->id);
                $newImageUploadDirectoryPath = $imageUploadPath.DIRECTORY_SEPARATOR.$inventoryComponentDirectoryName.DIRECTORY_SEPARATOR.'transfers'.DIRECTORY_SEPARATOR.$inventoryComponentTransferDirectoryName;
                if(!file_exists($newImageUploadDirectoryPath)){
                    File::makeDirectory($newImageUploadDirectoryPath,$mode = 0777, true, true);
                }
                foreach ($request->work_order_images as $imagePath){
                    $imagePathChunks = explode('/',$imagePath['image_name']);
                    $fileName = end($imagePathChunks);
                    $inventoryComponentTransferImageData = [
                        'inventory_component_transfer_id' => $inventoryComponentTransfer->id,
                        'name' => $fileName
                    ];
                    File::move(public_path().$imagePath['image_name'],$newImageUploadDirectoryPath.DIRECTORY_SEPARATOR.$fileName);
                    InventoryComponentTransferImage::create($inventoryComponentTransferImageData);
                }
            }
            $request->session()->flash('success','Inventory Component Transfer Saved Successfully!!');
            return redirect('/inventory/component/manage/'.$inventoryComponent->id);
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
            $quotationProjectSiteIds = Quotation::whereNotNull('quotation_status_id')->pluck('project_site_id')->toArray();
            $projectSites = ProjectSite::where('project_id',$projectId)->whereIn('id',$quotationProjectSiteIds)->select('id','name')->get();
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
}
