<?php

namespace App\Http\Controllers\User;
use App\Asset;
use App\Client;
use App\Http\Controllers\CustomTraits\Purchase\MaterialRequestTrait;
use App\Material;
use App\MaterialRequestComponentHistory;
use App\MaterialRequestComponents;
use App\MaterialRequestComponentTypes;
use App\MaterialRequests;
use App\MaterialVersion;
use App\ProjectSite;
use App\PurchaseRequestComponentStatuses;
use App\Quotation;
use App\QuotationMaterial;
use App\QuotationProduct;
use App\Unit;
use App\UnitConversion;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PurchaseController extends Controller
{
    use MaterialRequestTrait;
    public function __construct()
    {
        $this->middleware('custom.auth');
    }
    public function getManageView(Request $request){
        try{
            $units = Unit::where('is_active', true)->select('id','name')->get();
            return view('purchase/material-request/manage')->with(compact('units'));
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
        $nosUnitId = Unit::where('slug','nos')->pluck('id')->first();
        $units = Unit::select('id','name')->get()->toArray();
        return view('purchase/material-request/create')->with(compact('nosUnitId','units'));
    }
    public function getMaterialRequestListing(Request $request){
        try{
          $materialRequests = MaterialRequests::orderBy('id','desc')->get();
          $materialRequestList = array();
          $iterator = 0;
          foreach($materialRequests as $key => $materialRequest){
              foreach($materialRequest->materialRequestComponents as $key => $materialRequestComponents){
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
                  $materialRequestList[$iterator]['created_at'] =$materialRequest['created_at'];
                  $materialRequestList[$iterator]['rm_id'] = $this->getPurchaseIDFormat('material-request-component',$materialRequest['project_site_id'],$materialRequest['created_at'],$materialRequestComponents->serial_no);
                  $iterator++;
              }
          }
          $iTotalRecords = count($materialRequestList);
          $records = array();
          $records['data'] = array();
          $assetComponentTypeIds = MaterialRequestComponentTypes::whereIn('slug',['system-asset','new-asset'])->pluck('id')->toArray();
          for($iterator = 0,$pagination = $request->start; $iterator < $request->length && $iterator < count($materialRequestList); $iterator++,$pagination++ ){
              switch(strtolower($materialRequestList[$pagination]['component_status'])){
                  case 'pending':
                      if(in_array($materialRequestList[$pagination]['component_type_id'],$assetComponentTypeIds)){
                          $unitEditable = 'false';
                      }else{
                            $unitEditable = 'true';
                      }
                      $user_status = '<td><span class="label label-sm label-danger">'. $materialRequestList[$pagination]['component_status_name'].' </span></td>';
                      $actionDropDown = '<div class="btn-group">
                            <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                Actions
                                <i class="fa fa-angle-down"></i>
                            </button>
                            <ul class="dropdown-menu pull-left" role="menu">
                                <li>
                                    <a href="javascript:void(0);">
                                        <i class="icon-docs"></i> Edit 
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);" onclick="openApproveModal('.$materialRequestList[$pagination]['material_request_component_id'].')">
                                        <i class="icon-tag"></i> Approve / Disapprove 
                                    </a>
                                </li>
                            </ul>
                        </div>';
                      break;

                  case 'admin-approved':
                      $user_status = '<td><span class="label label-sm label-success">'. $materialRequestList[$pagination]['component_status_name'].' </span></td>';
                      $actionDropDown = '<div class="btn-group">
                            <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                Actions
                                <i class="fa fa-angle-down"></i>
                            </button>
                            <ul class="dropdown-menu pull-left" role="menu">
                                <li>
                                    <a href="javascript:void(0);">
                                        <i class="icon-docs"></i> Edit 
                                    </a>
                                </li>
                                <li>
                                    <form action="/purchase/material-request/change-status/in-indent/'.$materialRequestList[$pagination]['material_request_component_id'].'" method="post">
                                        <a href="javascript:void(0);" onclick="submitIndentForm(this)">
                                            <i class="icon-tag"></i> Move To indent 
                                        </a>
                                        <input type="hidden" name="_token">
                                    </form>
                                </li>
                            </ul>
                        </div>';
                      break;

                  case 'admin-disapproved':
                      $user_status = '<td><span class="label label-sm label-success">'. $materialRequestList[$pagination]['component_status_name'].' </span></td>';
                      $actionDropDown = '<div class="btn-group">
                            <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                Actions
                                <i class="fa fa-angle-down"></i>
                            </button>
                            <ul class="dropdown-menu pull-left" role="menu">
                                <li>
                                    <a href="javascript:void(0);">
                                        <i class="icon-docs"></i> Edit 
                                    </a>
                                </li>
                            </ul>
                        </div>';
                      break;

                  case 'in-indent':
                      $user_status = '<td><span class="label label-sm label-success">'. $materialRequestList[$pagination]['component_status_name'].' </span></td>';
                      $actionDropDown = '<div class="btn-group">
                            <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                Actions
                                <i class="fa fa-angle-down"></i>
                            </button>
                            <ul class="dropdown-menu pull-left" role="menu">
                                <li>
                                    <a href="javascript:void(0);">
                                        <i class="icon-docs"></i> Edit 
                                    </a>
                                </li>
                            </ul>
                        </div>';
                      break;

                  default:
                      $user_status = '<td><span class="label label-sm label-success">'. $materialRequestList[$pagination]['component_status_name'].' </span></td>';
                      $actionDropDown = '<div class="btn-group">
                            <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                Actions
                                <i class="fa fa-angle-down"></i>
                            </button>
                            <ul class="dropdown-menu pull-left" role="menu">
                                <li>
                                    <a href="javascript:void(0);">
                                        <i class="icon-docs"></i> Edit 
                                    </a>
                                </li>
                            </ul>
                        </div>';
                      break;
              }
              $records['data'][$iterator] = [
                  '<input type="checkbox" class="multiple-select-checkbox" value="'.$materialRequestList[$pagination]['material_request_component_id'].'">',
                  $materialRequestList[$pagination]['material_request_component_id'],
                  $materialRequestList[$pagination]['name'],
                  $materialRequestList[$pagination]['client_name'],
                  $materialRequestList[$pagination]['project_name'],
                  $materialRequestList[$pagination]['rm_id'],
                  $user_status,
                  $actionDropDown
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
    public function editMaterialRequest(Request $request){
        return view('purchase/material-request/edit');
    }
    public function autoSuggest(Request $request){
        try{
            $request['project_site_id'] = ProjectSite::where('name', $request['site'] )->pluck('id')->first();
            $message = "Success";
            $iterator = 0;
            $data = array();
            switch($request->search_in){
                case 'material' :
                    $materialList = array();
                    $quotation = Quotation::where('project_site_id',$request['project_site_id'])->first();
                    if(count($quotation) > 0){
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
                        $materialList[$iterator]['material_name'] = null;
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
                    $data= $materialList;
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
                        $assetList[$iterator]['asset_name'] = null;
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
    public function getUnitsList(Request $request){
        try{
            $data = $request->all();
            $units = Unit::where('name','ilike','%'.$data['keyword'].'%')->select('name','id')->get()->toarray();
            $opt= '';
            foreach ($units as $unit) {
                $opt .= '<li onclick="selectUnit(\''.htmlspecialchars($unit['name'], ENT_QUOTES).'\')">'.$unit['name'].'</li>';
            }
            $abc = $opt;
            $str3 = '<ul id="material-list" style="border: 1px solid;height: 100px;overflow-y: overlay">'.$abc.'</ul>';
            $units = $str3;
            return ($units);
        }catch(\Exception $e){
            $data = [
                'action' => 'M.R. Get Unit List',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            $status = 500;
            $units = null;
        }
        return response()->json($units,$status);
    }
    public function getAssetUnitsList(Request $request){
        $data = $request->all();
        $units = Unit::where('name','ilike','%'.$data['keyword'].'%')->select('name','id')->get()->toarray();
        $opt= '';
        foreach ($units as $unit) {
            $opt .= '<li onclick="selectAssetUnit(\''.htmlspecialchars($unit['name'], ENT_QUOTES).'\')">'.$unit['name'].'</li>';
        }
        $abc = $opt;
        $str3 = '<ul id="asset-list" style="border: 1px solid;height: 100px;overflow-y: overlay">'.$abc.'</ul>';
        $units = $str3;
        return ($units);
    }

    public function getProjectsList(Request $request){
        try{
            $data = $request->all();
            $user = Auth::user();
            if($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin'){
                $projects = ProjectSite::join('projects','projects.id','=','project_sites.project_id')
                                    ->join('clients','clients.id','=','projects.client_id')
                                    ->where('project_sites.name','ilike','%'.$data['keyword'].'%')
                                    ->where('clients.company','ilike','%'.$data['client_name'].'%')
                                    ->select('project_sites.name as name','project_sites.id as id')
                                    ->get()
                                    ->toarray();
            }else{
                $projects = ProjectSite::join('projects','projects.id','=','project_sites.project_id')
                    ->join('clients','clients.id','=','projects.client_id')
                    ->join('user_project_site_relation','user_project_site_relation.project_site_id','=','user_project_sites.id')
                    ->where('project_sites.name','ilike','%'.$data['keyword'].'%')
                    ->where('clients.company','ilike','%'.$data['client_name'].'%')
                    ->where('user_project_site_relation.user_id',$user->id)
                    ->select('project_sites.name as name','project_sites.id as id')
                    ->get()
                    ->toarray();
            }
            $opt= '';
            foreach ($projects as $project) {
                $opt .= '<li onclick="selectProject(\''.htmlspecialchars($project['name'], ENT_QUOTES).'\','.$project['id'].')">'.$project['name'].'</li>';
            }
            $abc = $opt;
            $str3 = '<ul id="asset-list" style="border: 1px solid;height: 100px;overflow-y: overlay">'.$abc.'</ul>';
            $projects = $str3;
            $status = 200;
        }catch(\Exception $e){
            $data = [
                'action' => 'Get Project List',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            $status = 500;
            $projects = '';
        }
        return response()->json($projects,$status);

    }
    public function  getClientsList(Request $request){
        try{
            $status = 200;
            $data = $request->all();
            $user = Auth::user();
            if($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin'){
                $clients = Client::where('company','ilike','%'.$data['keyword'].'%')->select('company','id')->get()->toarray();
            }else{
                $clients = Client::join('projects','projects.client_id','=','clients.id')
                    ->join('project_sites','project_sites.project_id','=','projects.id')
                    ->join('user_project_site_relation','user_project_site_relation.project_site_id','=','project_sites.id')
                    ->where('clients.company','ilike','%'.$data['keyword'].'%')
                    ->where('user_project_site_relation.user_id',$user->id)
                    ->select('clients.company as company','clients.id as id')
                    ->distinct('id')
                    ->get();
            }
            $opt= '';
            foreach ($clients as $client) {
                $opt .= '<li onclick="selectClient(\''.htmlspecialchars($client['company'], ENT_QUOTES).'\')">'.$client['company'].'</li>';
            }
            $abc = $opt;
            $str3 = '<ul id="client-list" style="border: 1px solid;height: 100px;overflow-y: overlay">'.$abc.'</ul>';
            $clients = $str3;
        }catch(\Exception $e){
            $data = [
                'action' => 'Get Client List',
                'params' => $request->all(),
                'exception' => $request->all()
            ];
            Log::critical(json_encode($data));
            $status = 500;
            $clients = '';
        }
        return response()->json($clients,$status);

    }
    public function getUsersList(Request $request){
        try{
            $data = $request->all();
            $projectSite = ProjectSite::where('name','ilike',$data['project_site_name'])->pluck('id')->first();
            $adminUsers = User::join('user_has_roles','user_has_roles.user_id','=','users.id')
                                ->join('roles','roles.id','=','user_has_roles.role_id')
                                ->whereIn('roles.slug',['admin','superadmin'])
                                ->where(function($query) use($data){
                                    $query->where('users.first_name','ilike','%'.$data['keyword'].'%');
                                    $query->orWhere('users.last_name','ilike','%'.$data['keyword'].'%');
                                })
                                ->select('users.first_name as first_name','users.last_name as last_name','users.id as id')
                                ->get()->toArray();
            $users = User::join('user_has_roles','user_has_roles.user_id','=','users.id')
                        ->join('roles','roles.id','=','user_has_roles.role_id')
                        ->join('user_project_site_relation','user_project_site_relation.user_id','=','users.id')
                        ->whereNotIn('roles.slug',['admin','superadmin'])
                        ->where(function($query) use($data){
                            $query->where('users.first_name','ilike','%'.$data['keyword'].'%');
                            $query->orWhere('users.last_name','ilike','%'.$data['keyword'].'%');
                        })
                        ->where('user_project_site_relation.project_site_id',$projectSite)
                        ->select('users.first_name as first_name','users.last_name as last_name','users.id as id')
                        ->get()->toArray();
            $users = array_merge($adminUsers,$users);
            $opt= '';
            foreach ($users as $user) {
                $opt .= '<li onclick="selectUser(\''.htmlspecialchars($user['first_name'], ENT_QUOTES).'\','.$user['id'].')">'.$user['first_name'].' '.$user['last_name'].'</li>';
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
            return redirect('purchase/material-request/create');
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
              $materialRequests = MaterialRequests::orderBy('id','desc')->get();
              $materialRequestList = array();
              $iterator = 0;
              foreach($materialRequests as $key => $materialRequest){
                  $materialRequestList[$iterator]['project_site_id'] =$materialRequest['project_site_id'];
                  $pro = $materialRequest->projectSite->project;
                  $materialRequestList[$iterator]['project_name'] =$pro->name;
                  $materialRequestList[$iterator]['client_name'] =$pro->client->company;
                  $materialRequestList[$iterator]['created_at'] =$materialRequest['created_at'];
                  $materialRequestList[$iterator]['rm_id'] = $this->getPurchaseIDFormat('material-request',$materialRequest['project_site_id'],$materialRequest['created_at'],$materialRequest->serial_no);
                  $iterator++;
              }
              $iTotalRecords = count($materialRequestList);
              $records = array();
              $iterator = 0;
              $records['data'] = array();
              for($iterator = 0,$pagination = $request->start; $iterator < $request->length && $iterator < count($materialRequestList); $iterator++,$pagination++ ){
                  $records['data'][$iterator] = [
                      '<input type="checkbox">',
                      $materialRequestList[$pagination]['rm_id'],
                      $materialRequestList[$pagination]['client_name'],
                      $materialRequestList[$pagination]['project_name'],
                      '<div class="btn-group">
                        <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                            Actions
                            <i class="fa fa-angle-down"></i>
                        </button>
                        <ul class="dropdown-menu pull-left" role="menu">
                            <li>
                                <a href="/purchase/material-request/edit/">
                                    <i class="icon-docs"></i> Edit 
                                </a>
                            </li>
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
        return view ('purchase/material-request/material-request-listing');
    }

    public function changeMaterialRequestComponentStatus(Request $request,$newStatus,$componentId = null){
        try{
            switch($newStatus){
                case 'admin-approved':
                    $componentIds = $request->component_id;
                    if($request->has('request')){
                        $remark = $request->remark;
                    }else{
                        $remark = '';
                    }
                    foreach($componentIds as $componentId){
                        $materialRequestComponent = MaterialRequestComponents::where('id',$componentId)->first();
                        if($materialRequestComponent->purchaseRequestComponentStatuses->slug == 'pending'){
                            if($request->has('quantity')){
                                $materialRequestComponent->update(['quantity' => $request->quantity]);
                            }
                            if($request->has('unit_id')){
                                $materialRequestComponent->update(['unit_id' => $request->unit_id]);
                            }
                            $quotationMaterialType = MaterialRequestComponentTypes::where('slug','quotation-material')->first();
                            $projectSiteId = $materialRequestComponent->materialRequest->project_site_id;
                            $adminApproveComponentStatusId = PurchaseRequestComponentStatuses::where('slug','admin-approved')->pluck('id')->first();
                            $materialComponentHistoryData = array();
                            $materialComponentHistoryData['component_status_id'] = $adminApproveComponentStatusId;
                            $materialComponentHistoryData['remark'] = $remark;
                            $materialComponentHistoryData['user_id'] = Auth::user()->id;
                            $materialComponentHistoryData['material_request_component_id'] = $componentId;
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
                                if((int)$materialRequestComponent['quantity'] < $allowedQuantity){
                                    MaterialRequestComponents::where('id',$componentId)->update(['component_status_id' => $adminApproveComponentStatusId]);
                                    $message = "Status Updated Successfully";
                                    MaterialRequestComponentHistory::create($materialComponentHistoryData);
                                }else{
                                    $message = "Allowed quantity is ".$allowedQuantity;
                                }
                            }else{
                                MaterialRequestComponents::where('id',$componentId)->update(['component_status_id' => $adminApproveComponentStatusId]);
                                $message = "Status Updated Successfully";
                                MaterialRequestComponentHistory::create($materialComponentHistoryData);
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
                    $materialComponentHistoryData['component_status_id'] = $adminDisapproveStatusId;
                    $materialComponentHistoryData['remark'] = $remark;
                    $materialComponentHistoryData['user_id'] = Auth::user()->id;
                    foreach($componentIds as $componentId){
                        MaterialRequestComponents::where('id',$componentId)->update(['component_status_id' => $adminDisapproveStatusId]);
                        $materialComponentHistoryData['material_request_component_id'] = $componentId;
                        MaterialRequestComponentHistory::create($materialComponentHistoryData);
                    }
                    break;

                case 'in-indent':
                    $inIndentStatusId = PurchaseRequestComponentStatuses::where('slug',$newStatus)->pluck('id')->first();
                    MaterialRequestComponents::where('id',$componentId)->update(['component_status_id' => $inIndentStatusId]);
                    $materialComponentHistoryData['material_request_component_id'] = $componentId;
                    $materialComponentHistoryData = array();
                    $materialComponentHistoryData['component_status_id'] = $inIndentStatusId;
                    $materialComponentHistoryData['user_id'] = Auth::user()->id;
                    $materialComponentHistoryData['material_request_component_id'] = $componentId;
                    MaterialRequestComponentHistory::create($materialComponentHistoryData);
                    break;
            }
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

    public function getMaterialRequestComponentDetail(Request $request,$materialRequestComponent){
        try{
            $assetComponentTypes = MaterialRequestComponentTypes::whereIn('slug',['system-asset','new-asset'])->pluck('id')->toArray();
            if(in_array($materialRequestComponent->component_type_id,$assetComponentTypes)){
                $nosUnit = Unit::where('slug','nos')->select('id','name')->first();
                $units = "<option value='$nosUnit->id'>$nosUnit->name</option>";
            }else{
                $newMaterialTypeId = MaterialRequestComponentTypes::where('slug','new-material')->pluck('id')->first();
                if($newMaterialTypeId == $materialRequestComponent->component_type_id){
                    $unitData = Unit::where('is_active',true)->select('id','name')->orderBy('name')->get()->toArray();
                }else{
                    $material = Material::where('name','ilike',$materialRequestComponent->name)->first();
                    $unit1Array = UnitConversion::join('units','units.id','=','unit_conversions.unit_2_id')
                                                ->where('unit_conversions.unit_1_id',$material->unit_id)
                                                ->select('units.id as id','units.name as name')
                                                ->get()
                                                ->toArray();
                    $units2Array = UnitConversion::join('units','units.id','=','unit_conversions.unit_1_id')
                                                ->where('unit_conversions.unit_2_id',$material->unit_id)
                                                ->whereNotIn('unit_conversions.unit_1_id',array_column($unit1Array,'id'))
                                                ->select('units.id as id','units.name as name')
                                                ->get()
                                                ->toArray();
                    $unitData = array_merge($unit1Array,$units2Array);
                }
                for($iterator = 0,$units='';$iterator < count($unitData); $iterator++){
                    if($unitData[$iterator]['id'] == $materialRequestComponent->unit_id){
                        $units .= "<option value='".$unitData[$iterator]['id']."' selected>".$unitData[$iterator]['name']."</option>";
                    }else{
                        $units .= "<option value='".$unitData[$iterator]['id']."'>".$unitData[$iterator]['name']."</option>";
                    }
                }
            }
            $response = [
                'units' => $units,
                'quantity' => $materialRequestComponent->quantity
            ];
            $status = 200;
        }catch(\Exception $e){
            $data = [
                'action' => 'Get Material Component Details',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            $status = 500;
            $response = null;
        }
        return response()->json($response,$status);
    }
}
