<?php
namespace App\Http\Controllers\CustomTraits;
use App\Category;
use App\CategoryMaterialRelation;
use App\Http\Requests\MaterialRequest;
use App\Material;
use App\MaterialVersion;
use App\Unit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

trait MaterialTrait{

    public function getManageView(Request $request) {
       try{
           return view('admin.material.manage');
       }catch(\Exception $e){
           $data = [
               'action' => 'Get material manage view',
               'params' => $request->all(),
               'exception' => $e->getMessage()
           ];
           Log::critical(json_encode($data));
           abort(500);
       }
    }

    public function getCreateView(Request $request) {
        try{
            $categories = Category::where('is_active',true)->select('id','name')->orderBy('name','asc')->get()->toArray();
            $units = Unit::where('is_active',true)->select('id','name')->orderBy('name','asc')->get()->toArray();
            return view('admin.material.create')->with(compact('categories','units'));
        }catch(\Exception $e){
            $data = [
                'action' => 'Get create material view',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getEditView(Request $request, $material) {
        try{
            $unit = Unit::where('id',$material->unit_id)->select('id','name')->first();
            $materialData['id'] = $material->id;
            $materialData['name'] = $material->name;
            $materialData['categories'] =  CategoryMaterialRelation::join('categories','categories.id','=','category_material_relations.category_id')
                                            ->where('category_material_relations.material_id', $material->id)
                                            ->select('category_material_relations.category_id as id','categories.name as name')
                                            ->get()
                                            ->toArray();
            $categoryIds = CategoryMaterialRelation::join('categories','categories.id','=','category_material_relations.category_id')
                ->where('category_material_relations.material_id', $material->id)
                ->select('category_material_relations.category_id as id','categories.name as name')
                ->pluck('id')
                ->toArray();
            $materialData['category_id'] = implode(',',$categoryIds);
            $categories = Category::whereNotIn('id',$categoryIds)->where('is_active', true)->select('id','name')->orderBy('name','asc')->get()->toArray();
            $materialVersion = MaterialVersion::where('material_id',$material->id)->orderBy('created_at','desc')->first();
            $materialData['rate_per_unit'] = $materialVersion->rate_per_unit;
            $materialData['unit'] = $materialVersion->unit_id;
            return view('admin.material.edit')->with(compact('categories','unit','materialData'));
        }catch(\Exception $e){
            $data = [
                'action' => 'get Edit material view',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function createMaterial(MaterialRequest $request){
        try{
//            dd($request->all());
            $now = Carbon::now();
            if($request->has('material_id')){
                $categoryId = CategoryMaterialRelation::where('material_id',$request->material_id)->pluck('category_id')->first();
                if($categoryId != $request->category_id){
                    $categoryMaterialData['material_id'] = $request->material_id;
                    $categoryMaterialData['category_id'] = $request->category_id;
                    $categoryMaterial = CategoryMaterialRelation::create($categoryMaterialData);
                }
            }else{
                $materialData['name'] = ucwords(trim($request->name));
                $categoryMaterialData['category_id'] = $request->category_id;
                $materialData['rate_per_unit'] = round($request->rate_per_unit,3);
                $materialData['unit_id'] = $request->unit;
                $materialData['is_active'] = (boolean)0;
                $materialData['created_at'] = $now;
                $materialData['updated_at'] = $now;
//                dd($materialData);
                $material = Material::create($materialData);
                $categoryMaterialData['material_id'] = $material['id'];
                $categoryMaterial = CategoryMaterialRelation::create($categoryMaterialData);
                $materialVersionData['material_id'] = $material->id;
                $materialVersionData['rate_per_unit'] = round($request->rate_per_unit,3);
                $materialVersionData['unit_id'] = $request->unit;
                $materialVersion = MaterialVersion::create($materialVersionData);
            }
            $request->session()->flash('success','Material created successfully.');
            return redirect('/material/create');
        }catch(\Exception $e){
            $data = [
                'action' => 'create material',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }

    }

    public function editMaterial(MaterialRequest $request, $material){
        try{
            $now = Carbon::now();
            $materialData['name'] = ucwords(trim($request->name));
            $materialData['rate_per_unit'] = round($request->rate_per_unit,3);
            $materialData['updated_at'] = $now;
            $material->update($materialData);
            if($request->category_id != null){
                $categoryMaterial = CategoryMaterialRelation::create(['category_id' => $request->category_id,'material_id'=>$material->id]);
            }
            $materialVersionData['material_id'] = $material->id;
            $materialVersionData['rate_per_unit'] = round($request->rate_per_unit,3);
            $materialVersionData['unit_id'] = Unit::where('id',$material->unit_id)->pluck('id')->first();
            $recentMaterialVersion = MaterialVersion::where('material_id',$material->id)->select('material_id','rate_per_unit','unit_id')->orderBy('created_at','desc')->first()->toArray();
            if($recentMaterialVersion != $materialVersionData){
                $materialVersion = MaterialVersion::create($materialVersionData);
            }
            $request->session()->flash('success','Material Edited successfully.');
            return redirect('/material/edit/'.$material->id);
        }catch(\Exception $e){
            $data = [
                'action' => 'Edit material',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function materialListing(Request $request){
        try{
            $materialData = Material::orderBy('id','asc')->get()->toArray();
            $iTotalRecords = count($materialData);
            $records = array();
            for($iterator = 0,$pagination = $request->start; $iterator < $request->length && $pagination < count($materialData); $iterator++,$pagination++ ){
                if($materialData[$pagination]['is_active'] == true){
                    $material_status = '<td><span class="label label-sm label-success"> Enabled </span></td>';
                    $status = 'Disable';
                }else{
                    $material_status = '<td><span class="label label-sm label-danger"> Disabled</span></td>';
                    $status = 'Enable';
                }
                $records['data'][$iterator] = [
                    $materialData[$pagination]['name'],
                    round($materialData[$pagination]['rate_per_unit'],3),
                    Unit::where('id',$materialData[$pagination]['unit_id'])->pluck('name')->first(),
                    $material_status,
                    date('d M Y',strtotime($materialData[$pagination]['created_at'])),
                    '<div class="btn-group">
                        <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                            Actions
                            <i class="fa fa-angle-down"></i>
                        </button>
                        <ul class="dropdown-menu pull-left" role="menu">
                            <li>
                                <a href="/material/edit/'.$materialData[$pagination]['id'].'">
                                    <i class="icon-docs"></i> Edit </a>
                            </li>
                            <li>
                                <a href="/material/change-status/'.$materialData[$pagination]['id'].'">
                                    <i class="icon-tag"></i> '.$status.' </a>
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
                'action' => 'Material Listing',
                'params' => $request->all(),
                'exception'=> $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }

        return response()->json($records,200);
    }

    public function changeMaterialStatus(Request $request, $material){
        try{
            $newStatus = (boolean)!$material->is_active;
            $material->update(['is_active' => $newStatus]);
            $request->session()->flash('success', 'Material Status changed successfully.');
            return redirect('/material/manage');
        }catch(\Exception $e){
            $data = [
                'action' => 'Change Material status',
                'param' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function checkMaterialName(Request $request){
        try{
            $materialName = ucwords($request->name);
            if($request->has('material_id')){
                $nameCount = Material::where('name','=',$materialName)->where('id','!=',$request->material_id)->count();
            }else{
                $nameCount = Material::where('name','=',$materialName)->count();
            }
            if($nameCount > 0){
                return 'false';
            }else{
                return 'true';
            }
        }catch(\Exception $e){
            $data = [
                'action' => 'Check Material name',
                'param' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }

    }

    public function autoSuggest(Request $request,$keyword){
        try{
            $materials = Material::where('name','ilike','%'.$keyword.'%')->get();
            $response = array();
            if($materials != null){
                $iterator = 0;
                $materials = $materials->toArray();
                foreach($materials as $material){
                    $response[$iterator] = Unit::where('id',$material['unit_id'])->select('id as unit_id','name as unit')->first()->toArray();
                    $response[$iterator]['rate_per_unit'] = round($material['rate_per_unit'],3);
                    $response[$iterator]['id'] = $material['id'];
                    $response[$iterator]['name'] = $material['name'];
                    $iterator++;
                }
            }
            $status = 200;
        }catch(\Exception $e){
            $response = array();
            $data = [
                'action' => 'Material Auto-suggest',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            $status = 500;
        }
        return response()->json($response,$status);
    }

}