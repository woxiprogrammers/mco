<?php
namespace App\Http\Controllers\CustomTraits;
use App\Category;
use App\CategoryMaterialRelation;
use App\Helper\MaterialProductHelper;
use App\Http\Requests\MaterialRequest;
use App\Material;
use App\MaterialVersion;
use App\Unit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Barryvdh\DomPDF\Facade as PDF;

trait MaterialTrait{

    public function getManageView(Request $request) {
       try{
           $categories = null;
           $categories = Category::where('is_active',true)->select('id','name')->orderBy('name','asc')->get()->toArray();
           $data['categories'] = $categories;
           return view('admin.material.manage', $data);
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
            $units = Unit::where('is_active', true)->orderBy('name','asc')->get()->toArray();
            $materialData['id'] = $material->id;
            $materialData['name'] = $material->name;
            $materialData['rate_per_unit'] = $material->rate_per_unit;
            $materialData['unit'] = $material->unit_id;
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
            return view('admin.material.edit')->with(compact('categories','units','materialData'));
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
            $materialData['updated_at'] = $now;
            $material->update($materialData);
            if($request->category_id != null){
                $categoryMaterial = CategoryMaterialRelation::create(['category_id' => $request->category_id,'material_id'=>$material->id]);
            }
            $updateMaterial = array();
            $updateMaterial[0]['id'] = $material->id;
            $updateMaterial[0]['rate_per_unit'] = $request->rate_per_unit;
            $updateMaterial[0]['unit_id'] = $request->unit;
            $response = MaterialProductHelper::updateMaterialsProductsAndProfitMargins($updateMaterial);
            if($response['slug'] == 'error'){
                $request->session()->flash('error',$response['message']);
            }else{
                $request->session()->flash('success','Material Edited successfully.');
            }
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
            if($request->has('search_name')){
                $materialData = Material::where('name','ilike','%'.$request->search_name.'%')->orderBy('name','asc')->get()->toArray();
            }else{
                $materialData = Material::orderBy('name','asc')->get()->toArray();
            }
            $iTotalRecords = count($materialData);
            $records = array();
            $records['data'] = array();
            $end = $request->length < 0 ? count($materialData) : $request->length;
            for($iterator = 0,$pagination = $request->start; $iterator < $end && $pagination < count($materialData); $iterator++,$pagination++ ){
                if($materialData[$pagination]['is_active'] == true){
                    $material_status = '<td><span class="label label-sm label-success"> Enabled </span></td>';
                    $status = 'Disable';
                }else{
                    $material_status = '<td><span class="label label-sm label-danger"> Disabled</span></td>';
                    $status = 'Enable';
                }
                if(Auth::user()->hasPermissionTo('edit-material')){
                    $records['data'][$iterator] = [
                        '<input type="checkbox" name="material_ids" value="'.$materialData[$pagination]['id'].'">',
                        $materialData[$pagination]['name'],
                        Unit::where('id',$materialData[$pagination]['unit_id'])->pluck('name')->first(),
                        round($materialData[$pagination]['rate_per_unit'],3),
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
                        </ul>
                    </div>'
                    ];
                }else{
                    $records['data'][$iterator] = [
                        '<input type="checkbox" name="material_ids" value="'.$materialData[$pagination]['id'].'">',
                        $materialData[$pagination]['name'],
                        Unit::where('id',$materialData[$pagination]['unit_id'])->pluck('name')->first(),
                        round($materialData[$pagination]['rate_per_unit'],3),
                        $material_status,
                        date('d M Y',strtotime($materialData[$pagination]['created_at'])),
                        '<div class="btn-group">
                            <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                Actions
                                <i class="fa fa-angle-down"></i>
                            </button>
                        </div>'
                    ];
                }

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

    public function changeMaterialStatus(Request $request){
        try{
            foreach($request->material_ids as $materialId){
                $material = Material::findOrFail($materialId);
                $newStatus = (boolean)!$material->is_active;
                $material->update(['is_active' => $newStatus]);
            }
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
            $materialName = $request->name;
            if($request->has('material_id')){
                $nameCount = Material::where('name','ilike',$materialName)->where('id','!=',$request->material_id)->count();
            }else{
                $nameCount = Material::where('name','ilike',$materialName)->count();
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

    public function generateBasicRateMaterialPdf(Request $request){
        try{
            $materialData = null;
            if ($request->has('material_category_ids')) {
                if  (in_array('all', $request->material_category_ids)) {
                    $materialData = DB::table('materials')
                        ->join('units','materials.unit_id','=','units.id')
                        ->join('category_material_relations','materials.id','=', 'category_material_relations.material_id')
                        ->join('categories','category_material_relations.category_id','=','categories.id')
                        ->where('materials.is_active',true)
                        ->orderBy('categories.id')
                        ->select('categories.name as category_name','materials.name as material_name','units.name as unit_name','materials.rate_per_unit as rate')
                        ->get()->toArray();
                }  else {
                    $materialData = DB::table('materials')
                        ->join('units','materials.unit_id','=','units.id')
                        ->join('category_material_relations','materials.id','=', 'category_material_relations.material_id')
                        ->join('categories','category_material_relations.category_id','=','categories.id')
                        ->whereIn('categories.id', $request->material_category_ids)
                        ->where('materials.is_active',true)
                        ->orderBy('categories.id')
                        ->select('categories.name as category_name','materials.name as material_name','units.name as unit_name','materials.rate_per_unit as rate')
                        ->get()->toArray();
                }
            }
            $data = array();
            $materialDataFinal = array();
            foreach ($materialData as $material) {
                if (!in_array($material->category_name, $materialDataFinal)) {
                    $materialDataFinal[$material->category_name][] = array (
                        'material_name' => $material->material_name,
                        'unit_name' =>  $material->unit_name,
                        'rate' =>  $material->rate,
                    );
                } else {
                    $materialDataFinal[$material->category_name][] = array (
                        'material_name' => $material->material_name,
                        'unit_name' =>  $material->unit_name,
                        'rate' =>  $material->rate,
                    );
                }
            }
            $data['materialData'] = $materialDataFinal;
            $pdf = App::make('dompdf.wrapper');
            $pdf->loadHTML(view('admin.material.pdf.materialbasicrates',$data));
            return $pdf->stream();
        } catch(\Exception $e) {
            $data = [
                'action' => 'Generate Basic Rate PDF',
                'param' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
        }
    }
}