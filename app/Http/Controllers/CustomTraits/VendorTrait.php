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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PhpParser\Node\Expr\Array_;

trait VendorTrait
{

    public function getCreateView(Request $request)
    {
        try {

            $cities = City::get();
            $cityArray = Array();
            $iterator = 0;
            foreach ($cities as $city) {
                $cityArray[$iterator]['id'] = $city->id;
                $cityArray[$iterator]['name'] = $city->name.", ".$city->state->name.', '.$city->state->country->name;
                $iterator++;
            }
            $categories = Category::where('is_active', true)->select('id','name')->orderBy('name','asc')->get()->toArray();

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
                $materialOptions[] = '<option value=""> No material Available </option>';
            }else{
                foreach($materials as $material){
                    $materialOptions[] = '<li  class="list-group-item"><input type="checkbox" name="material_ids" value="'.$material->id.'"><span> '.$material->name.'</span></li>';
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


    public function getEditView(Request $request, $vendor)
    {
        try {
            $vendor = $vendor->toArray();
            return view('admin.vendors.edit')->with(compact('vendor'));
        } catch (\Exception $e) {
            $data = [
                'action' => "Get vendor edit view",
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getManageView(Request $request)
    {
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

    public function createVendor(Request $request)
    {
        try {
            $data = Array();
            $data['name'] = ucwords($request->name);
            $data['company'] = $request->company;
            $data['mobile'] = $request->mobile;
            $data['email'] = $request->email;
            $data['gstin'] = $request->gstin;
            $data['alternate_contact'] = $request->alternate_contact;
            $data['is_active'] = false;
            $vendor = Vendor::create($data);
            $request->session()->flash('success', 'Vendor Created successfully.');
            return redirect('/vendors/create');
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

    public function editVendor(Request $request, $vendor)
    {
        try {
            $data = $request->all();
            $vendorData['name'] = ucwords(trim($data['name']));
            $vendorData['company'] = $data['company'];
            $vendorData['mobile'] = $data['mobile'];
            $vendorData['email'] = $data['email'];
            $vendorData['gstin'] = $data['gstin'];
            $vendorData['alternate_contact'] = $data['alternate_contact'];
            $vendorData['city'] = $data['city'];
            $vendor->update($vendorData);
            $request->session()->flash('success', 'Vendor Edited successfully.');
            return redirect('/vendors/edit/' . $vendor->id);
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

    public function vendorListing(Request $request)
    {
        try {
            if ($request->has('search_name')) {
                $vendorsData = Vendor::where('name', 'ilike', '%' . $request->search_name . '%')->orderBy('name', 'asc')->get()->toArray();
            } else {
                $vendorsData = Vendor::orderBy('name', 'asc')->get()->toArray();
            }
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
                $records['data'][$iterator] = [
                    $vendorsData[$pagination]['name'],
                    $vendorsData[$pagination]['mobile'],
                    $vendor_status,

                    '<div class="btn-group">
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
                </div>'
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

    public function changeVendorStatus(Request $request, $vendor)
    {
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

    public function checkVendorName(Request $request)
    {
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
