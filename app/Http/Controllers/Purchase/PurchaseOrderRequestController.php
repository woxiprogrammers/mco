<?php

namespace App\Http\Controllers\Purchase;

use App\Asset;
use App\AssetType;
use App\Category;
use App\CategoryMaterialRelation;
use App\Helper\UnitHelper;
use App\Http\Controllers\CustomTraits\Purchase\MaterialRequestTrait;
use App\Material;
use App\MaterialRequestComponentTypes;
use App\MaterialVersion;
use App\PurchaseOrder;
use App\PurchaseOrderComponent;
use App\PurchaseOrderComponentImage;
use App\PurchaseOrderRequest;
use App\PurchaseOrderRequestComponent;
use App\PurchaseOrderRequestComponentImage;
use App\PurchaseOrderStatus;
use App\PurchaseRequest;
use App\PurchaseRequestComponent;
use App\PurchaseRequestComponentVendorMailInfo;
use App\Unit;
use App\User;
use App\Vendor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
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
            $user = Auth::user();
            $purchaseOrderRequestData = [
                'purchase_request_id' => $request->purchase_request_id,
                'user_id' => $user->id
            ];
            $purchaseOrderRequest = PurchaseOrderRequest::create($purchaseOrderRequestData);
            foreach($request['data'] as $purchaseRequestComponentVendorRelationId => $componentData){
                $purchaseOrderRequestComponentData = [
                    'purchase_order_request_id' => $purchaseOrderRequest->id,
                    'purchase_request_component_vendor_relation_id' => $purchaseRequestComponentVendorRelationId,
                    'rate_per_unit' => $componentData['rate_per_unit'],
                    'quantity' => $componentData['quantity'],
                    'unit_id' => $componentData['unit_id'],
                    'hsn_code' => $componentData['hsn_code'],
                    'expected_delivery_date' => $componentData['expected_delivery_date'],
                    'cgst_percentage' => $componentData['cgst_percentage'],
                    'sgst_percentage' => $componentData['sgst_percentage'],
                    'igst_percentage' => $componentData['igst_percentage'],
                    'cgst_amount' => $componentData['cgst_amount'],
                    'sgst_amount' => $componentData['sgst_amount'],
                    'igst_amount' => $componentData['igst_amount'],
                    'total' => $componentData['total'],
                    'category_id' => $componentData['category_id']
                ];
                $purchaseOrderRequestComponent = PurchaseOrderRequestComponent::create($purchaseOrderRequestComponentData);
                if(array_key_exists('client_images',$componentData)){
                    $mainDirectoryName = sha1($purchaseOrderRequest->id);
                    $componentDirectoryName = sha1($purchaseOrderRequestComponent->id);
                    $uploadPath = public_path().env('PURCHASE_ORDER_REQUEST_IMAGE_UPLOAD').DIRECTORY_SEPARATOR.$mainDirectoryName.DIRECTORY_SEPARATOR.'client_approval_images'.DIRECTORY_SEPARATOR.$componentDirectoryName;
                    if (!file_exists($uploadPath)) {
                        File::makeDirectory($uploadPath, $mode = 0777, true, true);
                    }
                    foreach($componentData['client_images'] as $key => $clientImage){
                        $imageArray = explode(';',$clientImage);
                        $image = explode(',',$imageArray[1])[1];
                        $pos  = strpos($clientImage, ';');
                        $type = explode(':', substr($clientImage, 0, $pos))[1];
                        $extension = explode('/',$type)[1];
                        $filename = mt_rand(1,10000000000).sha1(time()).".{$extension}";
                        $fileFullPath = $uploadPath.DIRECTORY_SEPARATOR.$filename;
                        file_put_contents($fileFullPath,base64_decode($image));
                        $imageData = [
                            'purchase_order_request_component_id' => $purchaseOrderRequestComponent['id'] ,
                            'name' => $filename,
                            'caption' => '',
                            'is_vendor_approval' => false
                        ];
                        PurchaseOrderRequestComponentImage::create($imageData);
                    }
                }
                if(array_key_exists('vendor_images',$componentData)){
                    $mainDirectoryName = sha1($purchaseOrderRequest->id);
                    $componentDirectoryName = sha1($purchaseOrderRequestComponent->id);
                    $uploadPath = public_path().env('PURCHASE_ORDER_REQUEST_IMAGE_UPLOAD').DIRECTORY_SEPARATOR.$mainDirectoryName.DIRECTORY_SEPARATOR.'vendor_quotation_images'.DIRECTORY_SEPARATOR.$componentDirectoryName;
                    if (!file_exists($uploadPath)) {
                        File::makeDirectory($uploadPath, $mode = 0777, true, true);
                    }
                    foreach($componentData['vendor_images'] as $key => $vendorImage){
                        $imageArray = explode(';',$vendorImage);
                        $image = explode(',',$imageArray[1])[1];
                        $pos  = strpos($vendorImage, ';');
                        $type = explode(':', substr($vendorImage, 0, $pos))[1];
                        $extension = explode('/',$type)[1];
                        $filename = mt_rand(1,10000000000).sha1(time()).".{$extension}";
                        $fileFullPath = $uploadPath.DIRECTORY_SEPARATOR.$filename;
                        file_put_contents($fileFullPath,base64_decode($image));
                        $imageData = [
                            'purchase_order_request_component_id' => $purchaseOrderRequestComponent['id'] ,
                            'name' => $filename,
                            'caption' => '',
                            'is_vendor_approval' => true
                        ];
                        PurchaseOrderRequestComponentImage::create($imageData);
                    }
                }
            }
            $request->session()->flash('success', "Purchase Order Request Created Successfully.");
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
                $purchaseRequestFormat = PurchaseRequest::where('id',$purchaseOrderRequestsData[$pagination]['purchase_request_id'])->pluck('format_id')->first();
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
            $purchaseOrderRequestComponents = array();
            $draftPurchaseOrderRequestComponents = PurchaseOrderRequestComponent::where('purchase_order_request_id',$purchaseOrderRequest->id)->whereNull('is_approved')->get();
            foreach($draftPurchaseOrderRequestComponents as $purchaseOrderRequestComponent){
                $purchaseRequestComponentId = $purchaseOrderRequestComponent->purchaseRequestComponentVendorRelation->purchase_request_component_id;
                if(!array_key_exists($purchaseOrderRequestComponent->purchaseRequestComponentVendorRelation->purchase_request_component_id,$purchaseOrderRequestComponents)){
                    $purchaseOrderRequestComponents[$purchaseRequestComponentId]['name'] = $purchaseOrderRequestComponent->purchaseRequestComponentVendorRelation->purchaseRequestComponent->materialRequestComponent->name;
                    $purchaseOrderRequestComponents[$purchaseRequestComponentId]['quantity'] = $purchaseOrderRequestComponent->quantity;
                    $purchaseOrderRequestComponents[$purchaseRequestComponentId]['unit'] = $purchaseOrderRequestComponent->unit->name;
                }
                $rateWithTax = $purchaseOrderRequestComponent->rate_per_unit;
                $rateWithTax += ($purchaseOrderRequestComponent->rate_per_unit * ($purchaseOrderRequestComponent->cgst_percentage / 100));
                $rateWithTax += ($purchaseOrderRequestComponent->rate_per_unit * ($purchaseOrderRequestComponent->sgst_percentage / 100));
                $rateWithTax += ($purchaseOrderRequestComponent->rate_per_unit * ($purchaseOrderRequestComponent->igst_percentage / 100));
                $purchaseOrderRequestComponents[$purchaseRequestComponentId]['vendor_relations'][] = [
                    'component_vendor_relation_id' => $purchaseOrderRequestComponent->purchase_request_component_vendor_relation_id,
                    'purchase_order_request_component_id' => $purchaseOrderRequestComponent->id,
                    'vendor_name' => $purchaseOrderRequestComponent->purchaseRequestComponentVendorRelation->vendor->company,
                    'vendor_id' => $purchaseOrderRequestComponent->purchaseRequestComponentVendorRelation->vendor_id,
                    'rate_without_tax' => $purchaseOrderRequestComponent->rate_per_unit,
                    'rate_with_tax' => $rateWithTax,
                    'total_with_tax' => $rateWithTax * $purchaseOrderRequestComponents[$purchaseRequestComponentId]['quantity']
                ];
            }
            return view('purchase.purchase-order-request.approve')->with(compact('purchaseOrderRequest','purchaseOrderRequestComponents'));
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
                    ->join('purchase_request_components','purchase_request_components.purchase_request_id','=','purchase_requests.id')
                    ->where('purchase_requests.project_site_id',$projectSiteId)
                    ->pluck('purchase_request_components.id')
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

    public function getComponentTaxDetails(Request $request, $purchaseRequestComponentVendorRelation){
        try{
            $purchaseRequestComponentData = array();
            $purchaseRequestComponent = $purchaseRequestComponentVendorRelation->purchaseRequestComponent;
            $systemAssetTypeIds = MaterialRequestComponentTypes::whereIn('slug',['system-asset','new-asset'])->pluck('id')->toArray();
            $systemMaterialIds = MaterialRequestComponentTypes::whereIn('slug',['quotation-material','structure-material'])->pluck('id')->toArray();
            if(in_array($purchaseRequestComponent->materialRequestComponent->component_type_id,$systemAssetTypeIds)){
                $purchaseRequestComponentData['categories'] = [
                    'id' => '',
                    'name' => 'Asset'
                ];
                $purchaseRequestComponentData['hsn_code'] = '';
            }elseif(in_array($purchaseRequestComponent->materialRequestComponent->component_type_id,$systemMaterialIds)){
                $purchaseRequestComponentData['categories'] = Material::join('category_material_relations','category_material_relations.material_id','=','materials.id')
                                                                    ->join('categories','category_material_relations.category_id','=','categories.id')
                                                                    ->where('materials.name','ilike',$purchaseRequestComponent->materialRequestComponent->name)
                                                                    ->where('categories.is_active', true)
                                                                    ->select('categories.id as id','categories.name as name')
                                                                    ->get()->toArray();
                $purchaseRequestComponentData['hsn_code'] = Material::where('name','ilike',$purchaseRequestComponent->materialRequestComponent->name)
                                                                    ->pluck('hsn_code')->first();
            }else{
                $purchaseRequestComponentData['categories'] = Category::where('is_miscellaneous', true)->where('is_active', true)->select('id','name')->get()->toArray();
                $purchaseRequestComponentData['hsn_code'] = '';
            }
            $purchaseRequestComponentData['name'] = $purchaseRequestComponent->materialRequestComponent->name;
            $purchaseRequestComponentData['unit'] = $purchaseRequestComponent->materialRequestComponent->unit->name;
            $purchaseRequestComponentData['unit_id'] = $purchaseRequestComponent->materialRequestComponent->unit_id;
            $purchaseRequestComponentData['rate'] = $request->rate;
            $purchaseRequestComponentData['quantity'] = $purchaseRequestComponent->materialRequestComponent->quantity;
            $purchaseRequestComponentData['subtotal'] = $purchaseRequestComponentData['rate'] * $purchaseRequestComponentData['quantity'];
            return view('partials.purchase.purchase-order-request.component-tax-details')->with(compact('purchaseRequestComponentData'));
        }catch(\Exception $e){
            $data = [
                'action' => 'Get Component Tax Details',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            return response()->json([], 500);
        }
    }

    use MaterialRequestTrait;
    public function approvePurchaseOrderRequest(Request $request, $purchaseOrderRequest){
        try{
            $assetComponentTypeIds = MaterialRequestComponentTypes::whereIn('slug',['new-material','system-asset'])->pluck('id')->toArray();
            if($request->has('approved_purchase_order_request_relation')){
                if(Session::has('global_project_site')){
                    $projectSiteId = Session::get('global_project_site');
                }else{
                    $projectSiteId = $purchaseOrderRequest->purchaseOrderRequest->purchaseRequest->project_site_id;
                }
                foreach($request->approved_purchase_order_request_relation as $vendorId => $purchaseOrderRequestComponentArray){
                    $vendorInfo = Vendor::findOrFail($vendorId)->toArray();
                    $vendorInfo['materials'] = array();
                    $purchaseOrderCount = PurchaseOrder::whereDate('created_at', Carbon::now())->count();
                    $purchaseOrderCount++;
                    $purchaseOrderFormatID = $this->getPurchaseIDFormat('purchase-order',$projectSiteId,Carbon::now(),$purchaseOrderCount);
                    $purchaseOrderData = [
                        'user_id' => Auth::user()->id,
                        'vendor_id' => $vendorId,
                        'is_approved' => true,
                        'purchase_request_id' => $purchaseOrderRequest->purchase_request_id,
                        'purchase_order_status_id' => PurchaseOrderStatus::where('slug','open')->pluck('id')->first(),
                        'is_client_order' => false,
                        'purchase_order_request_id' => $purchaseOrderRequest->id,
                        'format_id' => $purchaseOrderFormatID,
                        'serial_no' => $purchaseOrderCount
                    ];
                    $purchaseOrder = PurchaseOrder::create($purchaseOrderData);
                    $iterator = 0;
                    foreach($purchaseOrderRequestComponentArray as $purchaseOrderRequestComponentId){
                        $purchaseOrderRequestComponent = PurchaseOrderRequestComponent::findOrFail($purchaseOrderRequestComponentId);
                        $purchaseOrderComponentData = PurchaseOrderRequestComponent::where('id', $purchaseOrderRequestComponentId)
                                                                ->select('id as purchase_order_request_component_id','rate_per_unit','gst','hsn_code','expected_delivery_date','remark','credited_days',
                                                                    'quantity','unit_id','cgst_percentage','sgst_percentage','igst_percentage','cgst_amount',
                                                                    'sgst_amount','igst_amount','total')
                                                                ->first()->toArray();
                        $purchaseOrderComponentData['purchase_order_id'] = $purchaseOrder->id;
                        $purchaseOrderComponentData['purchase_request_component_id'] = $purchaseOrderRequestComponent->purchaseRequestComponentVendorRelation->purchase_request_component_id;
                        $purchaseOrderComponent = PurchaseOrderComponent::create($purchaseOrderComponentData);
                        $newAssetTypeId = MaterialRequestComponentTypes::where('slug','new-asset')->pluck('id')->first();
                        $newMaterialTypeId = MaterialRequestComponentTypes::where('slug','new-material')->pluck('id')->first();
                        $componentTypeId = $purchaseOrderComponent->purchaseRequestComponent->materialRequestComponent->component_type_id;
                        $vendorInfo['materials'][$iterator]['item_name'] = $purchaseOrderComponent->purchaseRequestComponent->materialRequestComponent->name;
                        $vendorInfo['materials'][$iterator]['quantity'] = $purchaseOrderComponent['quantity'];
                        $vendorInfo['materials'][$iterator]['unit'] = Unit::where('id',$purchaseOrderComponent['unit_id'])->pluck('name')->first();
                        $vendorInfo['materials'][$iterator]['hsn_code'] = $purchaseOrderComponent['hsn_code'];
                        $vendorInfo['materials'][$iterator]['rate'] = $purchaseOrderComponent['rate'];
                        if($newMaterialTypeId == $componentTypeId){
                            $materialName = $purchaseOrderComponent->purchaseRequestComponent->materialRequestComponent->name;
                            $isMaterialExists = Material::where('name','ilike',$materialName)->first();
                            if($isMaterialExists == null){
                                $materialData = [
                                    'name' => $purchaseOrderComponent->purchaseRequestComponent->materialRequestComponent->name,
                                    'is_active' => true,
                                    'rate_per_unit' => $purchaseOrderComponent->rate_per_unit,
                                    'unit_id' => $purchaseOrderComponent->unit_id,
                                    'hsn_code' => $purchaseOrderComponent->hsn_code,
                                    'gst' => $purchaseOrderComponent->gst
                                ];
                                $material = Material::create($materialData);
                                $categoryMaterialData = [
                                    'material_id' => $material->id,
                                    'category_id' => $purchaseOrderRequestComponent->category_id
                                ];
                                CategoryMaterialRelation::create($categoryMaterialData);
                                $materialVersionData = [
                                    'material_id' => $material->id,
                                    'rate_per_unit' => $material->rate_per_unit,
                                    'unit_id' => $material->unit_id
                                ];
                                MaterialVersion::create($materialVersionData);
                            }
                        }elseif ($newAssetTypeId == $componentTypeId){
                            $assetName = $purchaseOrderComponent->purchaseRequestComponent->materialRequestComponent->name;
                            $is_present = Asset::where('name','ilike',$assetName)->pluck('id')->toArray();
                            if($is_present == null){
                                $asset_type = AssetType::where('slug','other')->pluck('id')->first();
                                $categoryAssetData = array();
                                $categoryAssetData['asset_types_id'] = $asset_type;
                                $categoryAssetData['name'] = $assetName;
                                $categoryAssetData['quantity'] = 1;
                                $categoryAssetData['is_fuel_dependent'] = false;
                                Asset::create($categoryAssetData);
                            }
                        }
                        if(in_array($purchaseOrderComponent->purchaseRequestComponent->materialRequestComponent->component_type_id,$assetComponentTypeIds)){
                            $vendorInfo['materials'][$iterator]['gst'] = '-';
                        }else{
                            $vendorInfo['materials'][$iterator]['gst'] = Material::where('name','ilike',$purchaseOrderComponent->purchaseRequestComponent->materialRequestComponent->name)->pluck('gst')->first();
                            if($vendorInfo['materials'][$iterator]['gst'] == null){
                                $vendorInfo['materials'][$iterator]['gst'] = '-';
                            }
                        }
                        $purchaseOrderRequestComponent->update(['is_approved' => true]);
                        $disapprovedPurchaseOrderRequestComponentIds = PurchaseOrderRequestComponent::join('purchase_request_component_vendor_relation','purchase_request_component_vendor_relation.id','=','purchase_order_request_components.purchase_request_component_vendor_relation_id')
                                ->where('purchase_request_component_vendor_relation.purchase_request_component_id',$purchaseOrderRequestComponent->purchase_request_component_id)
                                ->where('purchase_order_request_components.id','!=',$purchaseOrderRequestComponent->id)
                                ->pluck('purchase_order_request_components.id')
                                ->toArray();
                        PurchaseOrderRequestComponent::whereIn('id', $disapprovedPurchaseOrderRequestComponentIds)->update(['is_approved' => false]);
                        if(count($purchaseOrderRequestComponent->purchaseOrderRequestComponentImages) > 0){
                            $purchaseOrderMainDirectoryName = sha1($purchaseOrderComponent['purchase_order_id']);
                            $purchaseOrderComponentDirectoryName = sha1($purchaseOrderComponent['id']);
                            $mainDirectoryName = sha1($purchaseOrderRequest->id);
                            $componentDirectoryName = sha1($purchaseOrderRequestComponent->id);
                            foreach ($purchaseOrderRequestComponent->purchaseOrderRequestComponentImages as $purchaseOrderRequestComponentImage){
                                if($purchaseOrderRequestComponentImage->is_vendor_approval == true){
                                    $toUploadPath = public_path().env('PURCHASE_ORDER_IMAGE_UPLOAD').DIRECTORY_SEPARATOR.$purchaseOrderMainDirectoryName.DIRECTORY_SEPARATOR.'vendor_quotation_images'.DIRECTORY_SEPARATOR.$purchaseOrderComponentDirectoryName;
                                    $fromUploadPath = public_path().env('PURCHASE_ORDER_REQUEST_IMAGE_UPLOAD').DIRECTORY_SEPARATOR.$mainDirectoryName.DIRECTORY_SEPARATOR.'vendor_quotation_images'.DIRECTORY_SEPARATOR.$componentDirectoryName;
                                }else{
                                    $toUploadPath = public_path().env('PURCHASE_ORDER_IMAGE_UPLOAD').DIRECTORY_SEPARATOR.$purchaseOrderMainDirectoryName.DIRECTORY_SEPARATOR.'client_approval_images'.DIRECTORY_SEPARATOR.$purchaseOrderComponentDirectoryName;
                                    $fromUploadPath = public_path().env('PURCHASE_ORDER_REQUEST_IMAGE_UPLOAD').DIRECTORY_SEPARATOR.$mainDirectoryName.DIRECTORY_SEPARATOR.'client_approval_images'.DIRECTORY_SEPARATOR.$componentDirectoryName;
                                }
                                if (!file_exists($toUploadPath)) {
                                    File::makeDirectory($toUploadPath, $mode = 0777, true, true);
                                }
                                $fromUploadPath = $fromUploadPath.DIRECTORY_SEPARATOR.$purchaseOrderRequestComponentImage->name;
                                $toUploadPath = $toUploadPath.DIRECTORY_SEPARATOR.$purchaseOrderRequestComponentImage->name;
                                if(file_exists($fromUploadPath)){
                                    $imageData = [
                                        'purchase_order_component_id' => $purchaseOrderComponent['id'] ,
                                        'name' => $purchaseOrderRequestComponentImage->name,
                                        'caption' => $purchaseOrderRequestComponentImage->caption,
                                        'is_vendor_approval' => $purchaseOrderRequestComponentImage->is_vendor_approval
                                    ];
                                    File::move($fromUploadPath, $toUploadPath);
                                    PurchaseOrderComponentImage::create($imageData);
                                }
                            }
                        }
                    }
                    if(count($vendorInfo['materials']) > 0){
                        $pdf = App::make('dompdf.wrapper');
                        $pdf->loadHTML(view('purchase.purchase-request.pdf.vendor-quotation')->with(compact('vendorInfo','projectSiteInfo','pdfFlag')));
                        $pdfDirectoryPath = env('PURCHASE_VENDOR_ASSIGNMENT_PDF_FOLDER');
                        $pdfFileName = sha1($vendorId).'.pdf';
                        $pdfUploadPath = public_path().$pdfDirectoryPath.'/'.$pdfFileName;
                        $pdfContent = $pdf->stream();
                        if(file_exists($pdfUploadPath)){
                            unlink($pdfUploadPath);
                        }
                        if (!file_exists($pdfDirectoryPath)) {
                            File::makeDirectory(public_path().$pdfDirectoryPath, $mode = 0777, true, true);
                        }
                        file_put_contents($pdfUploadPath,$pdfContent);
                        $mailData = ['path' => $pdfUploadPath, 'toMail' => $vendorInfo['email']];
                        Mail::send('purchase.purchase-request.email.vendor-quotation', [], function($message) use ($mailData){
                            $message->subject('Testing with attachment');
                            $message->to($mailData['toMail']);
                            $message->from(env('MAIL_USERNAME'));
                            $message->attach($mailData['path']);
                        });
                        $mailInfoData = [
                            'user_id' => Auth::user()->id,
                            'type_slug' => 'for-purchase-order',
                            'vendor_id' => $purchaseOrder->vendor_id,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now()
                        ];
                        PurchaseRequestComponentVendorMailInfo::insert($mailInfoData);
                        unlink($pdfUploadPath);
                    }
                }
                $request->session()->flash('success','Purchase Orders Created Successfully !');
                return redirect('/purchase/purchase-order/manage');
            }else{
                $request->session()->flash('error','Please select at least one material/asset.');
                return redirect('/purchase/purchase-order-request/edit/'.$purchaseOrderRequest->id);
            }
        }catch(\Exception $e){
            $data = [
                'action' => 'Approve Purchase Order Request',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }
}
