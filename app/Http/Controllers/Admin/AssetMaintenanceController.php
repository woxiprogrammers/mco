<?php
    /**
     * Created by Harsha
     * Date: 27/1/18
     * Time: 12:25 PM
     */

namespace App\Http\Controllers\Admin;

use App\Asset;
use App\AssetMaintenance;
use App\AssetMaintenanceImage;
use App\AssetMaintenanceStatus;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AssetMaintenanceController extends Controller{

    public function __construct(){
        $this->middleware('custom.auth');
    }

    public function getCreateView(Request $request){
        try{
            return view('asset-maintenance.request.create');
        }catch(\Exception $e){
            $data = [
                'action' => 'Get Asset Maintenance Request Create View',
                'exception' => $e->getMessage(),
                'params' => $request->all()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function uploadTempAssetMaintenanceImages(Request $request){
        try {
            $user = Auth::user();
            $assetDirectoryName = sha1($user->id);
            $tempUploadPath = public_path() . env('ASSET_MAINTENANCE_REQUEST_TEMP_IMAGE_UPLOAD');
            $tempImageUploadPath = $tempUploadPath . DIRECTORY_SEPARATOR . $assetDirectoryName;
            /* Create Upload Directory If Not Exists */
            if (!file_exists($tempImageUploadPath)) {
                File::makeDirectory($tempImageUploadPath, $mode = 0777, true, true);
            }
            $extension = $request->file('file')->getClientOriginalExtension();
            $filename = mt_rand(1,10000000000).sha1(time()).".{$extension}";
            $request->file('file')->move($tempImageUploadPath,$filename);
            $path = env('ASSET_MAINTENANCE_REQUEST_TEMP_IMAGE_UPLOAD').DIRECTORY_SEPARATOR.$assetDirectoryName.DIRECTORY_SEPARATOR.$filename;
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
            Log::info($e->getMessage());
        }
        return response()->json($response);
    }

    public function displayAssetMaintenanceImages(Request $request){
        try{
            $path = $request->path;
            $count = $request->count;
            $random = mt_rand(1,10000000000);
        }catch (\Exception $e){
            $path = null;
            $count = null;
            Log::critical($e->getMessage());
        }
        return view('partials.asset-maintenance.image')->with(compact('path','count','random'));
    }

    public function removeAssetMaintenanceImage(Request $request){
        try {
            $sellerUploadPath = public_path() . $request->path;
            File::delete($sellerUploadPath);
            return response(200);
        } catch (\Exception $e) {
            return response(500);
        }
    }

    public function createAssetMaintenanceRequest(Request $request){
        try{
            $projectSiteId = Session::get('global_project_site');
            $user = Auth::user();
            $assetMaintenance = AssetMaintenance::create([
                'asset_id' => $request['asset_id'],
                'project_site_id' => $projectSiteId,
                'asset_maintenance_status_id' => AssetMaintenanceStatus::where('slug','maintenance-requested')->pluck('id')->first(),
                'user_id' => $user['id'],
                'remark' => $request['remark']
            ]);
            if($request->work_order_images != null) {
                $assetMaintenanceId = $assetMaintenance['id'];
                $work_order_images = $request->work_order_images;
                $assetDirectoryName = sha1($assetMaintenanceId);
                $UploadPath = public_path() . env('ASSET_MAINTENANCE_REQUEST_IMAGE_UPLOAD');
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
                    $data['asset_maintenance_id'] = $assetMaintenanceId;
                    AssetMaintenanceImage::create($data);
                    $oldFilePath = public_path() . $imagePath;
                    $newFilePath = public_path() . env('ASSET_MAINTENANCE_REQUEST_IMAGE_UPLOAD') . DIRECTORY_SEPARATOR . $assetDirectoryName . DIRECTORY_SEPARATOR . $filename;
                    File::move($oldFilePath, $newFilePath);
                }
            }
            $request->session()->flash('success','Maintenance Request Created successfully');
            return redirect('/asset/maintenance/request/create');
        }catch(\Exception $e){
            $data = [
                'action' => 'Create Asset Maintenance Request',
                'exception' => $e->getMessage(),
                'params' => $request->all()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function autoSuggest(Request $request,$keyword){
        try{
            $assetList = Asset::where('name','ilike','%'.$keyword.'%')->where('is_active',true)->select('id','name')->get();
        }catch(\Exception $e){
            $assetList = array();
            $data = [
                'action' => 'Asset Auto Suggest',
                'exception' => $e->getMessage(),
                'params' => $request->all()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
        return response($assetList,200);
    }

}