<?php

namespace App\Http\Controllers\Purchase;

use App\Asset;
use App\Helper\UnitHelper;
use App\Material;
use App\MaterialRequestComponentTypes;
use App\PurchaseOrder;
use App\PurchaseOrderComponent;
use App\PurchaseOrderRequest;
use App\PurchaseOrderRequestComponent;
use App\PurchaseRequest;
use App\PurchaseRequestComponent;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class PurchaseOrderRequestController extends Controller
{
    public function __construct(){
        $this->middleware('custom.auth');
    }

    public function getManageView(Request $request){
        try{
            return view('purchase.purchase-order-request.manage');
        }catch(\Exception $e){
            $data = [
                'action' => 'Get purchase order request manage view',
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getCreateView(Request $request){
        try{
            return view('purchase.purchase-order-request.create');
        }catch(\Exception $e){
            $data = [
                'action' => 'Get purchase order request create view',
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function createPurchaseOrderRequest(Request $request){
        try{
            return redirect('/purchase/purchase-order-request/manage');
        }catch(\Exception $e){
            $data = [
                'action' => 'Create purchase order request',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function listing(Request $request){
        try{
            if(Session::has('global_project_site')){
                $projectSiteId = Session::get('global_project_site');
                $purchaseOrderRequestsData = PurchaseOrderRequest::join('purchase_requests','purchase_requests.id','=','purchase_order_requests.purchase_request_id')
                                                        ->where('purchase_requests.project_site_id', $projectSiteId)
                                                        ->select('purchase_order_requests.id as id','purchase_order_requests.purchase_request_id as purchase_request_id','purchase_order_requests.user_id as user_id')
                                                        ->get();
            }else{
                $purchaseOrderRequestsData = PurchaseOrderRequest::get();
            }
            $records = array();
            $records['data'] = array();
            $records["draw"] = intval($request->draw);
            $records["recordsTotal"] = $records["recordsFiltered"] = count($purchaseOrderRequestsData);
            $end = $request->length < 0 ? count($purchaseOrderRequestsData) : $request->length;
            for($iterator = 0,$pagination = $request->start; $iterator < $end && $pagination < count($purchaseOrderRequestsData); $iterator++,$pagination++ ){
                $user = User::where('id',$purchaseOrderRequestsData[$pagination]['user_id'])->select('first_name','last_name')->first();
                $purchaseRequestFormat = PurchaseRequest::where('id',$purchaseOrderRequestsData[$pagination])->pluck('format_id')->first();
                $records['data'][] = [
                    $iterator+1,
                    $purchaseRequestFormat,
                    $user['first_name'].' '.$user['last_name'],
                    '<div class="btn-group">
                            <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                Actions
                                <i class="fa fa-angle-down"></i>
                            </button>
                            <ul class="dropdown-menu pull-left" role="menu">
                                <li>
                                    <a href="/purchase/purchase-order-request/edit/'.$purchaseOrderRequestsData[$pagination]['id'].'">
                                    <i class="icon-docs"></i> Edit </a>
                                </li>
                            </ul>
                        </div>'
                ];
            }
            $status = 200;
        }catch(\Exception $e){
            $data = [
                'action' => 'Purchase order request Listing',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            $records = array();
            $status = 500;
        }
        return response()->json($records,$status);
    }

    public function getEditView(Request $request,$purchaseOrderRequest){
        try{
            return view('purchase.purchase-order-request.edit');
        }catch(\Exception $e){
            $data = [
                'action' => 'Edit purchase order request',
                'params' => $request->all(),
                'purchase-order-request'=>$purchaseOrderRequest,
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function purchaseRequestAutoSuggest(Request $request,$keyword){
        try{
            if(Session::has('global_project_site')){
                $projectSiteId = Session::get('global_project_site');
                $purchaseOrderCreatedComponentIds = PurchaseOrderRequestComponent::join('purchase_order_requests','purchase_order_requests.id','=','purchase_order_request_components.purchase_order_request_id')
                    ->join('purchase_requests','purchase_requests.id','=','purchase_order_requests.purchase_request_id')
                    ->where('purchase_requests.project_site_id',$projectSiteId)
                    ->pluck('purchase_order_request_components.purchase_request_component_id')
                    ->toArray();
                $purchaseRequests = PurchaseRequest::join('purchase_request_components','purchase_request_components.purchase_request_id','=','purchase_requests.id')
                    ->join('purchase_request_component_vendor_relation','purchase_request_component_vendor_relation.purchase_request_component_id','=','purchase_request_components.id')
                    ->whereNotIn('purchase_request_components.id',$purchaseOrderCreatedComponentIds)
                    ->where('purchase_requests.format_id','ilike','%'.$keyword.'%')
                    ->where('purchase_requests.project_site_id',$projectSiteId)
                    ->select('purchase_requests.id as id','purchase_requests.format_id as format_id')
                    ->distinct('format_id')
                    ->get()
                    ->toArray();
            }else{
                $purchaseOrderCreatedComponentIds = PurchaseOrderRequestComponent::join('purchase_order_requests','purchase_order_requests.id','=','purchase_order_request_components.purchase_order_request_id')
                    ->join('purchase_requests','purchase_requests.id','=','purchase_order_requests.purchase_request_id')
                    ->pluck('purchase_order_request_components.purchase_request_component_id')
                    ->toArray();
                $purchaseRequests = PurchaseRequest::join('purchase_request_components','purchase_request_components.purchase_request_id','=','purchase_requests.id')
                    ->join('purchase_request_component_vendor_relation','purchase_request_component_vendor_relation.purchase_request_component_id','=','purchase_request_components.id')
                    ->whereNotIn('purchase_request_components.id',$purchaseOrderCreatedComponentIds)
                    ->where('purchase_requests.format_id','ilike','%'.$keyword.'%')
                    ->select('purchase_requests.id as id','purchase_requests.format_id as format_id')
                    ->distinct('format_id')
                    ->get()
                    ->toArray();
            }
            $status = 200;
        }catch (\Exception $e){
            $data = [
                'action' => 'Purchase Request Auto Suggest',
                'keyword' => $keyword,
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            $status = 500;
            $purchaseRequests = array();
        }
        return response()->json($purchaseRequests,$status);
    }

    public function getPurchaseRequestComponentDetails(Request $request){
        try{
            $purchaseRequestComponents = PurchaseRequestComponent::where('purchase_request_id', $request->purchase_request_id)->get();
            $iterator = 0;
            $purchaseRequestComponentData = array();
            foreach($purchaseRequestComponents as $purchaseRequestComponent){
                foreach ($purchaseRequestComponent->vendorRelations as $vendorRelation){
                    $purchaseRequestComponentData[$iterator]['vendor_relation_id'] = $vendorRelation->id;
                    $purchaseRequestComponentData[$iterator]['purchase_request_component_id'] = $purchaseRequestComponent->id;
                    $purchaseRequestComponentData[$iterator]['vendor_name'] = $vendorRelation->vendor->company;
                    $purchaseRequestComponentData[$iterator]['name'] = $purchaseRequestComponent->materialRequestComponent->name;
                    $purchaseRequestComponentData[$iterator]['quantity'] = $purchaseRequestComponent->materialRequestComponent->quantity;
                    $purchaseRequestComponentData[$iterator]['unit'] = $purchaseRequestComponent->materialRequestComponent->unit->name;
                    $purchaseRequestComponentData[$iterator]['unit_id'] = $purchaseRequestComponent->materialRequestComponent->unit_id;
                    $lastPurchaseOrderRateInfo = PurchaseOrderComponent::join('purchase_request_components','purchase_request_components.id','=','purchase_order_components.purchase_request_component_id')
                                                            ->join('material_request_components','material_request_components.id','=','purchase_request_components.material_request_component_id')
                                                            ->join('purchase_orders','purchase_orders.id','=','purchase_order_components.purchase_order_id')
                                                            ->where('material_request_components.name','ilike', $purchaseRequestComponentData[$iterator]['name'])
                                                            ->where('purchase_orders.is_approved', true)
                                                            ->orderBy('purchase_orders.created_at','desc')
                                                            ->select('purchase_order_components.rate_per_unit as rate_per_unit','purchase_order_components.unit_id as unit_id')
                                                            ->first();
                    if($lastPurchaseOrderRateInfo == null){
                        $systemAssetTypeId = MaterialRequestComponentTypes::where('slug','system-asset')->pluck('id')->first();
                        $materialTypeIds = MaterialRequestComponentTypes::whereIn('slug',['quotation-material','structure-material'])->pluck('id')->toArray();
                        if($purchaseRequestComponent->materialRequestComponent->component_type_id == $systemAssetTypeId){
                            $lastPurchaseOrderRate = Asset::where('name','ilike',$purchaseRequestComponentData[$iterator]['name'])->pluck('price')->first();
                        }elseif(in_array($purchaseRequestComponent->materialRequestComponent->component_type_id,$materialTypeIds)){
                            $materialInfo = Material::where('name','ilike',$purchaseRequestComponentData[$iterator]['name'])->select('id','rate_per_unit','unit_id')->first();
                            $lastPurchaseOrderRate = UnitHelper::unitConversion($materialInfo['unit_id'],$purchaseRequestComponentData[$iterator]['unit_id'],$materialInfo['rate_per_unit']);
                        }else{
                            $lastPurchaseOrderRate = 0;
                        }
                    }else{
                        $lastPurchaseOrderRate = UnitHelper::unitConversion($lastPurchaseOrderRateInfo['unit_id'],$purchaseRequestComponentData[$iterator]['unit_id'],$lastPurchaseOrderRateInfo['rate_per_unit']);
                    }
                    $purchaseRequestComponentData[$iterator]['rate_per_unit'] = $lastPurchaseOrderRate;
                    $iterator++;
                }
            }
            return view('partials.purchase.purchase-order-request.component-listing')->with(compact('purchaseRequestComponentData'));
        }catch (\Exception $e){
            $data = [
                'action' => 'Get Purchase Request Component Details',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            return response()->json([], 500);
        }
    }
}
