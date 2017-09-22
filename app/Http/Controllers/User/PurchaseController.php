<?php

namespace App\Http\Controllers\User;
use App\Assets;
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
use Dompdf\Exception;
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
  public function getMaterialRequestListing(Request $request){
      try{
          $userData = User::orderBy('id','asc')->get()->toArray();
          $iTotalRecords = count($userData);
          $records = array();
          $iterator = 0;
          for($iterator = 0,$pagination = $request->start; $iterator < $request->length && $iterator < count($userData); $iterator++,$pagination++ ){
              if($userData[$pagination]['is_active'] == true){
                  $user_status = '<td><span class="label label-sm label-success"> Enabled </span></td>';
                  $status = 'Disable';
              }else{
                  $user_status = '<td><span class="label label-sm label-danger"> Disabled</span></td>';
                  $status = 'Enable';
              }
              $records['data'][$iterator] = [
                  '<input type="checkbox">',
                  '1',
                  '<span><a href="#" data-toggle="tooltip" title="abbabbabababa"><i class="fa fa-info-circle" aria-hidden="true"></i> </a>&nbsp;&nbsp;'.$userData[$pagination]['first_name'].' '.$userData[$pagination]['last_name'].'</span>' ,
                  $userData[$pagination]['email'],
                   '<span><a href="#" data-toggle="tooltip" title="Hooray!"><i class="fa fa-info-circle" aria-hidden="true"></i></a> &nbsp;&nbsp;'. $userData[$pagination]['mobile'].'</span>',
                  date('d M Y',strtotime($userData[$pagination]['created_at'])),
                  $user_status,
                  '<div class="btn-group">
                        <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                            Actions
                            <i class="fa fa-angle-down"></i>
                        </button>
                        <ul class="dropdown-menu pull-left" role="menu">
                        <li>
                                <a href="/purchase/material-request/edit">
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
              'action' => 'User listing',
              'params' => $request->all(),
              'exception'=> $e->getMessage()
          ];
          Log::critical(json_encode($data));
          abort(500);
      }

      return response()->json($records,200);

  }
    public function editMaterialRequest(Request $request){
        return view('purchase/material-request/edit');
    }
    public function getMaterialsList(Request $request){
        $request['project_site_id'] = ProjectSite::where('name', $request['site'] )->pluck('id')->first();
        $iterator=0;
        $materialList = array();
        $quotation = Quotation::where('project_site_id',$request['project_site_id'])->first();
        $quotationMaterialId = Material::whereIn('id',array_column($quotation->quotation_materials->toArray(),'material_id'))
            ->where('name','ilike','%'.$request['keyword'].'%')->pluck('id');
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
            $materialList[$iterator]['unit_quantity'][0]['unit_id'] = $quotationMaterial->unit_id;
            $materialList[$iterator]['unit_quantity'][0]['unit_name'] = $quotationMaterial->unit->name;
            $unitConversionData = UnitConversion::where('unit_1_id',$quotationMaterial->unit_id)->get();
            $i = 1;
            foreach($unitConversionData as $key1 => $unitConversion){
                $materialList[$iterator]['unit_quantity'][$i]['quantity'] = $allowedQuantity * $unitConversion['unit_2_value'];
                $materialList[$iterator]['unit_quantity'][$i]['unit_id'] = $unitConversion->unit_2_id;
                $materialList[$iterator]['unit_quantity'][$i]['unit_name'] = $unitConversion->toUnit->name;
                $i++;
            }
            $materialList[$iterator]['material_request_component_type_slug'] = $quotationMaterialSlug->slug;
            $materialList[$iterator]['material_request_component_type_id'] = $quotationMaterialSlug->id;
            $iterator++;
        }
        $structureMaterials = Material::whereNotIn('id',$quotationMaterialId)->where('name','ilike','%'.$request->keyword.'%')->get();
        $structureMaterialSlug = MaterialRequestComponentTypes::where('slug','structure-material')->first();
        foreach($structureMaterials as $key1 => $material){
            $materialList[$iterator]['material_name'] = $material->name;
            $materialList[$iterator]['unit_quantity'][0]['quantity'] = null;
            $materialList[$iterator]['unit_quantity'][0]['unit_id'] = $material->unit_id;
            $materialList[$iterator]['unit_quantity'][0]['unit_name'] = $material->unit->name;
            $materialList[$iterator]['material_request_component_type_slug'] = $structureMaterialSlug->slug;
            $materialList[$iterator]['material_request_component_type_id'] = $structureMaterialSlug->id;
            $iterator++;
        }
        if(count($materialList) == 0){
            $materialList['material_name'] = null;
            $systemUnits = Unit::where('is_active',true)->get();
            $j = 0;
            foreach($systemUnits as $key2 => $unit){
                Log::info(5);
                $materialList['unit_quantity'][$j]['quantity'] = null;
                $materialList['unit_quantity'][$j]['unit_id'] = $unit->id;
                $materialList['unit_quantity'][$j]['unit_name'] = $unit->name;
                $j++;
            }
            $newMaterialSlug = MaterialRequestComponentTypes::where('slug','new-material')->first();
            $materialList['material_request_component_type_slug'] = $newMaterialSlug->slug;
            $materialList['material_request_component_type_id'] = $newMaterialSlug->id;
        }
        return($materialList);
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
    public function getAssetsList(Request $request){
        $data = $request->all();
        $units = Assets::where('name','ilike','%'.$data['keyword'].'%')->select('name','id')->get()->toarray();
        $opt= '';
        foreach ($units as $unit) {
            $opt .= '<li onclick="selectAssset(\''.htmlspecialchars($unit['name'], ENT_QUOTES).'\')">'.$unit['name'].'</li>';
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

          }




    }
}
