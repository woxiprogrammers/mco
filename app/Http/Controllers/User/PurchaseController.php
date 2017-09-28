<?php

namespace App\Http\Controllers\User;
use App\Asset;
use App\Client;
use App\Material;
use App\MaterialRequestComponents;
use App\MaterialRequestComponentTypes;
use App\MaterialRequests;
use App\MaterialVersion;
use App\Project;
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
    public function __construct()
    {
        $this->middleware('custom.auth');
    }
    public function getManageView(Request $request){
        return view('purchase/material-request/manage');
    }
    public function getCreateView(Request $request){
        return view('purchase/material-request/create');
    }
    public function getMaterialRequestIDFormat($project_site_id,$created_at,$serial_no){
    $format = "MR".$project_site_id.date_format($created_at,'y').date_format($created_at,'m').date_format($created_at,'d').$serial_no;
    return $format;
    }
    public function getMaterialRequestListing(Request $request){
      try{
          $materialRequests = MaterialRequests::get();
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
                  $materialRequestList[$iterator]['component_status'] = $materialRequestComponents->purchaseRequestComponentStatuses->name;
                  $materialRequestList[$iterator]['project_site_id'] =$materialRequest['project_site_id'];
                  $pro = $materialRequest->projectSite->project;
                  $materialRequestList[$iterator]['project_name'] =$pro->name;
                  $materialRequestList[$iterator]['client_name'] =$pro->client->company;
                  $materialRequestList[$iterator]['created_at'] =$materialRequest['created_at'];
                  $rm_id=2;
                  $materialRequestList[$iterator]['rm_id'] = $this->getMaterialRequestIDFormat($materialRequest['project_site_id'],$materialRequest['created_at'], $rm_id=2);
                  $iterator++;
              }
          }
          $iTotalRecords = count($materialRequestList);
          $records = array();
          $records['data'] = array();
          $iterator = 0;
          for($iterator = 0,$pagination = $request->start; $iterator < $request->length && $iterator < count($materialRequestList); $iterator++,$pagination++ ){
              if($materialRequestList[$pagination]['component_status'] == "pending"){
                  $user_status = '<td><span class="label label-sm label-danger">'. $materialRequestList[$pagination]['component_status'].' </span></td>';
                  $status = 'Disable';
              }else{
                  $user_status = '<td><span class="label label-sm label-success">'. $materialRequestList[$pagination]['component_status'].'</span></td>';
                  $status = 'Enable';
              }
              $records['data'][$iterator] = [
                  '<input type="checkbox">',
                  $materialRequestList[$pagination]['material_request_component_id'],
                  $materialRequestList[$pagination]['name'],
                  $materialRequestList[$pagination]['client_name'],
                  $materialRequestList[$pagination]['project_name'],
                  $materialRequestList[$pagination]['rm_id'],
                  $user_status,
                  '<div class="btn-group">
                        <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                            Actions
                            <i class="fa fa-angle-down"></i>
                        </button>
                        <ul class="dropdown-menu pull-left" role="menu">
                        <li>
                                <a href="/purchase/material-request/edit/">
                                    <i class="icon-docs"></i> Edit </a>
                            </li>
                            <li>
                                <a data-toggle="modal" data-target="#remarkModal">
                                    <i class="icon-tag"></i> Approve / Disapprove </a>
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
        $data = $request->all();
        $projects = ProjectSite::where('name','ilike','%'.$data['keyword'].'%')->select('name','id')->get()->toarray();
        $opt= '';
        foreach ($projects as $project) {
            $opt .= '<li onclick="selectProject(\''.htmlspecialchars($project['name'], ENT_QUOTES).'\','.$project['id'].')">'.$project['name'].'</li>';
        }
        $abc = $opt;
        $str3 = '<ul id="asset-list" style="border: 1px solid;height: 100px;overflow-y: overlay">'.$abc.'</ul>';
        $projects = $str3;
        return ($projects);
    }
    public function  getClientsList(Request $request){
        $data = $request->all();
        $clients = Client::where('company','ilike','%'.$data['keyword'].'%')->select('company','id')->get()->toarray();
        $opt= '';
        foreach ($clients as $client) {
            $opt .= '<li onclick="selectClient(\''.htmlspecialchars($client['company'], ENT_QUOTES).'\')">'.$client['company'].'</li>';
        }
        $abc = $opt;
        $str3 = '<ul id="client-list" style="border: 1px solid;height: 100px;overflow-y: overlay">'.$abc.'</ul>';
        $clients = $str3;
        return ($clients);
    }
    public function getUsersList(Request $request){
        $data = $request->all();
        $users = User::where('first_name','ilike','%'.$data['keyword'].'%')->select('first_name','last_name','id')->get()->toarray();
        $opt= '';
        foreach ($users as $user) {
            $opt .= '<li onclick="selectUser(\''.htmlspecialchars($user['first_name'], ENT_QUOTES).'\','.$user['id'].')">'.$user['first_name'].' '.$user['last_name'].'</li>';
        }
        $abc = $opt;
        $str3 = '<ul id="asset-list" style="border: 1px solid;height: 100px;overflow-y: overlay">'.$abc.'</ul>';
        $users = $str3;
        return ($users);
    }
    public function createMaterialList(Request $request){
          $data = $request->all();
          try{
                  $user = Auth::user();
                  $quotationId = Quotation::where('project_site_id',$data['project_site_id'])->pluck('id')->first();
                  $alreadyCreatedMaterialRequest = MaterialRequests::where('project_site_id',$data['project_site_id'])->where('user_id',$user['id'])->first();
                  if(count($alreadyCreatedMaterialRequest) > 0){
                      $materialRequest = $alreadyCreatedMaterialRequest;
                  }else{
                      $materialRequest['project_site_id'] = $data['project_site_id'];
                      $materialRequest['user_id'] = $user['id'];
                      $materialRequest['quotation_id'] = $quotationId != null ? $quotationId : null;
                      $materialRequest['assigned_to'] = $user['id'];
                      $materialRequest['on_behalf_of'] = $data['user_id'];
                      $materialRequest = MaterialRequests::create($materialRequest);
                  }
                  foreach($data['item_list'] as $key => $itemData){
                      $materialRequestComponentData['material_request_id'] = $materialRequest['id'];
                      $materialRequestComponentData['name'] = $itemData['name'];
                      $materialRequestComponentData['quantity'] = $itemData['quantity_id'];
                      $materialRequestComponentData['unit_id'] = $itemData['unit_id'];
                      $materialRequestComponentData['component_type_id'] = $itemData['component_type_id'];
                      $materialRequestComponentData['component_status_id'] = PurchaseRequestComponentStatuses::where('slug','pending')->pluck('id')->first();
                      $materialRequestComponentData['created_at'] = Carbon::now();
                      $materialRequestComponentData['updated_at'] = Carbon::now();
                      $materialRequestComponent[] = MaterialRequestComponents::insertGetId($materialRequestComponentData);
                  }
              $request->session()->flash('success', 'Material request created successfully.');
              return redirect('purchase/material-request/create');
          }catch(Exception $e){
              $request->session()->flash('error', 'Something went wrong.');
              return redirect('purchase/material-request/create');
          }
    }
    public function getMaterialRequestWiseListing(Request $request){
          try{
              $materialRequests = MaterialRequests::get();
              $materialRequestList = array();
              $iterator = 0;
              foreach($materialRequests as $key => $materialRequest){
                  $materialRequestList[$iterator]['project_site_id'] =$materialRequest['project_site_id'];
                  $pro = $materialRequest->projectSite->project;
                  $materialRequestList[$iterator]['project_name'] =$pro->name;
                  $materialRequestList[$iterator]['client_name'] =$pro->client->company;
                  $materialRequestList[$iterator]['created_at'] =$materialRequest['created_at'];
                  $rm_id=2;
                  $materialRequestList[$iterator]['rm_id'] = $this->getMaterialRequestIDFormat($materialRequest['project_site_id'],$materialRequest['created_at'], $rm_id=2);
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
                                    <i class="icon-docs"></i> Edit </a>
                            </li>
                            <li>
                                <a data-toggle="modal" data-target="#remarkModal">
                                    <i class="icon-tag"></i> Approve / Disapprove </a>
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

    /*public function changeStatus(Request $request){
        try{
            $materialRequestComponent = MaterialRequestComponents::where('id',$request['material_request_component_id'])->first();
            $quotationMaterialType = MaterialRequestComponentTypes::where('slug','quotation-material')->first();
            if($materialRequestComponent['component_type_id'] == $quotationMaterialType->id){
                $adminApproveComponentStatusId = PurchaseRequestComponentStatuses::where('slug','admin-approved')->pluck('id')->first();
                $usedQuantity = MaterialRequestComponents::where('id','!=',$materialRequestComponent->id)
                                ->where('material_request_id',$materialRequestComponent['material_request_id'])
                                ->where('component_type_id',$quotationMaterialType['id'])
                                ->where('component_status_id',$adminApproveComponentStatusId)
                                ->where('name',$materialRequestComponent['name'])->sum('quantity');
                $quotation = Quotation::where('project_site_id',$request['project_site_id'])->first();
                $quotationMaterialId = Material::whereIn('id',array_column($quotation->quotation_materials->toArray(),'material_id'))
                    ->where('name',$materialRequestComponent->name)->pluck('id')->first();
                $quotationMaterial = QuotationMaterial::where('quotation_id',$quotation->id)->where('material_id',$quotationMaterialId)->first();
                $materialVersions = MaterialVersion::where('material_id',$quotationMaterial['material_id'])->where('unit_id',$quotationMaterial['unit_id'])->pluck('id');
                $material_quantity = QuotationProduct::where('quotation_products.quotation_id',$quotation->id)
                    ->join('product_material_relation','quotation_products.product_version_id','=','product_material_relation.product_version_id')
                    ->whereIn('product_material_relation.material_version_id',$materialVersions)
                    ->sum(DB::raw('quotation_products.quantity * product_material_relation.material_quantity'));
                $allowedQuantity = $material_quantity - $usedQuantity;
                if((int)$materialRequestComponent['quantity'] < $allowedQuantity){
                    MaterialRequestComponents::where('id',$request['material_request_component_id'])->update(['component_status_id' => $request['change_component_status_id_to']]);
                    $message = "Status Updated Successfully";
                }else{
                    $message = "Allowed quantity is ".$allowedQuantity;
                }
            }else{
                MaterialRequestComponents::where('id',$request['material_request_component_id'])->update(['component_status_id' => $request['change_component_status_id_to']]);
                $message = "Status Updated Successfully";
            }

           $status = 200;
        }catch(\Exception $e){
            $status = 500;
            $message = "Fail";
            $data = [
                'action' => 'Change status of material request',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
        }
        $response = [
            "message" => $message,
        ];
        return response()->json($response,$status);
    }*/
    public function changeMaterialRequestComponentStatus(Request $request,$newStatus,$componentId = null){
        try{
            $materialComponentData = array();
            switch($newStatus){
                case 'admin-approved':
                    $componentIds = array();
                    if($componentId != null){
                        $componentIds[] = $componentId;
                    }else{
                        $componentIds = $request->component_id;
                    }
                    foreach($componentIds as $componentId){
                        $materialRequestComponent = MaterialRequestComponents::where('id',$componentId)->first();
                        $quotationMaterialType = MaterialRequestComponentTypes::where('slug','quotation-material')->first();
                        $projectSiteId = $materialRequestComponent->materialRequest->project_site_id;
                        if($materialRequestComponent['component_type_id'] == $quotationMaterialType->id){
                            $adminApproveComponentStatusId = PurchaseRequestComponentStatuses::where('slug','admin-approved')->pluck('id')->first();
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
                                MaterialRequestComponents::where('id',$request['material_request_component_id'])->update(['component_status_id' => $request['change_component_status_id_to']]);
                                $message = "Status Updated Successfully";
                            }else{
                                $message = "Allowed quantity is ".$allowedQuantity;
                            }
                        }else{
                            MaterialRequestComponents::where('id',$request['material_request_component_id'])->update(['component_status_id' => $request['change_component_status_id_to']]);
                            $message = "Status Updated Successfully";
                        }
                    }
                    break;
                case 'admin-disapproved': break;
                case 'in-indent': break;
            }
        }catch(\Exception $e){
            $data = [
                'action' => 'Change Material Request Component Statuses',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
        }
    }
}
