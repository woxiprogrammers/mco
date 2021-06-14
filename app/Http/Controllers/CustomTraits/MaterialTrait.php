<?php
namespace App\Http\Controllers\CustomTraits;

use App\AssetImage;
use App\Category;
use App\MaterialImages;
use Illuminate\Support\Facades\File;
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

    public function displayMaterialImages(Request $request){
        try{
            $path = $request->path;
            $count = $request->count;
            $random = mt_rand(1,10000000000);
        }catch (\Exception $e){
            $path = null;
            $count = null;
        }
        return view('partials.material.image')->with(compact('path','count','random'));
    }

    public function removeMaterialImage(Request $request)
    {
        try {
/*            $splitPath = explode("/",$request->path);
            $imgname = $splitPath[count($splitPath)-1];
            DB::table('material_images')->where('name', $imgname)->delete();*/
            $sellerUploadPath = public_path() . $request->path;
            File::delete($sellerUploadPath);
            return response(200);
        } catch (\Exception $e) {
            return response(500);
        }
    }

    public function uploadTempMaterialImages(Request $request){
        try {
            $user = Auth::user();
            $assetDirectoryName = sha1($user->id);
            $tempUploadPath = public_path() . env('MATERIAL_TEMP_IMAGE_UPLOAD');
            $tempImageUploadPath = $tempUploadPath . DIRECTORY_SEPARATOR . $assetDirectoryName;
            /* Create Upload Directory If Not Exists */
            if (!file_exists($tempImageUploadPath)) {
                File::makeDirectory($tempImageUploadPath, $mode = 0777, true, true);
            }
            $extension = $request->file('file')->getClientOriginalExtension();
            $filename = mt_rand(1,10000000000).sha1(time()).".{$extension}";
            $request->file('file')->move($tempImageUploadPath,$filename);
            $path = env('MATERIAL_TEMP_IMAGE_UPLOAD').DIRECTORY_SEPARATOR.$assetDirectoryName.DIRECTORY_SEPARATOR.$filename;
            $response = [
                'jsonrpc' => '2.0',
                'result' => 'OK',
                'path' => $path,
            ];
        }catch (\Exception $e){
            $response = [
                'jsonrpc' => '2.0',
                'error' => [
                    'code' => 101,
                    'message' => 'Failed to open input stream.',
                ],
                'id' => 'id'
            ];
        }
        return response()->json($response);
    }

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
            $materialData['gst'] = $material->gst;
            $materialData['hsn_code'] = $material->hsn_code;
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
            $materialImages = MaterialImages::where('material_id',$material->id)->select('id','name')->get();
            if($materialImages != null){
                $materialImage = $this->getImagePath($material->id,$materialImages);
            }

           $categories = Category::whereNotIn('id',$categoryIds)->where('is_active', true)->select('id','name')->orderBy('name','asc')->get()->toArray();
            return view('admin.material.edit')->with(compact('materialImage','categories','units','materialData'));
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

    public function getImagePath($assetId,$images){
        $assetDirectoryName = sha1($assetId);
        $imageUploadPath = env('MATERIAL_IMAGE_UPLOAD').DIRECTORY_SEPARATOR.$assetDirectoryName;
        $iterator = 0;
        $imagePaths = array();
        foreach($images as $image){
            $imagePaths[$iterator] = array();
            $imagePaths[$iterator]['path'] = $imageUploadPath.DIRECTORY_SEPARATOR.$image['name'];
            $imagePaths[$iterator]['id'] = $image['id'];
            $iterator++;
        }
        return $imagePaths;
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
                $materialData['gst'] = $request->gst;
                $materialData['hsn_code'] = $request->hsn_code;
                $material = Material::create($materialData);
                $categoryMaterialData['material_id'] = $material['id'];
                $categoryMaterial = CategoryMaterialRelation::create($categoryMaterialData);
                $materialVersionData['material_id'] = $material->id;
                $materialVersionData['rate_per_unit'] = round($request->rate_per_unit,3);
                $materialVersionData['unit_id'] = $request->unit;
                $materialVersion = MaterialVersion::create($materialVersionData);
                if($request->work_order_images != null) {
                    $matId = $material['id'];
                    $work_order_images = $request->work_order_images;
                    $assetDirectoryName = sha1($matId);
                    $UploadPath = public_path() . env('MATERIAL_IMAGE_UPLOAD');
                    $ImageUploadPath = $UploadPath . DIRECTORY_SEPARATOR . $assetDirectoryName;
                    if (!file_exists($ImageUploadPath)) {
                        File::makeDirectory($ImageUploadPath, $mode = 0777, true, true);
                    }
                    foreach ($work_order_images as $images) {
                        $imagePath = $images['image_name'];
                        $imageName = explode("/", $imagePath);
                        $filename = $imageName[4];
                        $data = Array();
                        $data['name'] = $filename;
                        $data['material_id'] = $matId;
                        MaterialImages::create($data);
                        $oldFilePath = public_path() . $imagePath;
                        $newFilePath = public_path() . env('MATERIAL_IMAGE_UPLOAD') . DIRECTORY_SEPARATOR . $assetDirectoryName . DIRECTORY_SEPARATOR . $filename;
                        File::move($oldFilePath, $newFilePath);
                    }
                }
            }

           $request->session()->flash('success','Material created successfully.');
            return redirect('/material/manage');
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
            $materialData['gst'] = $request->gst;
            $materialData['hsn_code'] = $request->hsn_code;
            $material->update($materialData);
            if($request->category_id != null){
                $categoryMaterial = CategoryMaterialRelation::create(['category_id' => $request->category_id,'material_id'=>$material->id]);
            }
            $updateMaterial = array();
            $updateMaterial[0]['id'] = $material->id;
            $updateMaterial[0]['rate_per_unit'] = $request->rate_per_unit;
            $updateMaterial[0]['unit_id'] = $request->unit;
            $response = MaterialProductHelper::updateMaterialsProductsAndProfitMargins($updateMaterial);
            $assetImages = $request->material_images;
            if($request->work_order_images != null) {
                $assetDirectoryName = sha1($material->id);
                $UploadPath = public_path() . env('MATERIAL_IMAGE_UPLOAD');
                $ImageUploadPath = $UploadPath . DIRECTORY_SEPARATOR . $assetDirectoryName;
                if (!file_exists($ImageUploadPath)) {
                    File::makeDirectory($ImageUploadPath, $mode = 0777, true, true);
                }
                foreach ($request->work_order_images as $images) {
                    $imagePath = $images['image_name'];
                    $imageName = explode("/", $imagePath);
                    $filename = $imageName[4];
                    $data = Array();
                    $data['name'] = $filename;
                    $data['material_id'] = $material->id;
                    MaterialImages::create($data);
                    $oldFilePath = public_path() . $imagePath;
                    $newFilePath = public_path() . env('MATERIAL_IMAGE_UPLOAD') . DIRECTORY_SEPARATOR . $assetDirectoryName . DIRECTORY_SEPARATOR . $filename;
                    File::move($oldFilePath, $newFilePath);
                }

            }
            if ($request->work_order_images != null && $assetImages != null) {
                $existingImages = array_column(array_merge($request->work_order_images, $assetImages), "image_name");
            } elseif ($request->work_order_images != null) {
                $existingImages = array_column($request->work_order_images, "image_name");
            } elseif ($assetImages != null) {
                $existingImages = array_column($assetImages, "image_name");
            } else {
                $existingImages = null;
            }
            $filename = Array();
            if ($existingImages != null) {
                foreach ($existingImages as $images) {
                    $imagePath = $images;
                    $imageName = explode("/", $imagePath);
                    $filename[] = end($imageName);
                }
            } else {
                $filename[] = emptyArray();
            }
            $deletedAssetImages = MaterialImages::where('material_id', $material->id)->whereNotIn('name', $filename)->get();
            foreach ($deletedAssetImages as $images) {
                $images->delete();
            }
            if($response['slug'] == 'error'){
                $request->session()->flash('error',$response['message']);
            }else{
                $request->session()->flash('success','Material Edited successfully.');
            }
            return redirect('/material/manage');
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
            $skip = $request->start;
            $take = $request->length;
            $totalRecordCount = 0;
            $user = Auth::user();
            $materialData = array();
            $ids = array();
            $filterFlag = true;
            $initList = false;
            if(!($request->has('action'))) {
                $initList = true;
            }

            if($request->action[0] == 'filter_cancel') {
                $initList = true;
            }

            $isSearchName = false;
            if($request->has('search_name') && $request->search_name != '' && $filterFlag == true){
                $ids = Material::where('name','ilike','%'.$request->search_name.'%')
                                ->pluck('id')->toArray();
                if(count($ids) <= 0){
                    $filterFlag = false;
                } else {
                    $isSearchName = true;
                }
            }

            $isSearchRate = false;
            if($request->has('search_rate') && $request->search_rate != '' && $filterFlag == true){
                if($isSearchName) {
                    $ids = Material::whereIn('id',$ids)
                    ->where('rate_per_unit',$request->search_rate)
                    ->pluck('id')->toArray();
                } else {
                    $ids = Material::where('rate_per_unit',$request->search_rate)
                    ->pluck('id')->toArray();
                }
                
                if(count($ids) <= 0){
                    $filterFlag = false;
                } else {
                    $isSearchRate = true;
                }
            }

            if($request->has('search_name_cat') && $request->search_name_cat != '' && $filterFlag == true){
                if($isSearchRate) {
                    $ids = Material::join('category_material_relations','category_material_relations.material_id','=','materials.id')
                        ->join('categories','categories.id','=','category_material_relations.category_id')
                        ->where('categories.name','ilike','%'.$request->search_name_cat.'%')
                        ->whereIn('materials.id',$ids)
                        ->pluck('materials.id')->toArray();
                } else {
                    $ids = Material::join('category_material_relations','category_material_relations.material_id','=','materials.id')
                    ->join('categories','categories.id','=','category_material_relations.category_id')
                    ->where('categories.name','ilike','%'.$request->search_name_cat.'%')
                    ->pluck('materials.id')->toArray();
                }
                if(count($ids) <= 0){
                    $filterFlag = false;
                }
            }
            if($filterFlag == true && $initList == false) {
                $materialData = Material::whereIn('id',$ids)
                                ->orderBy('name','asc')
                                ->skip($skip)->take($take)
                                ->get()->toArray();

                $totalRecordCount = Material::whereIn('id',$ids)
                                ->count();
            }
            if ($initList) {
                $materialData = Material::orderBy('name','asc')
                                ->skip($skip)->take($take)
                                ->get()->toArray();

                $totalRecordCount = Material::count();
            }
            $iTotalRecords = count($materialData);
            $records = array();
            $records['data'] = array();
            $profilePicAddress = null;
            $end = $request->length < 0 ? count($materialData) : $request->length;
            for($iterator = 0,$pagination = 0; $iterator < $end && $pagination < count($materialData); $iterator++,$pagination++ ){
                $materialImagetag = null;
                $materialImages = MaterialImages::where('material_id',$materialData[$pagination]['id'])->select('id','name')->get();
                if($materialImages != null){
                    $profilePicAddress = $this->getImagePath($materialData[$pagination]['id'],$materialImages);
                    if($profilePicAddress != null) {
                        $profilePicAddress = env('APP_URL').$profilePicAddress[0]['path'];
                        $materialImagetag = '<a href="'.$profilePicAddress.'" target="_blank"><img src="'.$profilePicAddress.'" height="60" width="60" style="border-radius: 50%;box-shadow: 2px 2px 1px 1px #888888;"></a>';
                    } else {
                        $profilePicAddress = env('APP_URL').'/assets/layouts/layout3/img/no-image.png';
                        $materialImagetag = '<img src="'.$profilePicAddress.'" height="60" width="60" style="border-radius: 50%;box-shadow: 2px 2px 1px 1px #888888;">';
                    }
                } else {
                    $profilePicAddress = env('APP_URL').'/assets/layouts/layout3/img/no-image.png';
                    $materialImagetag = '<img src="'.$profilePicAddress.'" height="60" width="60" style="border-radius: 50%;box-shadow: 2px 2px 1px 1px #888888;">';
                }
                if($materialData[$pagination]['is_active'] == true){
                    $material_status = '<td><span class="label label-sm label-success"> Enabled </span></td>';
                    $status = 'Disable';
                }else{
                    $material_status = '<td><span class="label label-sm label-danger"> Disabled</span></td>';
                    $status = 'Enable';
                }
                if($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin' || $user->customHasPermission('edit-material')){
                    $categoryname = CategoryMaterialRelation::join('categories','categories.id','=','category_material_relations.category_id')
                                    ->where('category_material_relations.material_id','=',$materialData[$pagination]['id'])
                                    ->get(['categories.name'])->toArray();
                    $catNameArr = array();
                    if(count($categoryname) > 1) {
                        foreach ($categoryname as $catname) {
                            $catNameArr[] = $catname['name'];
                        }
                        $catNameStr = implode(" , ", $catNameArr);
                    } else {
                        $catNameStr = $categoryname[0]['name'];
                    }
                    $records['data'][$iterator] = [
                        '<input type="checkbox" name="material_ids" value="'.$materialData[$pagination]['id'].'">',
                        $materialImagetag,
                        ucwords($catNameStr),
                        ucwords($materialData[$pagination]['name']),
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
                        </ul>
                    </div>'
                    ];
                }else{
                    $records['data'][$iterator] = [
                        '<input type="checkbox" name="material_ids" value="'.$materialData[$pagination]['id'].'">',
                        $materialImagetag,
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
            $records["recordsTotal"] = $totalRecordCount;
            $records["recordsFiltered"] = $totalRecordCount;
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
            $message = 'Material Status changed successfully.';
            $status = 200;
        }catch(\Exception $e){
            $message = 'Something went wrong';
            $status = 500;
            $data = [
                'action' => 'Change Material status',
                'param' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
        }
        $response = [
            'message' => $message,
        ];
        return response($response,$status);
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
            $materials = Material::where('is_active', true)->where('name','ilike','%'.$keyword.'%')->get();
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