<?php

namespace App\Http\Controllers\User;
use App\Asset;
use App\Client;
use App\Http\Controllers\CustomTraits\Notification\NotificationTrait;
use App\Http\Controllers\CustomTraits\Purchase\MaterialRequestTrait;
use App\Material;
use App\MaterialRequestComponentHistory;
use App\MaterialRequestComponents;
use App\MaterialRequestComponentTypes;
use App\MaterialRequestComponentVersion;
use App\MaterialRequests;
use App\MaterialVersion;
use App\Project;
use App\ProjectSite;
use App\PurchaseOrderComponent;
use App\PurchaseRequestComponentStatuses;
use App\Quotation;
use App\QuotationMaterial;
use App\QuotationProduct;
use App\QuotationStatus;
use App\Unit;
use App\UnitConversion;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class PurchaseController extends Controller
{
    use MaterialRequestTrait;
    public function __construct()
    {
        $this->middleware('custom.auth');
    }
    public function getManageView(Request $request){
        try{
            $purchaseStatus = PurchaseRequestComponentStatuses::get()->toArray();
            $units = Unit::where('is_active', true)->select('id','name')->get();
            return view('purchase/material-request/manage')->with(compact('units','purchaseStatus'));
        }catch(\Exception $e){
            $data = [
                'action' => 'Get Material Request manage page',
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getCreateView(Request $request){
        $user = Auth::user();
        $userData = array(
            "id" => $user['id'],
            "username" => $user['first_name']." ".$user['last_name']
        );
        $nosUnitId = Unit::where('slug','nos')->pluck('id')->first();
        $units = Unit::select('id','name')->get()->toArray();
        $unitOptions = '';
        foreach($units as $unit){
            $unitOptions .= '<option value="'.$unit['id'].'">'.$unit['name'].'</option>';
        }
        return view('purchase/material-request/create')->with(compact('nosUnitId','units','unitOptions','userData'));
    }

    public function getMaterialRequestListing(Request $request){
        try{
            $postdata = null;
            $m_name = "";
            $m_id = "";
            $status = 0;
            $site_id = 0;
            $month = 0;
            $year = 0;
            $m_count = 0;
            $postDataArray = array();
            if ($request->has('m_name')) {
                if ($request['m_name'] != "") {
                    $m_name = $request['m_name'];
                }
            }

            if ($request->has('m_id')) {
                if ($request['m_id'] != "") {
                    $m_id = $request['m_id'];
                }
            }

            if ($request->has('status')) {
                $status = $request['status'];
            }
            if($request->has('postdata')) {
                $postdata = $request['postdata'];
                if($postdata != null) {
                    $mstr = explode(",",$request['postdata']);
                    foreach($mstr as $nstr)
                    {
                        $narr = explode("=>",$nstr);
                        $narr[0] = str_replace("\x98","",$narr[0]);
                        $ytr[1] = $narr[1];
                        $postDataArray[$narr[0]] = $ytr[1];
                    }
                }
                $month = $postDataArray['month'];
                $year = $postDataArray['year'];
                $m_count = $postDataArray['m_count'];
            }
            if($request->has('site_id')){
                $site_id = $request->site_id;
            }
            $materialRequests = array();
            $ids = MaterialRequests::all()->pluck('id');
            $filterFlag = true;
            if ($site_id != 0 && $filterFlag == true) {
                $ids = MaterialRequests::whereIn('id',$ids)->where('project_site_id', $site_id)->pluck('id');
                if(count($ids) <= 0) {
                    $filterFlag = false;
                }
            }
            if ($year != 0 && $filterFlag == true) {
                $ids = MaterialRequests::whereIn('id',$ids)->whereYear('created_at', $year)->pluck('id');
                if(count($ids) <= 0) {
                    $filterFlag = false;
                }
            }
            if ($month != 0 && $filterFlag == true) {
                $ids = MaterialRequests::whereIn('id',$ids)->whereMonth('created_at', $month)->pluck('id');
                if(count($ids) <= 0) {
                    $filterFlag = false;
                }
            }

            if ($status != 0 && $filterFlag == true) {
               $ids = MaterialRequests::join('material_request_components','material_request_components.material_request_id','=','material_requests.id')
                   ->where('material_request_components.component_status_id',$status)
                   ->whereIn('material_requests.id',$ids)->distinct('material_requests.id')->pluck('material_requests.id');
                if(count($ids) <= 0) {
                    $filterFlag = false;
                }
            }
            if ($m_name != "" && $filterFlag == true) {
                $ids = MaterialRequests::join('material_request_components','material_request_components.material_request_id','=','material_requests.id')
                    ->where('material_request_components.name','ilike','%'.$m_name.'%')
                    ->whereIn('material_requests.id',$ids)->distinct('material_requests.id')->pluck('material_requests.id');
                if(count($ids) <= 0) {
                    $filterFlag = false;
                }
            }

            if ($m_id != "" && $filterFlag == true) {
                $ids = MaterialRequests::whereIn('id',$ids)->where('format_id','ilike','%'.$m_id.'%')->pluck('id');
                if(count($ids) <= 0) {
                    $filterFlag = false;
                }
            }

            if ($m_count != 0 && $filterFlag == true) {
                $ids = MaterialRequests::whereIn('id',$ids)->where('serial_no', $m_count)->pluck('id');
                if(count($ids) <= 0) {
                    $filterFlag = false;
                }
            }

            if ($filterFlag) {
                $materialRequests = MaterialRequests::whereIn('id',$ids)->orderBy('id','desc')->get();
            }

            $materialRequestList = array();
            $iterator = 0;
            foreach($materialRequests as $key => $materialRequest){
                $materialRequestComponentArray = MaterialRequestComponents::where('material_request_id',$materialRequest->id)->orderBy('id','desc')->get();
                foreach($materialRequestComponentArray as $key => $materialRequestComponents){
                    if($materialRequestComponents->component_status_id == $status || $status == 0) {
                        $materialRequestList[$iterator]['material_request_component_id'] = $materialRequestComponents->id;
                        $materialRequestList[$iterator]['name'] = $materialRequestComponents->name;
                        $materialRequestList[$iterator]['quantity'] = $materialRequestComponents->quantity;
                        $materialRequestList[$iterator]['unit_id'] = $materialRequestComponents->unit_id;
                        $materialRequestList[$iterator]['unit'] = $materialRequestComponents->unit->name;
                        $materialRequestList[$iterator]['component_type_id'] = $materialRequestComponents->component_type_id;
                        $materialRequestList[$iterator]['component_type'] = $materialRequestComponents->materialRequestComponentTypes->name;
                        $materialRequestList[$iterator]['component_status_id'] = $materialRequestComponents->component_status_id;
                        $materialRequestList[$iterator]['component_status'] = $materialRequestComponents->purchaseRequestComponentStatuses->slug;
                        $materialRequestList[$iterator]['component_status_name'] = $materialRequestComponents->purchaseRequestComponentStatuses->name;
                        $materialRequestList[$iterator]['project_site_id'] =$materialRequest['project_site_id'];
                        $pro = $materialRequest->projectSite->project;
                        $materialRequestList[$iterator]['project_name'] =$pro->name;
                        $materialRequestList[$iterator]['client_name'] =$pro->client->company;
                        $materialRequestList[$iterator]['site_name'] =$materialRequest->projectSite->name;
                        $materialRequestList[$iterator]['created_at'] =$materialRequest['created_at'];
                        $materialRequestList[$iterator]['rm_id'] = $this->getPurchaseIDFormat('material-request-component',$materialRequest['project_site_id'],$materialRequestComponents['created_at'],$materialRequestComponents->serial_no);
                        $materialRequestList[$iterator]['mr_id'] = $this->getPurchaseIDFormat('material-request',$materialRequest['project_site_id'],$materialRequest['created_at'],$materialRequest->serial_no);
                        $iterator++;

                    }
                }
            }
            $iTotalRecords = count($materialRequestList);
            if($request->length == -1){
                $length = $iTotalRecords;
            }else{
                $length = $request->length;
            }

            $records = array();
            $user = Auth::user();
            $records['data'] = array();
            $assetComponentTypeIds = MaterialRequestComponentTypes::whereIn('slug',['system-asset','new-asset'])->pluck('id')->toArray();
            for($iterator = 0,$pagination = $request->start; $iterator < $length && $pagination < count($materialRequestList); $iterator++,$pagination++ ){
                switch(strtolower($materialRequestList[$pagination]['component_status'])){
                    case 'pending':
                        if(in_array($materialRequestList[$pagination]['component_type_id'],$assetComponentTypeIds)){
                          $unitEditable = 'false';
                        }else{
                            $unitEditable = 'true';
                        }
                        $checkboxComponent = '<input type="checkbox" class="multiple-select-checkbox" value="'.$materialRequestList[$pagination]['material_request_component_id'].'">';
                        $checkboxComponentMoveToIndent = '<input type="checkbox" class="multiple-select-checkbox-mti" value="'.$materialRequestList[$pagination]['material_request_component_id'].'" disabled>';
                        $user_status = '<td><span class="label label-sm label-danger">'. $materialRequestList[$pagination]['component_status_name'].' </span></td>';
                        $actionDropDown = '<div class="btn-group">
                            <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                Actions
                                <i class="fa fa-angle-down"></i>
                            </button>                      
                            <ul class="dropdown-menu pull-left" role="menu">';
                        if($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin' || $user->customHasPermission('approve-material-request')){
                            $actionDropDown .= '<li>
                                    <a href="javascript:void(0);" onclick="openApproveModal('.$materialRequestList[$pagination]['material_request_component_id'].')">
                                        <i class="icon-tag"></i> Approve / Disapprove 
                                    </a>
                                </li>';
                        }
                        $actionDropDown .= '</ul>
                            </div>';
                        break;

                    case 'admin-approved':
                        $checkboxComponent = '<input type="checkbox" class="multiple-select-checkbox" value="'.$materialRequestList[$pagination]['material_request_component_id'].'" disabled>';
                        $checkboxComponentMoveToIndent = '<input type="checkbox" class="multiple-select-checkbox-mti" value="'.$materialRequestList[$pagination]['material_request_component_id'].'">';
                        $user_status = '<td><span class="label label-sm label-success">'. $materialRequestList[$pagination]['component_status_name'].' </span></td>';
                        $actionDropDown = '<div class="btn-group">
                        <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                            Actions
                            <i class="fa fa-angle-down"></i>
                        </button>
                        <ul class="dropdown-menu pull-left" role="menu">';
                        if($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin' || $user->customHasPermission('approve-material-request')){
                            $actionDropDown .= '<li>
                                    <a href="javascript:void(0);" onclick="openIndentModal('.$materialRequestList[$pagination]['material_request_component_id'].')">
                                        <i class="icon-tag"></i>  Move To indent
                                    </a>
                                </li>';
                        }
                        $actionDropDown .= '</ul>
                            </div>';
                        break;

                    case 'admin-disapproved':
                        $checkboxComponent = '<input type="checkbox" class="multiple-select-checkbox" value="'.$materialRequestList[$pagination]['material_request_component_id'].'" disabled>';
                        $checkboxComponentMoveToIndent = '<input type="checkbox" class="multiple-select-checkbox-mti" value="'.$materialRequestList[$pagination]['material_request_component_id'].'" disabled>';
                        $user_status = '<td><span class="label label-sm label-success">'. $materialRequestList[$pagination]['component_status_name'].' </span></td>';
                        $actionDropDown = '<div class="btn-group">
                            <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                Actions
                                <i class="fa fa-angle-down"></i>
                            </button>
                            <ul class="dropdown-menu pull-left" role="menu">';
                        $actionDropDown .= '</ul>
                            </div>';
                        break;

                    case 'in-indent':
                        $checkboxComponent = '<input type="checkbox" class="multiple-select-checkbox" value="'.$materialRequestList[$pagination]['material_request_component_id'].'" disabled>';
                        $checkboxComponentMoveToIndent = '<input type="checkbox" class="multiple-select-checkbox-mti" value="'.$materialRequestList[$pagination]['material_request_component_id'].'" disabled>';
                        $user_status = '<td><span class="label label-sm label-success">'. $materialRequestList[$pagination]['component_status_name'].' </span></td>';
                        $actionDropDown = '<div class="btn-group">
                            <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                Actions
                                <i class="fa fa-angle-down"></i>
                            </button>
                            <ul class="dropdown-menu pull-left" role="menu">';
                        $actionDropDown .= '</ul>
                        </div>';
                        break;

                    default:
                        $checkboxComponent = '<input type="checkbox" class="multiple-select-checkbox" value="'.$materialRequestList[$pagination]['material_request_component_id'].'" disabled>';
                        $checkboxComponentMoveToIndent = '<input type="checkbox" class="multiple-select-checkbox-mti" value="'.$materialRequestList[$pagination]['material_request_component_id'].'" disabled>';
                        $user_status = '<td><span class="label label-sm label-success">'. $materialRequestList[$pagination]['component_status_name'].' </span></td>';
                        $actionDropDown = '<div class="btn-group">
                            <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                Actions
                                <i class="fa fa-angle-down"></i>
                            </button>
                            <ul class="dropdown-menu pull-left" role="menu">';
                        $actionDropDown .='</ul>
                            </div>';
                        break;
                }
                $records['data'][$iterator] = [
                    $checkboxComponent,
                    $checkboxComponentMoveToIndent,
                    $materialRequestList[$pagination]['mr_id'],
                    $materialRequestList[$pagination]['name'],
                    $materialRequestList[$pagination]['quantity'],
                    Unit::where('id', $materialRequestList[$pagination]['unit_id'])->pluck('name')    ,
                    date('d M Y',strtotime($materialRequestList[$pagination]['created_at'])),
                    $user_status,
                    $actionDropDown,
                    '<a href="javascript:void(0);" class="btn btn-xs green dropdown-toggle" type="button" aria-expanded="true" onclick="openDetails('.$materialRequestList[$pagination]['material_request_component_id'].')">
                                        Details
                                    </a>'
                ];
            }
            $records["draw"] = intval($request->draw);
            $records["recordsTotal"] = $iTotalRecords;
            $records["recordsFiltered"] = $iTotalRecords;
            $status = 200;
        }catch(\Exception $e){
            $data = [
              'action' => 'Material Request listing',
              'params' => $request->all(),
              'exception'=> $e->getMessage()
            ];
            Log::critical(json_encode($data));
            $status = 500;
            $records = [];
        }
        return response()->json($records,$status);
    }

    public function editMaterialRequest(Request $request){
        return view('purchase/material-request/edit');
    }

    public function autoSuggest(Request $request){
        try{
            $message = "Success";
            $iterator = 0;
            $data = array();
            switch($request->search_in){
                case 'material' :
                    $materialList = array();
                    $quotation = Quotation::where('project_site_id',$request['project_site_id'])->first();
                    if(count($quotation) != null){
                        $quotationMaterialId = Material::whereIn('id',array_column($quotation->quotation_materials->toArray(),'material_id'))
                            ->where('name','ilike','%'.$request->keyword.'%')->pluck('id');
                        $quotationMaterials = QuotationMaterial::where('quotation_id',$quotation->id)->whereIn('material_id',$quotationMaterialId)->get();
                        $quotationMaterialSlug = MaterialRequestComponentTypes::where('slug','quotation-material')->first();
                        $materialRequestID = MaterialRequests::where('project_site_id',$request['project_site_id'])->pluck('id')->first();
                        $adminApproveComponentStatusId = PurchaseRequestComponentStatuses::where('slug','admin-approved')->pluck('id')->first();
                        foreach($quotationMaterials as $key => $quotationMaterial){
                            $usedMaterial = MaterialRequestComponents::where('material_request_id',$materialRequestID)->where('component_type_id',$quotationMaterialSlug->id)->where('component_status_id',$adminApproveComponentStatusId)->where('name',$quotationMaterial->material->name)->orderBy('created_at','asc')->get();
                            $totalQuantityUsed = 0;
                            foreach($usedMaterial as $index => $material){
                                if($material->unit_id == $quotationMaterial->unit_id){
                                    $totalQuantityUsed += $material->quantity;
                                }else{
                                    $unitConversionValue = UnitConversion::where('unit_1_id',$material->unit_id)->where('unit_2_id',$quotationMaterial->unit_id)->first();
                                    if(count($unitConversionValue) > 0){
                                        $conversionQuantity = $material->quantity * $unitConversionValue->unit_1_value;
                                        $totalQuantityUsed += $conversionQuantity;
                                    }else{
                                        $reverseUnitConversionValue = UnitConversion::where('unit_1_id',$quotationMaterial->unit_id)->where('unit_2_id',$material->unit_id)->first();
                                        $conversionQuantity = $material->quantity / $reverseUnitConversionValue->unit_2_value;
                                        $totalQuantityUsed += $conversionQuantity;
                                    }
                                }
                            }
                            $materialVersions = MaterialVersion::where('material_id',$quotationMaterial['material_id'])->where('unit_id',$quotationMaterial['unit_id'])->pluck('id');
                            $material_quantity = QuotationProduct::where('quotation_products.quotation_id',$quotation->id)
                                ->join('product_material_relation','quotation_products.product_version_id','=','product_material_relation.product_version_id')
                                ->whereIn('product_material_relation.material_version_id',$materialVersions)
                                ->sum(DB::raw('quotation_products.quantity * product_material_relation.material_quantity'));
                            $allowedQuantity = $material_quantity - $totalQuantityUsed;
                            $materialList[$iterator]['material_name'] = $quotationMaterial->material->name;
                            $materialList[$iterator]['unit_quantity'][0]['quantity'] = $allowedQuantity;
                            $materialList[$iterator]['unit_quantity'][0]['unit_id'] = (int)$quotationMaterial->unit_id;
                            $materialList[$iterator]['unit_quantity'][0]['unit_name'] = $quotationMaterial->unit->name;
                            $unitConversionIds1 = UnitConversion::where('unit_1_id',$quotationMaterial->unit_id)->pluck('unit_2_id');
                            $unitConversionIds2 = UnitConversion::where('unit_2_id',$quotationMaterial->unit_id)->pluck('unit_1_id');
                            $unitConversionNeededIds = array_merge($unitConversionIds1->toArray(),$unitConversionIds2->toArray());
                            $i = 1;
                            foreach($unitConversionNeededIds as $unitId){
                                $conversionData = $this->unitConversion($quotationMaterial->unit_id,$unitId,$allowedQuantity);
                                $materialList[$iterator]['unit_quantity'][$i]['quantity'] = $conversionData['quantity_to'];
                                $materialList[$iterator]['unit_quantity'][$i]['unit_id'] = $conversionData['unit_to_id'];
                                $materialList[$iterator]['unit_quantity'][$i]['unit_name'] = $conversionData['unit_to_name'];
                                $i++;
                            }
                            $materialList[$iterator]['material_request_component_type_slug'] = $quotationMaterialSlug->slug;
                            $materialList[$iterator]['material_request_component_type_id'] = $quotationMaterialSlug->id;
                            $iterator++;
                        }
                        $structureMaterials = Material::whereNotIn('id',$quotationMaterialId)->where('name','ilike','%'.$request->keyword.'%')->get();
                    }else{
                        $structureMaterials = Material::where('name','ilike','%'.$request->keyword.'%')->get();
                    }
                    $structureMaterialSlug = MaterialRequestComponentTypes::where('slug','structure-material')->first();
                    foreach($structureMaterials as $key1 => $material){
                        $materialList[$iterator]['material_name'] = $material->name;
                        $materialList[$iterator]['unit_quantity'][0]['quantity'] = null;
                        $materialList[$iterator]['unit_quantity'][0]['unit_id'] = $material->unit_id;
                        $materialList[$iterator]['unit_quantity'][0]['unit_name'] = $material->unit->name;
                        $unitConversionIds1 = UnitConversion::where('unit_1_id',$material->unit_id)->pluck('unit_2_id');
                        $unitConversionIds2 = UnitConversion::where('unit_2_id',$material->unit_id)->pluck('unit_1_id');
                        $unitConversionNeededIds = array_merge($unitConversionIds1->toArray(),$unitConversionIds2->toArray());
                        $i = 1;
                        foreach($unitConversionNeededIds as $unitId){
                            $conversionData = $this->unitConversion($material->unit_id,$unitId,null);
                            $materialList[$iterator]['unit_quantity'][$i]['quantity'] = $conversionData['quantity_to'];
                            $materialList[$iterator]['unit_quantity'][$i]['unit_id'] = $conversionData['unit_to_id'];
                            $materialList[$iterator]['unit_quantity'][$i]['unit_name'] = $conversionData['unit_to_name'];
                            $i++;
                        }
                        $materialList[$iterator]['material_request_component_type_slug'] = $structureMaterialSlug->slug;
                        $materialList[$iterator]['material_request_component_type_id'] = $structureMaterialSlug->id;
                        $iterator++;
                    }
                    if(count($materialList) == 0){
                        $materialList[$iterator]['material_name'] = $request->keyword;
                        $systemUnits = Unit::where('is_active',true)->get();
                        $j = 0;
                        foreach($systemUnits as $key2 => $unit){
                            $materialList[$iterator]['unit_quantity'][$j]['quantity'] = null;
                            $materialList[$iterator]['unit_quantity'][$j]['unit_id'] = $unit->id;
                            $materialList[$iterator]['unit_quantity'][$j]['unit_name'] = $unit->name;
                            $j++;
                        }
                        $newMaterialSlug = MaterialRequestComponentTypes::where('slug','new-material')->first();
                        $materialList[$iterator]['material_request_component_type_slug'] = $newMaterialSlug->slug;
                        $materialList[$iterator]['material_request_component_type_id'] = $newMaterialSlug->id;
                    }
                    $data = $materialList;
                    break;
                case "asset" :
                    $assetList = array();
                    $alreadyExistAsset = Asset::where('name','ilike','%'.$request['keyword'].'%')->get();
                    $assetUnit = Unit::where('slug','nos')->pluck('name')->first();
                    $systemAssetStatus = MaterialRequestComponentTypes::where('slug','system-asset')->first();
                    foreach ($alreadyExistAsset as $key => $asset){
                        $assetList[$iterator]['asset_id'] = $asset['id'];
                        $assetList[$iterator]['asset_name'] = $asset['name'];
                        $assetList[$iterator]['asset_unit'] = $assetUnit;
                        $assetList[$iterator]['material_request_component_type_slug'] = $systemAssetStatus->slug;
                        $assetList[$iterator]['material_request_component_type_id'] = $systemAssetStatus->id;
                        $iterator++;
                    }
                    if(count($assetList) == 0){
                        $assetList[$iterator]['asset_id'] = null;
                        $assetList[$iterator]['asset_name'] = $request['keyword'];
                        $assetList[$iterator]['asset_unit'] = $assetUnit;
                        $newAssetSlug = MaterialRequestComponentTypes::where('slug','new-asset')->first();
                        $assetList[$iterator]['material_request_component_type_slug'] = $newAssetSlug->slug;
                        $assetList[$iterator]['material_request_component_type_id'] = $newAssetSlug->id;
                    }
                    $data = $assetList;
                    break;
            }
        }catch(\Exception $e){
            $status = 500;
            $message = "Fail";
            $data = [
                'action' => 'AutoSuggestion',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
        }
        $response = [
            "message" => $message,
            "data" => $data
        ];
        return($data);
    }

    public function unitConversion($unit_from_id,$unit_to_id,$quantity_from){
        $unitConversionData = UnitConversion::where('unit_1_id',$unit_from_id)->where('unit_2_id',$unit_to_id)->first();
        if(count($unitConversionData) > 0){
            $data['quantity_to'] = ($quantity_from == null) ? null :($unitConversionData['unit_2_value'] * $quantity_from) / $unitConversionData['unit_1_value'];
            $data['unit_to_id'] = $unitConversionData->unit_2_id;
            $data['unit_to_name'] = $unitConversionData->toUnit->name;
        }else{
            $reverseUnitConversionData = UnitConversion::where('unit_2_id',$unit_from_id)->where('unit_1_id',$unit_to_id)->first();
            $data['quantity_to'] = ($quantity_from == null) ? null :($reverseUnitConversionData['unit_1_value'] * $quantity_from) / $reverseUnitConversionData['unit_2_value'];
            $data['unit_to_id'] = $reverseUnitConversionData->unit_1_id;
            $data['unit_to_name'] = $reverseUnitConversionData->fromUnit->name;
        }
        return $data;
    }

    public function getUsersList(Request $request){
        try{
            $data = $request->all();
            $projectSiteId = $request->project_site_id;
            if($request->module == 'material-request'){
                $users = User::join('user_has_roles','user_has_roles.user_id','=','users.id')
                    ->join('roles','roles.id','=','user_has_roles.role_id')
                    ->join('user_project_site_relation','user_project_site_relation.user_id','=','users.id')
                    ->join('user_has_permissions','user_has_permissions.user_id','=','users.id')
                    ->join('permissions','permissions.id','=','user_has_permissions.permission_id')
                    ->whereNotIn('roles.slug',['admin','superadmin'])
                    ->where(function($query) use($data){
                        $query->where('users.first_name','ilike','%'.$data['keyword'].'%');
                        $query->orWhere('users.last_name','ilike','%'.$data['keyword'].'%');
                    })
                    ->where('user_project_site_relation.project_site_id',$projectSiteId)
                    ->whereIn('permissions.name',['create-material-request','approve-material-request'])
                    ->select('users.first_name as first_name','users.last_name as last_name','users.id as id')
                    ->distinct('id')
                    ->get()->toArray();
            }elseif ($request->module == 'purchase-request'){
                $users = User::join('user_has_roles','user_has_roles.user_id','=','users.id')
                    ->join('roles','roles.id','=','user_has_roles.role_id')
                    ->join('user_project_site_relation','user_project_site_relation.user_id','=','users.id')
                    ->join('user_has_permissions','user_has_permissions.user_id','=','users.id')
                    ->join('permissions','permissions.id','=','user_has_permissions.permission_id')
                    ->whereNotIn('roles.slug',['admin','superadmin'])
                    ->where(function($query) use($data){
                        $query->where('users.first_name','ilike','%'.$data['keyword'].'%');
                        $query->orWhere('users.last_name','ilike','%'.$data['keyword'].'%');
                    })
                    ->where('user_project_site_relation.project_site_id',$projectSiteId)
                    ->whereIn('permissions.name',['create-purchase-request','approve-purchase-request'])
                    ->select('users.first_name as first_name','users.last_name as last_name','users.id as id')
                    ->distinct('id')
                    ->get()->toArray();
            }else{
                $users = User::join('user_has_roles','user_has_roles.user_id','=','users.id')
                    ->join('roles','roles.id','=','user_has_roles.role_id')
                    ->join('user_project_site_relation','user_project_site_relation.user_id','=','users.id')
                    ->whereNotIn('roles.slug',['admin','superadmin'])
                    ->where(function($query) use($data){
                        $query->where('users.first_name','ilike','%'.$data['keyword'].'%');
                        $query->orWhere('users.last_name','ilike','%'.$data['keyword'].'%');
                    })
                    ->where('user_project_site_relation.project_site_id',$projectSiteId)
                    ->select('users.first_name as first_name','users.last_name as last_name','users.id as id')
                    ->distinct('id')
                    ->get()->toArray();
            }
            $opt= '';
            foreach ($users as $user) {
                $opt .= '<li onclick="selectUser(\''.htmlspecialchars($user['first_name'].' '.$user['last_name'], ENT_QUOTES).'\','.$user['id'].')">'.$user['first_name'].' '.$user['last_name'].'</li>';
            }
            $abc = $opt;
            $str3 = '<ul id="asset-list" style="border: 1px solid;height: 100px;overflow-y: overlay">'.$abc.'</ul>';
            $users = $str3;
            $status = 200;
        }catch(\Exception $e){
            $data = [
                'action' => 'Get User List',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            $status = 500;
            $users = '';
        }
        return response()->json($users,$status);
    }

    public function createMaterialList(Request $request){
        try{
            $data = $request->all();
            $user = Auth::user();
            $materialRequestComponentId = $this->createMaterialRequest($data,$user,false);
            if($materialRequestComponentId == null){
                $request->session()->flash('error', 'Material request could not be created.');
            }else{
                $request->session()->flash('success', 'Material request created successfully.');
            }
            return redirect('purchase/material-request/manage');
        }catch(\Exception $e){
            $data = [
                'action' => 'Create Material Request',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getMaterialRequestWiseListing(Request $request){
        try{
            $postdata = null;
            $mr_name = "";
            $status = 0;
            $site_id = 0;
            $month = 0;
            $year = 0;
            $mr_count = 0;
            $postDataArray = array();
            if ($request->has('mr_name')) {
                if ($request['mr_name'] != "") {
                  $mr_name = $request['mr_name'];
                }
            }

            if ($request->has('status')) {
                $status = $request['status'];
            }
            if($request->has('postdata')) {
                $postdata = $request['postdata'];
                if($postdata != null) {
                    $mstr = explode(",",$request['postdata']);
                    foreach($mstr as $nstr)
                    {
                      $narr = explode("=>",$nstr);
                      $narr[0] = str_replace("\x98","",$narr[0]);
                      $ytr[1] = $narr[1];
                      $postDataArray[$narr[0]] = $ytr[1];
                    }
                }
                $site_id = $postDataArray['site_id'];
                $month = $postDataArray['month'];
                $year = $postDataArray['year'];
                $mr_count = $postDataArray['mr_count'];
            }
            if($request->has('site_id')){
                $site_id = $request->site_id;
            }
            $materialRequests = array();
            $ids = MaterialRequests::all()->pluck('id');
            $filterFlag = true;
            if ($site_id != 0 && $filterFlag == true) {
                $ids = MaterialRequests::whereIn('id',$ids)->where('project_site_id', $site_id)->pluck('id');
                if(count($ids) <= 0) {
                  $filterFlag = false;
                }
            }
            if ($year != 0 && $filterFlag == true) {
                $ids = MaterialRequests::whereIn('id',$ids)->whereYear('created_at', $year)->pluck('id');
                if(count($ids) <= 0) {
                  $filterFlag = false;
                }
            }
            if ($month != 0 && $filterFlag == true) {
                $ids = MaterialRequests::whereIn('id',$ids)->whereMonth('created_at', $month)->pluck('id');
                if(count($ids) <= 0) {
                  $filterFlag = false;
                }
            }
            if ($status != 0 && $filterFlag == true) {
                $ids = MaterialRequests::join('material_request_components','material_request_components.material_request_id','=','material_requests.id')
                  ->where('material_request_components.component_status_id',$status)->distinct('material_requests.id')->pluck('material_requests.id');
                if(count($ids) <= 0) {
                  $filterFlag = false;
                }
            }
            if ($mr_count != 0 && $filterFlag == true) {
                $ids = MaterialRequests::whereIn('id',$ids)->where('serial_no', $mr_count)->pluck('id');
                if(count($ids) <= 0) {
                    $filterFlag = false;
                }
            }
            if ($filterFlag) {
                $materialRequests = MaterialRequests::whereIn('id',$ids)->orderBy('id','desc')->get();
            }
            $materialRequestList = array();
            $iterator = 0;
            foreach($materialRequests as $key => $materialRequest){
                $materialRequestList[$iterator]['project_site_id'] =$materialRequest['project_site_id'];
                $pro = $materialRequest->projectSite->project;
                $materialRequestList[$iterator]['project_name'] =$pro->name;
                $materialRequestList[$iterator]['client_name'] =$pro->client->company;
                $materialRequestList[$iterator]['site_name'] = $materialRequest->projectSite->name;
                $materialRequestList[$iterator]['created_at'] =$materialRequest['created_at'];
                $materialRequestList[$iterator]['rm_id'] = $this->getPurchaseIDFormat('material-request',$materialRequest['project_site_id'],$materialRequest['created_at'],$materialRequest->serial_no);
                $iterator++;
            }
            $iTotalRecords = count($materialRequestList);
            $records = array();
            $records['data'] = array();
            if($request->length == -1){
                $length = $iTotalRecords;
            }else{
                $length = $request->length;
            }
            for($iterator = 0,$pagination = $request->start; $iterator < $length && $iterator < count($materialRequestList); $iterator++,$pagination++ ){
                $records['data'][$iterator] = [
                    $materialRequestList[$pagination]['rm_id'],
                    $materialRequestList[$pagination]['client_name'],
                    $materialRequestList[$pagination]['project_name']." - ".$materialRequestList[$pagination]['site_name'],
                    '<div class="btn-group">
                        <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                            Actions
                            <i class="fa fa-angle-down"></i>
                        </button>
                        <ul class="dropdown-menu pull-left" role="menu">
                            <!--<li>
                                <a href="/purchase/material-request/edit/">
                                    <i class="icon-docs"></i> Edit 
                                </a>
                            </li>-->
                        </ul>
                    </div>'
                ];
            }
            $records["draw"] = intval($request->draw);
            $records["recordsTotal"] = $iTotalRecords;
            $records["recordsFiltered"] = $iTotalRecords;
        }catch(\Exception $e){
            $records = array();
            $data = [
              'action' => 'Material Request listing',
              'params' => $request->all(),
              'exception'=> $e->getMessage()
            ];
        }
        return response()->json($records,200);
    }

    public function getMaterialRequestWiseListingView(){
        $approvedQuotationStatus = QuotationStatus::where('slug','approved')->first();
        $projectSiteIds = Quotation::where('quotation_status_id',$approvedQuotationStatus['id'])->pluck('project_site_id')->toArray();
        $projectIds = ProjectSite::whereIn('id',$projectSiteIds)->pluck('project_id')->toArray();
        $clientIds = Project::whereIn('id',$projectIds)->pluck('client_id')->toArray();
        $clients = Client::whereIn('id',$clientIds)->where('is_active',true)->orderBy('id','asc')->get()->toArray();
        return view ('purchase/material-request/material-request-listing')->with(compact('clients'));
    }

    use NotificationTrait;

    public function changeMaterialRequestComponentStatus(Request $request,$newStatus,$componentId = null){
        try{
            $user = Auth::user();
            switch($newStatus){
                case 'admin-approved':
                    $componentIds = $request->component_id;
                    foreach($componentIds as $componentId){
                        $materialRequestComponent = MaterialRequestComponents::where('id',$componentId)->first();
                        $adminApproveComponentStatusId = PurchaseRequestComponentStatuses::where('slug','admin-approved')->pluck('id')->first();
                        if($materialRequestComponent->purchaseRequestComponentStatuses->slug == 'pending'){
                            if($request->has('quantity')){
                                if($materialRequestComponent['quantity'] != $request->quantity){
                                    $materialRequestComponentVersion['material_request_component_id'] = $materialRequestComponent['id'];
                                    $materialRequestComponentVersion['component_status_id'] = $adminApproveComponentStatusId;
                                    $materialRequestComponentVersion['user_id'] = $user['id'];
                                    $materialRequestComponentVersion['quantity'] = $request->quantity;
                                    $materialRequestComponentVersion['unit_id'] = $materialRequestComponent['unit_id'];
                                    $materialRequestComponentVersion['remark'] = $request->remark;
                                    MaterialRequestComponentVersion::create($materialRequestComponentVersion);
                                }
                                $materialRequestComponent->update(['quantity' => $request->quantity]);
                            }
                            if($request->has('unit_id')){
                                if($materialRequestComponent['unit_id'] != $request->unit_id) {
                                    $materialRequestComponentVersion['material_request_component_id'] = $materialRequestComponent['id'];
                                    $materialRequestComponentVersion['component_status_id'] = $adminApproveComponentStatusId;
                                    $materialRequestComponentVersion['user_id'] = $user['id'];
                                    $materialRequestComponentVersion['quantity'] = $materialRequestComponent['quantity'];
                                    $materialRequestComponentVersion['unit_id'] = $request->unit_id;
                                    $materialRequestComponentVersion['remark'] = $request->remark;
                                    MaterialRequestComponentVersion::create($materialRequestComponentVersion);
                                }
                                $materialRequestComponent->update(['unit_id' => $request->unit_id]);
                            }
                            $quotationMaterialType = MaterialRequestComponentTypes::where('slug','quotation-material')->first();
                            $projectSiteId = $materialRequestComponent->materialRequest->project_site_id;
                            $materialComponentHistoryData = array();
                            $materialComponentHistoryData['component_status_id'] = $materialRequestComponentVersion['component_status_id'] = $adminApproveComponentStatusId;
                            $materialComponentHistoryData['remark'] = $materialRequestComponentVersion['remark'] = $request->remark;
                            $materialComponentHistoryData['user_id'] = $materialRequestComponentVersion['user_id'] = Auth::user()->id;
                            $materialComponentHistoryData['material_request_component_id'] = $materialRequestComponentVersion['material_request_component_id'] = $componentId;
                            $materialRequestComponentVersion['quantity'] = $materialRequestComponent['quantity'];
                            $materialRequestComponentVersion['unit_id'] = $materialRequestComponent['unit_id'];
                            if($materialRequestComponent['component_type_id'] == $quotationMaterialType->id){
                                $usedQuantity = MaterialRequestComponents::join('material_requests','material_requests.id','=','material_request_components.material_request_id')
                                    ->where('material_request_components.id','!=',$materialRequestComponent->id)
                                    ->where('material_requests.project_site_id', $projectSiteId)
                                    ->where('material_request_components.component_type_id',$quotationMaterialType['id'])
                                    ->where('material_request_components.component_status_id',$adminApproveComponentStatusId)
                                    ->where('material_request_components.name',$materialRequestComponent['name'])
                                    ->sum('material_request_components.quantity');
                                $quotation = Quotation::where('project_site_id',$projectSiteId)->first();
                                $quotationMaterialId = Material::whereIn('id',array_column($quotation->quotation_materials->toArray(),'material_id'))
                                    ->where('name',$materialRequestComponent->name)
                                    ->pluck('id')
                                    ->first();
                                $quotationMaterial = QuotationMaterial::where('quotation_id',$quotation->id)->where('material_id',$quotationMaterialId)->first();
                                $materialVersions = MaterialVersion::where('material_id',$quotationMaterial['material_id'])->where('unit_id',$quotationMaterial['unit_id'])->pluck('id');
                                $material_quantity = QuotationProduct::where('quotation_products.quotation_id',$quotation->id)
                                    ->join('product_material_relation','quotation_products.product_version_id','=','product_material_relation.product_version_id')
                                    ->whereIn('product_material_relation.material_version_id',$materialVersions)
                                    ->sum(DB::raw('quotation_products.quantity * product_material_relation.material_quantity'));
                                $allowedQuantity = $material_quantity - $usedQuantity;
                                MaterialRequestComponents::where('id',$componentId)->update(['component_status_id' => $adminApproveComponentStatusId]);
                                MaterialRequestComponentHistory::create($materialComponentHistoryData);
                                MaterialRequestComponentVersion::create($materialRequestComponentVersion);
                                $message = "Status Updated Successfully";
                            }else{
                                MaterialRequestComponents::where('id',$componentId)->update(['component_status_id' => $adminApproveComponentStatusId]);
                                $message = "Status Updated Successfully";
                                MaterialRequestComponentHistory::create($materialComponentHistoryData);
                                MaterialRequestComponentVersion::create($materialRequestComponentVersion);
                            }
                        }
                    }
                    break;

                case 'admin-disapproved':
                    $componentIds = $request->component_id;
                    if($request->has('remark')){
                        $remark = $request->remark;
                    }else{
                        $remark = '';
                    }
                    $adminDisapproveStatusId = PurchaseRequestComponentStatuses::where('slug',$newStatus)->pluck('id')->first();
                    $materialComponentHistoryData = array();
                    $materialRequestComponentVersion['component_status_id'] = $materialComponentHistoryData['component_status_id'] = $adminDisapproveStatusId;
                    $materialRequestComponentVersion['remark'] = $materialComponentHistoryData['remark'] = $remark;
                    $materialRequestComponentVersion['user_id'] = $materialComponentHistoryData['user_id'] = Auth::user()->id;
                    foreach($componentIds as $componentId){
                        $materialRequestComponent = MaterialRequestComponents::findOrFail($componentId);
                        $materialRequestComponent->update(['component_status_id' => $adminDisapproveStatusId]);
                        $materialComponentHistoryData['material_request_component_id'] = $materialRequestComponentVersion['material_request_component_id'] = $componentId;
                        $materialRequestComponentVersion['quantity'] = $materialRequestComponent['quantity'];
                        $materialRequestComponentVersion['unit_id'] = $materialRequestComponent['unit_id'];
                        MaterialRequestComponentHistory::create($materialComponentHistoryData);
                        MaterialRequestComponentVersion::create($materialRequestComponentVersion);
                        $userTokens = User::join('material_requests','material_requests.on_behalf_of','=','users.id')
                                        ->join('material_request_components','material_request_components.material_request_id','=','material_requests.id')
                                        ->where('material_request_components.id', $componentId)
                                        ->select('users.mobile_fcm_token','users.web_fcm_token')
                                        ->get()
                                        ->toArray();
                        $webTokens = array_column($userTokens,'web_fcm_token');
                        $mobileTokens = array_column($userTokens,'mobile_fcm_token');
                        $notificationString = '1D -'.$materialRequestComponent->materialRequest->projectSite->project->name.' '.$materialRequestComponent->materialRequest->projectSite->name;
                        $notificationString .= ' '.$user['first_name'].' '.$user['last_name'].'Material Disapproved.';
                        $notificationString .= ' '.$remark;
                        $this->sendPushNotification('Manisha Construction',$notificationString,$webTokens,$mobileTokens,'d-m-r');
                    }
                    break;

                case 'in-indent':
                    $inIndentStatusId = PurchaseRequestComponentStatuses::where('slug',$newStatus)->pluck('id')->first();
                    $materialRequestComponent = MaterialRequestComponents::where('id',$request['component_id'])->first();
                    $materialRequestComponentVersion['material_request_component_id'] = $request['component_id'];
                    $materialRequestComponentVersion['component_status_id'] = $inIndentStatusId;
                    $materialRequestComponentVersion['user_id'] = $user['id'];
                    $materialRequestComponentVersion['quantity'] = ($request->has('quantity')) ? $request->quantity : $materialRequestComponent['quantity'];
                    $materialRequestComponentVersion['unit_id'] = ($request->has('unit_id')) ? $request->unit_id : $materialRequestComponent['unit_id'];
                    $materialRequestComponentVersion['remark'] = $request->remark;
                    MaterialRequestComponentVersion::create($materialRequestComponentVersion);
                    MaterialRequestComponents::where('id',$request['component_id'])->update(['component_status_id' => $inIndentStatusId,'quantity' => $request->quantity,'unit_id' => $request->unit_id]);
                    $materialComponentHistoryData = array();
                    $materialComponentHistoryData['component_status_id'] = $inIndentStatusId;
                    $materialComponentHistoryData['user_id'] = Auth::user()->id;
                    $materialComponentHistoryData['material_request_component_id'] = $request['component_id'];
                    MaterialRequestComponentHistory::create($materialComponentHistoryData);
                    break;
            }
            $request->session()->flash('success', "Status updated successfully.");
            return redirect('/purchase/material-request/manage');
        }catch(\Exception $e){
            $data = [
                'action' => 'Change Material Request Component Statuses',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
        }
    }

    public function changeMaterialRequestComponentStatustoMTI(Request $request){
        try{
            $user = Auth::user();
            $componentIds = $request->bulk_component_id;
            $inIndentStatusId = PurchaseRequestComponentStatuses::where('slug','in-indent')->pluck('id')->first();
            foreach($componentIds as $componentId){
                $materialRequestComponent = MaterialRequestComponents::where('id',$componentId)->first();
                $materialRequestComponentVersion['material_request_component_id'] = $componentId;
                $materialRequestComponentVersion['component_status_id'] = $inIndentStatusId;
                $materialRequestComponentVersion['user_id'] = $user['id'];
                $materialRequestComponentVersion['quantity'] = ($request->has('quantity')) ? $request->quantity : $materialRequestComponent['quantity'];
                $materialRequestComponentVersion['unit_id'] = ($request->has('unit_id')) ? $request->unit_id : $materialRequestComponent['unit_id'];
                $materialRequestComponentVersion['remark'] = $request->remark;
                MaterialRequestComponentVersion::create($materialRequestComponentVersion);
                MaterialRequestComponents::where('id',$componentId)->update(['component_status_id' => $inIndentStatusId,'quantity' => $materialRequestComponentVersion['quantity'],'unit_id' => $materialRequestComponentVersion['unit_id']]);
                $materialComponentHistoryData['component_status_id'] = $inIndentStatusId;
                $materialComponentHistoryData['user_id'] = $user['id'];
                $materialComponentHistoryData['material_request_component_id'] = $componentId;
                MaterialRequestComponentHistory::create($materialComponentHistoryData);
            }
            $request->session()->flash('success', "Bulk Move to Indent Status updated successfully.");
            return redirect('/purchase/material-request/manage');
        }catch(\Exception $e){
            $data = [
                'action' => 'Change Material Request Component Statuses to Move to Indent (Bulk)',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
        }
    }

    public function getMaterialRequestComponentDetail(Request $request,$materialRequestComponent){
        try {
            $assetComponentTypes = MaterialRequestComponentTypes::whereIn('slug', ['system-asset', 'new-asset'])->pluck('id')->toArray();
            if (in_array($materialRequestComponent->component_type_id, $assetComponentTypes)) {
                $nosUnit = Unit::where('slug', 'nos')->select('id', 'name')->first();
                $units = "<option value='$nosUnit->id'>$nosUnit->name</option>";
            } else {
                $newMaterialTypeId = MaterialRequestComponentTypes::where('slug', 'new-material')->pluck('id')->first();
                if ($newMaterialTypeId == $materialRequestComponent->component_type_id) {
                    $unitData = Unit::where('is_active', true)->select('id', 'name')->orderBy('name')->get()->toArray();
                } else {
                    $material = Material::where('name', 'ilike', $materialRequestComponent->name)->first();
                    $unit1Array = UnitConversion::join('units', 'units.id', '=', 'unit_conversions.unit_2_id')
                        ->where('unit_conversions.unit_1_id', $material->unit_id)
                        ->select('units.id as id', 'units.name as name')
                        ->get()
                        ->toArray();
                    $units2Array = UnitConversion::join('units', 'units.id', '=', 'unit_conversions.unit_1_id')
                        ->where('unit_conversions.unit_2_id', $material->unit_id)
                        ->whereNotIn('unit_conversions.unit_1_id', array_column($unit1Array, 'id'))
                        ->select('units.id as id', 'units.name as name')
                        ->get()
                        ->toArray();
                    $unitData = array_merge($unit1Array, $units2Array);
                    $unitData[] = [
                        'id' => $material->unit->id,
                        'name' => $material->unit->name,
                    ];
                }
                for ($iterator = 0, $units = ''; $iterator < count($unitData); $iterator++) {
                    if ($unitData[$iterator]['id'] == $materialRequestComponent->unit_id) {
                        $units .= "<option value='" . $unitData[$iterator]['id'] . "' selected>" . $unitData[$iterator]['name'] . "</option>";
                    } else {
                        $units .= "<option value='" . $unitData[$iterator]['id'] . "'>" . $unitData[$iterator]['name'] . "</option>";
                    }
                }
            }
            $response = [
                'units' => $units,
                'quantity' => $materialRequestComponent->quantity
            ];
            $status = 200;
        } catch (\Exception $e) {
            $data = [
                'action' => 'Get Material Component Details',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            $status = 500;
            $response = null;
        }
        return response()->json($response, $status);
    }

    public function getPurchaseDetails(Request $request,$materialRequestComponentId){
        try{
            $data = array();
            $materialRequestComponent = MaterialRequestComponents::where('id',$materialRequestComponentId)->first();
            $materialComponentVersions = $materialRequestComponent->materialRequestComponentVersion;
            $iterator = 0;
            foreach($materialComponentVersions as $key => $materialComponentVersion){
                $materialRequestComponentStatusSlug = $materialComponentVersion->purchaseRequestComponentStatuses->slug;
                $user = $materialComponentVersion->user;
                switch ($materialRequestComponentStatusSlug){
                    case 'pending' :
                        $data[$iterator]['display_message'] = date('l, d F Y',strtotime($materialComponentVersion['created_at'])).' '.$materialComponentVersion['quantity'].' '.$materialComponentVersion->unit->name.' material requested by '.$user->first_name.' '.$user->last_name.' '.$materialComponentVersion->remark;
                        break;

                    case 'manager-approved' :
                        $data[$iterator]['display_message'] = date('l, d F Y',strtotime($materialComponentVersion['created_at'])).' '.$materialComponentVersion['quantity'].' '.$materialComponentVersion->unit->name.' material approved by '.$user->first_name.' '.$user->last_name.' '.$materialComponentVersion->remark;
                        break;

                    case 'manager-disapproved':
                        $data[$iterator]['display_message'] = date('l, d F Y',strtotime($materialComponentVersion['created_at'])).' '.$materialComponentVersion['quantity'].' '.$materialComponentVersion->unit->name.' material disapproved by '.$user->first_name.' '.$user->last_name.' '.$materialComponentVersion->remark;
                        break;

                    case 'admin-approved' :
                        $data[$iterator]['display_message'] = date('l, d F Y',strtotime($materialComponentVersion['created_at'])).' '.$materialComponentVersion['quantity'].' '.$materialComponentVersion->unit->name.' material approved by '.$user->first_name.' '.$user->last_name.' '.$materialComponentVersion->remark;
                        break;

                    case 'admin-disapproved':
                        $data[$iterator]['display_message'] = date('l, d F Y',strtotime($materialComponentVersion['created_at'])).' '.$materialComponentVersion['quantity'].' '.$materialComponentVersion->unit->name.' material disapproved by '.$user->first_name.' '.$user->last_name.' '.$materialComponentVersion->remark;
                        break;

                    case 'in-indent':
                        $data[$iterator]['display_message'] = date('l, d F Y',strtotime($materialComponentVersion['created_at'])).' '.$materialComponentVersion['quantity'].' '.$materialComponentVersion->unit->name.' material moved to Purchase by '.$user->first_name.' '.$user->last_name.' '.$materialComponentVersion->remark;
                        break;

                    case 'p-r-assigned':
                        $data[$iterator]['display_message'] = date('l, d F Y',strtotime($materialComponentVersion['created_at'])).' '.$materialComponentVersion['quantity'].' '.$materialComponentVersion->unit->name.' P. R. created by '.$user->first_name.' '.$user->last_name.' '.$materialComponentVersion->remark;
                        break;

                    case 'p-r-manager-approved':
                        $data[$iterator]['display_message'] = date('l, d F Y',strtotime($materialComponentVersion['created_at'])).' '.$materialComponentVersion['quantity'].' '.$materialComponentVersion->unit->name.' P. R. approved by '.$user->first_name.' '.$user->last_name.' '.$materialComponentVersion->remark;
                        break;

                    case 'p-r-manager-disapproved':
                        $data[$iterator]['display_message'] = date('l, d F Y',strtotime($materialComponentVersion['created_at'])).' '.$materialComponentVersion['quantity'].' '.$materialComponentVersion->unit->name.' P. R. disapproved by '.$user->first_name.' '.$user->last_name.' '.$materialComponentVersion->remark;
                        break;

                    case 'p-r-admin-approved':
                        $data[$iterator]['display_message'] = date('l, d F Y',strtotime($materialComponentVersion['created_at'])).' '.$materialComponentVersion['quantity'].' '.$materialComponentVersion->unit->name.' P. R. approved by '.$user->first_name.' '.$user->last_name.' '.$materialComponentVersion->remark;
                        break;

                    case 'p-r-admin-disapproved':
                        $data[$iterator]['display_message'] = date('l, d F Y',strtotime($materialComponentVersion['created_at'])).' '.$materialComponentVersion['quantity'].' '.$materialComponentVersion->unit->name.' P. R. disapproved by '.$user->first_name.' '.$user->last_name.' '.$materialComponentVersion->remark;
                        break;

                    case 'purchase-requested':
                        $data[$iterator]['display_message'] = date('l, d F Y',strtotime($materialComponentVersion['created_at'])).' '.$materialComponentVersion['quantity'].' '.$materialComponentVersion->unit->name.' purchase requested by '.$user->first_name.' '.$user->last_name.' '.$materialComponentVersion->remark;
                        break;
                }
                $iterator++;
            }
            $purchaseOrderComponent = PurchaseOrderComponent::join('purchase_request_components','purchase_request_components.id','=','purchase_order_components.purchase_request_component_id')
                ->join('purchase_orders','purchase_orders.id','=','purchase_order_components.purchase_order_id')
                ->join('purchase_requests','purchase_requests.id','=','purchase_request_components.purchase_request_id')
                ->join('material_request_components','material_request_components.id','=','purchase_request_components.material_request_component_id')
                ->where('material_request_components.id',$materialRequestComponentId)
                ->select('purchase_orders.user_id','purchase_orders.is_approved','purchase_orders.purchase_order_status_id','purchase_orders.created_at','purchase_order_components.quantity','purchase_order_components.unit_id','purchase_order_components.remark')
                ->first();
            if($purchaseOrderComponent != null){
                $unitName = Unit::where('id',$purchaseOrderComponent['unit_id'])->pluck('name')->first();
                $user = User::where('id',$purchaseOrderComponent['user_id'])->first();
                $data[$iterator]['display_message'] = date('l, d F Y',strtotime($purchaseOrderComponent['created_at'])).' '.$purchaseOrderComponent['quantity'].' '.$unitName.' purchase order created by '.$user->first_name.' '.$user->last_name.''.$purchaseOrderComponent['remark'];
            }
            return view('partials.purchase.purchase-detail')->with(compact('data'));
        }catch(\Exception $e){
            $data = [
                'action' => 'Get Purchase Details',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            return response()->json([],500);
        }
    }

    public function validateQuantity(Request $request){
        try{
            $quotation = Quotation::where('project_site_id',$request['project_site_id'])->first();
            if(count($quotation) != null){
                $quotationMaterialId = Material::whereIn('id',array_column($quotation->quotation_materials->toArray(),'material_id'))
                    ->where('name','ilike','%'.$request->material_name.'%')->pluck('id')->first();
                $quotationMaterial = QuotationMaterial::where('quotation_id',$quotation->id)->where('material_id',$quotationMaterialId)->first();
                $quotationMaterialSlug = MaterialRequestComponentTypes::where('slug','quotation-material')->first();
                $materialRequestID = MaterialRequests::where('project_site_id',$request['project_site_id'])->pluck('id');
                $adminApproveComponentStatusId = PurchaseRequestComponentStatuses::where('slug','admin-approved')->pluck('id')->first();
                $usedMaterial = MaterialRequestComponents::whereIn('material_request_id',$materialRequestID)->where('component_type_id',$quotationMaterialSlug->id)->where('component_status_id',$adminApproveComponentStatusId)->where('name','ilike',$quotationMaterial->material->name)->orderBy('created_at','asc')->get();
                $totalQuantityUsed = 0;
                foreach($usedMaterial as $index => $material){
                    if($material->unit_id == $request->unit_id){
                        $totalQuantityUsed += $material->quantity;
                    }else{
                        $unitConversionValue = UnitConversion::where('unit_1_id',$material->unit_id)->where('unit_2_id',$request->unit_id)->first();
                        if(count($unitConversionValue) > 0){
                            $conversionQuantity = $material->quantity * $unitConversionValue->unit_1_value;
                            $totalQuantityUsed += $conversionQuantity;
                        }else{
                            $reverseUnitConversionValue = UnitConversion::where('unit_1_id', $request->unit_id)->where('unit_2_id',$material->unit_id)->first();
                            $conversionQuantity = $material->quantity / $reverseUnitConversionValue->unit_2_value;
                            $totalQuantityUsed += $conversionQuantity;
                        }
                    }
                }
                $materialVersions = MaterialVersion::where('material_id',$quotationMaterial['material_id'])->where('unit_id',$quotationMaterial['unit_id'])->pluck('id');
                $material_quantity = QuotationProduct::where('quotation_products.quotation_id',$quotation->id)
                    ->join('product_material_relation','quotation_products.product_version_id','=','product_material_relation.product_version_id')
                    ->whereIn('product_material_relation.material_version_id',$materialVersions)
                    ->sum(DB::raw('quotation_products.quantity * product_material_relation.material_quantity'));
                $quotationMaterialQuantity = 0;
                if($quotationMaterial->unit_id == $request->unit_id){
                    $quotationMaterialQuantity += $material_quantity;
                }else{
                    $unitConversionValue = UnitConversion::where('unit_1_id',$quotationMaterial->unit_id)->where('unit_2_id',$request->unit_id)->first();
                    if(count($unitConversionValue) > 0){
                        $conversionQuantity = $material_quantity * $unitConversionValue->unit_1_value;
                        $quotationMaterialQuantity += $conversionQuantity;
                    }else{
                        $reverseUnitConversionValue = UnitConversion::where('unit_1_id', $request->unit_id)->where('unit_2_id',$quotationMaterial->unit_id)->first();
                        $conversionQuantity = $material_quantity / $reverseUnitConversionValue->unit_2_value;
                        $quotationMaterialQuantity += $conversionQuantity;
                    }
                }
                $allowedQuantity = $quotationMaterialQuantity - $totalQuantityUsed;
                if($allowedQuantity < $request->quantity){
                    $status = 203;
                    $message = 'You have entered more than allowed quantity. Allowed quotation quantity is '.$allowedQuantity;
                }else{
                    $status = 200;
                    $message = 'Successful.';
                }
            }else{
                $status = 200;
                $message = 'Successful.';
            }
        }catch (\Exception $e){
            $data = [
                'action' => 'Validate Material Quantity',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            $status = 500;
            $message = 'Something went wrong';
            Log::critical(json_encode($data));
        }
        $response = [
            'message' => $message
        ];
        return response()->json($response, $status);
    }
}
