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

    public function getCreateView(Request $request){
        return view('inventory/create');
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
                        <a href="/inventory/create" style="color: white">
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
}
