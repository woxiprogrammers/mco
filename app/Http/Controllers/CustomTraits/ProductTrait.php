<?php
namespace App\Http\Controllers\CustomTraits;
use App\Category;
use App\Material;
use App\MaterialVersion;
use App\ProfitMargin;
use App\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

trait ProductTrait{

    public function getManageView(Request $request) {
        try{
            return view('admin.product.manage');
        }catch(\Exception $e){
            $data = [
                'action' => 'Get Manage View',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getCreateView(Request $request) {
        try{
            $categories = Category::where('is_active', true)->select('id','name')->orderBy('name','asc')->get()->toArray();
            $profitMargins = ProfitMargin::where('is_active', true)->select('id','name','base_percentage')->orderBy('id','asc')->get()->toArray();
            $units = Unit::where('is_active', true)->select('id','name')->orderBy('name','asc')->get()->toArray();
            return view('admin.product.create')->with(compact('categories','profitMargins','units'));
        }catch(\Exception $e){
            $data = [
                'action' => 'Get Manage View',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getEditView(Request $request) {
        try{
            return view('admin.product.edit');
        }catch(\Exception $e){
            $data = [
                'action' => 'Get Manage View',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);

        }
    }

    public function getMaterials(Request $request,$category){
        try{
            $materials = Material::where('category_id',$category->id)->where('is_active', true)->select('id','name')->orderBy('name','asc')->get();
            $materialOptions = array();
            if($materials == null){
                $materialOptions[] = '<option value=""> No material Available </option>';
            }else{
                foreach($materials as $material){
                    $materialOptions[] = '<option value="'.$material->id.'"> '.$material->name.' </option>';
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

    public function getMaterialsDetails(Request $request){
        try{
            $materialIds = $request->material_ids;
            $materials = Material::whereIn('id',$materialIds)->select('id','name')->orderBy('name','asc')->get()->toArray();
            $totalRecords = count($materials);
            $records = array();
            $records['data'][] = '<tr>
                                    <th style="width: 25%"> Name </th>
                                    <th> Rate </th>
                                    <th> Unit </th>
                                    <th> Quantity </th>
                                    <th> Amount </th>
                                  </tr>';
            foreach($materials as $material){
                $materialVersion = MaterialVersion::where('material_id',$material['id'])->orderBy('created_at','desc')->first();
                $unit = Unit::where('id',$materialVersion->unit_id)->select('id','name')->first();
                $records['data'][] = "<tr>".
                    "<td>".$material['name']."</td>".
                    "<td>".$materialVersion->rate_per_unit.'<input type="hidden" name="material_rate['.$materialVersion->id.']" value="'.$materialVersion->rate_per_unit.'">'."</td>".
                    "<td>".$unit->name.'<input type="hidden" name="material_unit['.$materialVersion->id.']" value="'.$unit->id.'">'."</td>".
                    "<td>".'<input type="number" name="material_quantity['.$materialVersion->id.']" onkeyup="changedQuantity('.$materialVersion->id.')" required>'."</td>".
                    "<td>".'<input type="text" name="material_amount['.$materialVersion->id.']" required>'."</td>".
                "</tr>";
            }
            $status = 200;
        }catch(\Exception $e){
            $status = 500;
            $records = array();
            $data = [
                'action' => 'Get Materials',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
        return response()->json($records,$status);
    }

    public function createProduct(Request $request){
        try{
            $productData['name'] = ucwords($request->name);
            $productData['description'] = $request->description;
            $productData['category_id'] = $request->category_id;
            $productData['unit_id'] = $request->unit_id;
        }catch(\Exception $e){
            $data = [
                'action' => 'Create Product',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

}