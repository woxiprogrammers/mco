<?php

namespace App\Http\Controllers\Inventory;

use App\InventoryComponent;
use App\InventoryComponentTransfers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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
            return view('inventory/component-manage')->with(compact('inventoryComponent'));
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
}
