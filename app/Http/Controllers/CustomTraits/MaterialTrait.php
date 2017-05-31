<?php
namespace App\Http\Controllers\CustomTraits;
use App\Category;
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
            $categories = Category::where('is_active', true)->select('id','name')->orderBy('name','asc')->get()->toArray();
            $units = Unit::where('is_active', true)->select('id','name')->orderBy('name','asc')->get()->toArray();
            $materialData['id'] = $material->id;
            $materialData['name'] = $material->name;
            $materialData['category_id'] = $material->category_id;
            $materialVersion = MaterialVersion::where('material_id',$material->id)->orderBy('created_at','desc')->first();
            $materialData['rate_per_unit'] = $materialVersion->rate_per_unit;
            $materialData['unit'] = $materialVersion->unit_id;
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
            $materialData['name'] = ucwords($request->name);
            $materialData['category_id'] = $request->category_id;
            $materialData['is_active'] = (boolean)0;
            $materialData['created_at'] = $now;
            $materialData['updated_at'] = $now;
            $material = Material::create($materialData);
            $materialVersionData['material_id'] = $material->id;
            $materialVersionData['rate_per_unit'] = $request->rate_per_unit;
            $materialVersionData['unit_id'] = $request->unit;
            $materialVersion = MaterialVersion::create($materialVersionData);
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
            $materialData['name'] = ucwords($request->name);
            $materialData['category_id'] = $request->category_id;
            $materialData['is_active'] = (boolean)0;
            $materialData['updated_at'] = $now;
            $material->update($materialData);
            $materialVersionData['material_id'] = $material->id;
            $materialVersionData['rate_per_unit'] = $request->rate_per_unit;
            $materialVersionData['unit_id'] = (int)$request->unit;
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
            for($iterator = 0,$pagination = $request->start; $iterator < $request->length && $iterator < count($materialData); $iterator++,$pagination++ ){
                $materialVersion = MaterialVersion::where('material_id',$materialData[$pagination]['id'])->select('rate_per_unit','unit_id')->orderBy('created_at','desc')->first();

                if($materialData[$pagination]['is_active'] == true){
                    $material_status = '<td><span class="label label-sm label-success"> Enabled </span></td>';
                    $status = 'Disable';
                }else{
                    $material_status = '<td><span class="label label-sm label-danger"> Disabled</span></td>';
                    $status = 'Enable';
                }
                $records['data'][$iterator] = [
                    $materialData[$pagination]['name'],
                    $materialVersion->rate_per_unit,
                    Unit::where('id',$materialVersion->unit_id)->pluck('name')->first(),
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

}