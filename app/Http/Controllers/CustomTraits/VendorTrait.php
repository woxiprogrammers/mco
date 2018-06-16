<?php
/**
 * Created by Ameya Joshi
 * Date: 23/5/17
 */

namespace App\Http\Controllers\CustomTraits;
use App\Category;
use App\City;
use App\Http\Requests\CategoryRequest;
use App\Material;
use App\State;
use App\Unit;
use App\Vendor;
use App\VendorCityRelation;
use App\VendorMaterialCityRelation;
use App\VendorMaterialRelation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use PhpParser\Node\Expr\Array_;

trait VendorTrait
{

    public function getCreateView(Request $request){
        try {
            $cities = City::get();
            $cityArray = Array();
            $iterator = 0;
            foreach ($cities as $city) {
                $cityArray[$iterator]['id'] = $city->id;
                $cityArray[$iterator]['name'] = $city->name.", ".$city->state->name.', '.$city->state->country->name;
                $iterator++;
            }
            $categories = Category::join('category_material_relations','category_material_relations.category_id','=','categories.id')
                ->where('is_active', true)
                ->select('categories.id as id','categories.name as name')
                ->distinct('id')
                ->orderBy('name','asc')
                ->get()
                ->toArray();
            return view('admin.vendors.create')->with(compact('cityArray','categories'));
        } catch (\Exception $e) {
            $data = [
                'action' => "Get vendor create view",
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getMaterials(Request $request,$category){
        try{
            $materials = Material::join('category_material_relations','materials.id','=','category_material_relations.material_id')
                ->where('category_material_relations.category_id',$category->id)
                ->where('materials.is_active', true)
                ->select('materials.id as id','materials.name as name')
                ->orderBy('materials.name','asc')
                ->get();
            $materialOptions = array();
            if($materials == null){
                $materialOptions[] = '<li> No material Available </li>';
            }else{
                foreach($materials as $material){
                    $materialOptions[] = '<li  class="list-group-item"><input type="checkbox" name="material_ids[]" value="'.$material->id.'"><span> '.$material->name.'</span></li>';
                }
            }
            $status = 200;
        }catch(\Exception $e){
            $status = 500;
            $materialOptions = array();
            $data = [
                'action' => 'Get Materials',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
        return response()->json($materialOptions,$status);
    }

    public function autoSuggest(Request $request, $keyword){
        try{
            $vendors = Vendor::where('name','ilike','%'.$keyword.'%')->select('id','name')->get();
            if($vendors == null){
                $response = array();
            }else{
                $response = $vendors->toArray();
            }
            $status = 200;
        }catch(\Exception $e){
            $data = [
                'action' => 'Vendor auto suggest',
                'param' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            $status = 500;
            $response = array();
        }
        return response()->json($response,$status);
    }

    public function getEditView(Request $request, $vendor){
        try {
            $cities = City::all();
            $cityArray = Array();
            $iterator = 0;
            foreach ($cities as $city) {
                $cityArray[$iterator]['id'] = $city->id;
                $cityArray[$iterator]['name'] = $city->name.", ".$city->state->name.', '.$city->state->country->name;
                $iterator++;
            }
            $categories = Category::join('category_material_relations','category_material_relations.category_id','=','categories.id')
                ->where('is_active', true)
                ->select('categories.id as id','categories.name as name')
                ->orderBy('name','asc')
                ->get()
                ->toArray();
            $vendorCities = array_column(($vendor->cityRelations->toArray()),'city_id');
            $vendorMaterialInfo = array();
            $isTransportationVendor = ($vendor['for_transportation'] == true) ? true : false;
            if($isTransportationVendor != true){
                foreach($vendor->materialRelations as $material){
                    $vendorMaterialInfo[$material['material_id']] = array();
                    $vendorMaterialInfo[$material['material_id']]['name'] = $material->material->name;
                    $vendorMaterialInfo[$material['material_id']]['cities'] = VendorMaterialCityRelation::join('vendor_material_relation','vendor_material_relation.id','=','vendor_material_city_relation.vendor_material_relation_id')
                        ->join('vendor_city_relation','vendor_city_relation.id','=','vendor_material_city_relation.vendor_city_relation_id')
                        ->where('vendor_material_relation.material_id',$material['material_id'])
                        ->pluck('vendor_city_relation.city_id')
                        ->toArray();

                }
            }


            return view('admin.vendors.edit')->with(compact('vendor','cityArray','categories','vendorCities','vendorMaterialInfo','isTransportationVendor'));        } catch (\Exception $e) {
            $data = [
                'action' => "Get vendor edit view",
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getManageView(Request $request){
        try {
            return view('admin.vendors.manage');
        } catch (\Exception $e) {
            $data = [
                'action' => 'Get Vendor manage view',
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function createVendor(Request $request){
        try {
            $data = Array();
            $data['name'] = ucwords($request->name);
            $data['company'] = $request->company;
            $data['mobile'] = $request->mobile;
            $data['email'] = $request->email;
            $data['gstin'] = $request->gstin;
            $data['alternate_contact'] = $request->alternate_contact;
            $data['alternate_email'] = $request->alternate_email;
            $data['is_active'] = false;
            $data['for_transportation'] = ($request->has('transportation_vendor')) ? true : false;
            $vendor = Vendor::create($data);
            $vendorCityData = array();
            $vendorMaterialData = array();
            $vendorCityData['vendor_id'] = $vendor->id;
            $vendorMaterialData['vendor_id'] = $vendor->id;
            $vendorCityRelation = array();
            foreach($request->cities as $cityId){
                $vendorCityData['city_id'] = $cityId;
                $vendorCity = VendorCityRelation::create($vendorCityData);
                $vendorCityRelation[$cityId] = $vendorCity->id;
            }
            if($request ->has( 'material_city')) {
                $materialIds = array_keys($request->material_city);
                foreach ($materialIds as $materialId) {
                    $vendorMaterialData['material_id'] = $materialId;
                    $vendorMaterial = VendorMaterialRelation::create($vendorMaterialData);
                    $vendorMaterialCityData = array();
                    $vendorMaterialCityData['vendor_material_relation_id'] = $vendorMaterial->id;
                    foreach ($request->material_city[$materialId] as $materialCityId) {
                        $vendorMaterialCityData['vendor_city_relation_id'] = $vendorCityRelation[$materialCityId];
                        VendorMaterialCityRelation::create($vendorMaterialCityData);
                    }
                }
            }
            $request->session()->flash('success', 'Vendor Created successfully.');
            return redirect('/vendors/manage');
        } catch (\Exception $e) {
            $data = [
                'action' => 'Create Vendor',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function editVendor(Request $request, $vendor){
        try {
            $currentVendorCities = array_column(($vendor->cityRelations->toArray()),'city_id');
            if($request->has('cities')){
                $deletedCities = array_diff($currentVendorCities,$request->cities);
                if($deletedCities != null){
			$vendorCityRelationIds = VendorCityRelation::where('vendor_id',$vendor->id)->whereIn('city_id',$deletedCities)->pluck('id');
	                $deletedMaterialCityMaterialIds = VendorMaterialCityRelation::join('vendor_material_relation','vendor_material_relation.id','=','vendor_material_city_relation.vendor_material_relation_id')
                                                    ->whereIn('vendor_material_city_relation.vendor_city_relation_id',$vendorCityRelationIds)
                                                    ->count();
        	        if(count($deletedMaterialCityMaterialIds) > 0){
                	    $request->session()->flash('success', 'City is already assigned to material');
	                    return redirect('/vendors/edit/'.$vendor->id);
        	        }
		}
            }
            $data = $request->except(['cities','material','material_city','_token','_method']);
            $data['name'] = ucwords(trim($data['name']));
            $vendor->update($data);
            $vendorCityData = array();
            $vendorMaterialData = array();
            $vendorCityData['vendor_id'] = $vendor->id;
            $vendorMaterialData['vendor_id'] = $vendor->id;
            $vendorCityRelation = array();
            $currentVendorCities = array_column(($vendor->cityRelations->toArray()),'city_id');
            if($request->has('cities')){
                $deletedCities = array_diff($currentVendorCities,$request->cities);
                foreach($request->cities as $cityId){
                    $vendorCity = VendorCityRelation::where('vendor_id',$vendor->id)->where('city_id',$cityId)->first();
                    if($vendorCity == null){
                        $vendorCityData['city_id'] = $cityId;
                        $vendorCity = VendorCityRelation::create($vendorCityData);
                    }
                    $vendorCityRelation[$cityId] = $vendorCity->id;
                }
            }else{
                $deletedCities = $currentVendorCities;
            }
            $deletedCitiesId = VendorCityRelation::where('vendor_id',$vendor->id)->whereIn('city_id',$deletedCities)->pluck('id');
            VendorMaterialCityRelation::whereIn('vendor_city_relation_id',$deletedCitiesId)->delete();
            VendorCityRelation::where('vendor_id',$vendor->id)->whereIn('city_id',$deletedCities)->delete();

            if($request ->has( 'material_city')) {
                $materialIds = array_keys($request->material_city);
                VendorMaterialRelation::where('vendor_id', $vendor->id)->whereNotIn('material_id', $materialIds)->delete();
                foreach ($materialIds as $materialId) {
                    $vendorMaterial = VendorMaterialRelation::where('vendor_id', $vendor->id)->where('material_id', $materialId)->first();
                    if ($vendorMaterial == null) {
                        $vendorMaterialData['material_id'] = $materialId;
                        $vendorMaterial = VendorMaterialRelation::create($vendorMaterialData);
                    }
                    $vendorMaterialCityData = array();
                    $vendorMaterialCityData['vendor_material_relation_id'] = $vendorMaterial->id;
                    foreach ($request->material_city[$materialId] as $materialCityId) {
                        $vendorMaterialCity = VendorMaterialCityRelation::where('vendor_material_relation_id', $vendorMaterial->id)->where('vendor_city_relation_id', $vendorCityRelation[$materialCityId])->first();
                        $deletedCityRelation = VendorCityRelation::where('vendor_id',$vendor->id)->whereNotIn('city_id', $request->material_city[$materialId])->pluck('id')->toArray();
                        VendorMaterialCityRelation::where('vendor_material_relation_id', $vendorMaterial->id)->whereIn('vendor_city_relation_id', $deletedCityRelation)->delete();
                        if ($vendorMaterialCity == null) {
                            $vendorMaterialCityData['vendor_city_relation_id'] = $vendorCityRelation[$materialCityId];
                            VendorMaterialCityRelation::create($vendorMaterialCityData);
                        }
                    }
                }
            }else{
                $vendorMaterialRelations = VendorMaterialRelation::where('vendor_id', $vendor->id)->get();
                foreach($vendorMaterialRelations as $vendorMaterialRelation){
                    foreach($vendorMaterialRelation->vendorCityRelation as $vendorCityRelation){
                        VendorMaterialCityRelation::where('id', $vendorCityRelation->id)->delete();
                    }
                    VendorMaterialRelation::where('id', $vendorMaterialRelation->id)->delete();
                }
            }
            $request->session()->flash('success', 'Vendor Edited successfully.');
            return redirect('/vendors/manage');
        } catch (\Exception $e) {
            $data = [
                'action' => 'Edit Vendor',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function vendorListing(Request $request){
        try {
            $user = Auth::user();
            $vendorId = Vendor::pluck('id')->toArray();
            if($request->has('search_company')){
                $vendorId = Vendor::whereIn('id',$vendorId)->where('company','ilike','%'.$request->search_company.'%')->pluck('id')->toArray();
            }
            if ($request->has('search_name')) {
                $vendorId = Vendor::whereIn('id',$vendorId)->where('name','ilike','%'.$request->search_name.'%')->pluck('id')->toArray();
            }
            $vendorsData = Vendor::whereIn('id', $vendorId)->orderBy('id','desc')->get();
            $iTotalRecords = count($vendorsData);
            $records = array();
            $records['data'] = array();
            $end = $request->length < 0 ? count($vendorsData) : $request->length;
            for ($iterator = 0, $pagination = $request->start; $iterator < $end && $pagination < count($vendorsData); $iterator++, $pagination++) {
                if ($vendorsData[$pagination]['is_active'] == true) {
                    $vendor_status = '<td><span class="label label-sm label-success"> Enabled </span></td>';
                    $status = 'Disable';
                } else {
                    $vendor_status = '<td><span class="label label-sm label-danger"> Disabled</span></td>';
                    $status = 'Enable';
                }
                if($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin' || $user->customHasPermission('approve-manage-user')){
                    $actionButton = '<div class="btn-group">
                        <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                            Actions
                            <i class="fa fa-angle-down"></i>
                        </button>
                        <ul class="dropdown-menu pull-left" role="menu">
                            <li>
                                <a href="/vendors/edit/' . $vendorsData[$pagination]['id'] . '">
                                <i class="icon-docs"></i> Edit </a>
                        </li>
                        <li>
                            <a href="/vendors/change-status/' . $vendorsData[$pagination]['id'] . '">
                                <i class="icon-tag"></i> ' . $status . ' </a>
                        </li>
                    </ul>
                </div>';
                }else{
                    $actionButton = '<div class="btn-group">
                        <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                            Actions
                            <i class="fa fa-angle-down"></i>
                        </button>
                        <ul class="dropdown-menu pull-left" role="menu">
                            <li>
                                <a href="/vendors/edit/' . $vendorsData[$pagination]['id'] . '">
                                <i class="icon-docs"></i> Edit </a>
                        </li>
                    </ul>
                </div>';
                }
                $records['data'][$iterator] = [
                    $vendorsData[$pagination]['company'],
                    $vendorsData[$pagination]['name'],
                    $vendorsData[$pagination]['mobile'],
                    $vendor_status,
                    $actionButton
                ];
            }
            $records["draw"] = intval($request->draw);
            $records["recordsTotal"] = $iTotalRecords;
            $records["recordsFiltered"] = $iTotalRecords;
        } catch (\Exception $e) {
            $records = array();
            $data = [
                'action' => 'Get Vendor Listing',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
        return response()->json($records);
    }

    public function changeVendorStatus(Request $request, $vendor){
        try {
            $newStatus = (boolean)!$vendor->is_active;
            $vendor->update(['is_active' => $newStatus]);
            $request->session()->flash('success', 'Vendor Status changed successfully.');
            return redirect('/vendors/manage');
        } catch (\Exception $e) {
            $data = [
                'action' => 'Change vendor status',
                'param' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function checkVendorName(Request $request){
        try {
            $vendorName = $request->name;
            if ($request->has('vendor_id')) {
                $nameCount = Vendor::where('name', 'ilike', $vendorName)->where('id', '!=', $request->vendor_id)->count();
            } else {
                $nameCount = Vendor::where('name', 'ilike', $vendorName)->count();
            }
            if ($nameCount > 0) {
                return 'false';
            } else {
                return 'true';
            }
        } catch (\Exception $e) {
            $data = [
                'action' => 'Check Vendor name',
                'param' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }
}
