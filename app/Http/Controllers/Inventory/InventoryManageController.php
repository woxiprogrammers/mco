<?php

namespace App\Http\Controllers\Inventory;

use App\InventoryComponent;
use App\InventoryComponentTransfers;
use App\InventoryTransferTypes;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
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
            return view('inventory/component-manage')->with(compact('inventoryComponent','inTransferTypes','outTransferTypes'));
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
            return view('partials.inventory.inventory-component-transfer-detail')->with(compact('inventoryComponentTransfer'));
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

    public function uploadTempWorkOrderImages(Request $request,$inventoryComponentId){
        try{
            /*$user = Auth::user();
            $userDirectoryName = sha1($user->id);
            $inventoryComponentDirectoryName = sha1($inventoryComponentId);
            $tempUploadPath = public_path().env('WORK_ORDER_TEMP_IMAGE_UPLOAD');
            $tempImageUploadPath = $tempUploadPath.DIRECTORY_SEPARATOR.$quotationDirectoryName;
            /* Create Upload Directory If Not Exists
            if (!file_exists($tempImageUploadPath)) {
                File::makeDirectory($tempImageUploadPath, $mode = 0777, true, true);
            }
            $extension = $request->file('file')->getClientOriginalExtension();
            $filename = mt_rand(1,10000000000).sha1(time()).".{$extension}";
            $request->file('file')->move($tempImageUploadPath,$filename);
            $path = env('WORK_ORDER_TEMP_IMAGE_UPLOAD').DIRECTORY_SEPARATOR.$quotationDirectoryName.DIRECTORY_SEPARATOR.$filename;
            $response = [
                'jsonrpc' => '2.0',
                'result' => 'OK',
                'path' => $path
            ];*/
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

    public function displayWorkOrderImages(Request $request){
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
}
