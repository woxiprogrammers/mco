<?php

namespace App\Http\Controllers\Admin;

use App\Asset;
use App\AssetImage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PhpParser\Node\Expr\Array_;


class AssetManagementController extends Controller
{
    public function __construct(){
        $this->middleware('custom.auth');
    }
    public function getManageView(Request $request){
        return view('admin.asset.manage');
    }
    public function getCreateView(Request $request){
        return view('admin.asset.create');
    }
    public function getEditView(Request $request,$asset){
        try{
            $asset = $asset->toArray();
            $assetId = $asset['id'];
            $assetImages = AssetImage::where('asset_id',$assetId)->select('id','name')->get();
            if($assetImages != null){
                $assetImage = $this->getImagePath($assetId,$assetImages);
            }
            return view('admin.asset.edit')->with(compact('asset','assetImage'));
        }catch (\Exception $e){
            $data = [
                'action' => "Get asset edit view",
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function createAsset(Request $request){
        try{
            $data = Array();
            $data['name'] = $request->name;
            $data['model_number'] = $request->model_number;
            $data['expiry_date'] = $request->expiry_date;
            $data['price'] = $request->price;
            $data['is_fuel_dependent'] = $request->is_fuel_dependent;
            $data['litre_per_unit'] = $request->litre_per_unit;
            $data['is_active'] = false;
            $asset = Asset::create($data);
            $assetId = $asset['id'];
            $work_order_images = $request->work_order_images;
            $assetDirectoryName = sha1($assetId);
            $UploadPath = public_path() . env('ASSET_IMAGE_UPLOAD');
            $ImageUploadPath = $UploadPath . DIRECTORY_SEPARATOR . $assetDirectoryName;
            if (!file_exists($ImageUploadPath)) {
                File::makeDirectory($ImageUploadPath, $mode = 0777, true, true);
            }
            foreach ($work_order_images as $images){
                    $imagePath = $images['image_name'];
                    $imageName = explode("/",$imagePath);
                    $filename = $imageName[4];
                    $data = Array();
                    $data['name'] = $filename;
                    $data['asset_id'] = $assetId;
                    AssetImage::create($data);
                    $oldFilePath = public_path().$imagePath;
                    $newFilePath = public_path().env('ASSET_IMAGE_UPLOAD').DIRECTORY_SEPARATOR.$assetDirectoryName.DIRECTORY_SEPARATOR.$filename;
                    File::move($oldFilePath, $newFilePath);
            }
            $request->session()->flash('success', 'Asset Created successfully.');
            return redirect('/asset/create');
        }catch (\Exception $e){
            $data = [
                'action' => 'Create Asset',
                'params' => $request->all(),
                'exception'=> $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function editAsset(Request $request,$asset){
        try{
            $data = $request->all();
            $assetData['name'] = ucwords(trim($data['name']));
            $assetData['model_number'] = $data['model_number'];
            $assetData['expiry_date'] = $data['expiry_date'];
            $assetData['price'] = $data['price'];
            $assetData['is_fuel_dependent'] = $data['is_fuel_dependent'];
            $assetData['litre_per_unit'] = $data['litre_per_unit'];
            $asset->update($assetData);

            $assetId = $asset->id;
            $work_order_images = $request->work_order_images;
            $assetDirectoryName = sha1($assetId);
            if($work_order_images != null) {
                foreach ($work_order_images as $images) {
                    $imagePath = $images['image_name'];
                    $imageName = explode("/", $imagePath);
                    $filename = $imageName[4];
                    $data = Array();
                    $data['name'] = $filename;
                    $data['asset_id'] = $assetId;
                    AssetImage::create($data);
                    $oldFilePath = public_path() . $imagePath;
                    $newFilePath = public_path() . env('ASSET_IMAGE_UPLOAD') . DIRECTORY_SEPARATOR . $assetDirectoryName . DIRECTORY_SEPARATOR . $filename;
                    File::move($oldFilePath, $newFilePath);
                }
            }

            $assetImages = $request->asset_images;
            if($work_order_images != null && $assetImages != null){
                $existingImages = array_column(array_merge($work_order_images,$assetImages),"image_name");
            }elseif($work_order_images != null){
               $existingImages = array_column($work_order_images,"image_name");
            }else{
                $existingImages = array_column($assetImages,"image_name");
            }
            $filename = Array();
            if($existingImages != null) {
                foreach ($existingImages as $images) {
                    $imagePath = $images;
                    $imageName = explode("/", $imagePath);
                    $filename[] = end($imageName);
                }
            }
            $deletedAssetImages = AssetImage::where('asset_id',$assetId)->whereNotIn('name',$filename)->get();
            foreach($deletedAssetImages as $images){
                $images->delete();
            }
            $request->session()->flash('success', 'Asset Edited successfully.');
            return redirect('/asset/edit/'.$asset->id);
        }catch (Exception $e){
            $data = [
                'action' => 'Edit Asset',
                'params' => $request->all(),
                'exception'=> $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }


    public function uploadTempAssetImages(Request $request){
        try {
            $user = Auth::user();
            $assetDirectoryName = sha1($user->id);
            $tempUploadPath = public_path() . env('ASSET_TEMP_IMAGE_UPLOAD');
            $tempImageUploadPath = $tempUploadPath . DIRECTORY_SEPARATOR . $assetDirectoryName;
            /* Create Upload Directory If Not Exists */
            if (!file_exists($tempImageUploadPath)) {
                File::makeDirectory($tempImageUploadPath, $mode = 0777, true, true);
            }
            $extension = $request->file('file')->getClientOriginalExtension();
            $filename = mt_rand(1,10000000000).sha1(time()).".{$extension}";
            $request->file('file')->move($tempImageUploadPath,$filename);
            $path = env('ASSET_TEMP_IMAGE_UPLOAD').DIRECTORY_SEPARATOR.$assetDirectoryName.DIRECTORY_SEPARATOR.$filename;
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

    public function assetListing(Request $request){
                    try{
                        if($request->has('search_name')){
                            $assetData = Asset::where('id','ilike','%'.$request->search_name.'%')->orderBy('name','asc')->get()->toArray();
                        }else{
                            $assetData = Asset::orderBy('id','a[sc')->get()->toArray();
                        }
                        $iTotalRecords = count($assetData);
                        $records = array();
                        $records['data'] = array();
                        $end = $request->length < 0 ? count($assetData) : $request->length;
                        for($iterator = 0,$pagination = $request->start; $iterator < $end && $pagination < count($assetData); $iterator++,$pagination++ ){
                            if($assetData[$pagination]['is_active'] == true){
                                $asset_status = '<td><span class="label label-sm label-success"> Enabled </span></td>';
                                $status = 'Disable';
                            }else{
                                $asset_status = '<td><span class="label label-sm label-danger"> Disabled</span></td>';
                                $status = 'Enable';
                            }
                            $records['data'][$iterator] = [
                                $assetData[$pagination]['id'],
                                $assetData[$pagination]['model_number'],
                                $asset_status,

                                '<div class="btn-group">
                       <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                           Actions
                           <i class="fa fa-angle-down"></i>
                       </button>
                       <ul class="dropdown-menu pull-left" role="menu">
                           <li>
                               <a href="/asset/edit/'.$assetData[$pagination]['id'].'">
                               <i class="icon-docs"></i> Edit </a>
                       </li>
                       <li>
                           <a href="/asset/change-status/'.$assetData[$pagination]['id'].'">
                               <i class="icon-tag"></i> '.$status.' </a>
                       </li>
                   </ul>
               </div>'
                            ];
                        }

                        $records["draw"] = intval($request->draw);
                        $records["recordsTotal"] = $iTotalRecords;
                        $records["recordsFiltered"] = $iTotalRecords;
                    }catch (Exception $e){
                        $records = array();
                        $data = [
                            'action' => 'Get Asset Listing',
                            'params' => $request->all(),
                            'exception'=> $e->getMessage()
                        ];
                        Log::critical(json_encode($data));
                        abort(500);
                    }
                    return response()->json($records);
    }

    public function displayAssetImages(Request $request){
        try{
            $path = $request->path;
            $count = $request->count;
            $random = mt_rand(1,10000000000);
        }catch (\Exception $e){
            $path = null;
            $count = null;
        }
        return view('partials.asset.image')->with(compact('path','count','random'));
    }

    public function removeAssetImage(Request $request)
    {
        try {
            $sellerUploadPath = public_path() . $request->path;
            File::delete($sellerUploadPath);
            return response(200);
        } catch (\Exception $e) {
            return response(500);
        }
    }
    public function changeAssetStatus(Request $request, $asset){
        try{
            $newStatus = (boolean)!$asset->is_active;
            $asset->update(['is_active' => $newStatus]);
            $request->session()->flash('success', 'Asset Status changed successfully.');
            return redirect('/asset/manage');
        }catch(\Exception $e){
            $data = [
                'action' => 'Change asset status',
                'param' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function checkAssetName(Request $request){
        try{
            $assetName = $request->name;
            if($request->has('id')){
                $nameCount = Asset::where('name','ilike',$assetName)->where('id','!=',$request->id)->count();
            }else{
                $nameCount = Asset::where('name','ilike',$assetName)->count();
            }
            if($nameCount > 0){
                return 'false';
            }else{
                return 'true';
            }
        }catch(\Exception $e){
            $data = [
                'action' => 'Check Asset Name',
                'param' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getImagePath($assetId,$images){
        $assetDirectoryName = sha1($assetId);
        $imageUploadPath = env('ASSET_IMAGE_UPLOAD').DIRECTORY_SEPARATOR.$assetDirectoryName;
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
}
