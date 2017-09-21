<?php

namespace App\Http\Controllers\Admin;

use App\AssetManagement;
use Illuminate\Support\Facades\File;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

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
        return view('admin.asset.edit');
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
                $asset = AssetManagement::create($data);
               $assetId = 1;
               $imageUpload = $this->uploadTempAssetImages($assetId);
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

    public function editAsset(Request $request){
        try{

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


    public function uploadTempAssetImages(Request $request,$assetId){
        try{
            $assetDirectoryName = sha1($assetId);
            $tempUploadPath = public_path().env('ASSET_TEMP_IMAGE_UPLOAD');
            $tempImageUploadPath = $tempUploadPath.DIRECTORY_SEPARATOR.$assetDirectoryName;
            Log::info($tempImageUploadPath);
            /* Create Upload Directory If Not Exists */
            Log::info('result of if');
            Log::info(!file_exists($tempImageUploadPath));
            if (!file_exists($tempImageUploadPath)) {
                Log::info('in if');
                File::makeDirectory($tempImageUploadPath, $mode = 0777, true, true);
            }
            $extension = $request->file('file')->getClientOriginalExtension();
            $filename = mt_rand(1,10000000000).sha1(time()).'.'.$extension.'';
            $request->file('file')->move($tempImageUploadPath,$filename);
            $path = env('ASSET_TEMP_IMAGE_UPLOAD').DIRECTORY_SEPARATOR.$assetDirectoryName.DIRECTORY_SEPARATOR.$filename;
            $response = [
                'jsonrpc' => '2.0',
                'result' => 'OK',
                'path' => $path
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

    public function displayAssetImages(Request $request){
        try{
            $path = $request->path;
            $count = $request->count;
            $random = mt_rand(1,10000000000);
        }catch (\Exception $e){
            $path = null;
            $count = null;
        }
        return view('admin.asset.create')->with(compact('path','count','random'));
    }

    public function removeAssetImage(Request $request){
        try{
            $sellerUploadPath = public_path().$request->path;
            File::delete($sellerUploadPath);
            return response(200);
        }catch(\Exception $e){
            return response(500);
        }
    }

}
