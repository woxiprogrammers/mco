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
use App\AssetMaintenanceTransaction;
use App\AssetMaintenanceTransactionImages;
use App\AssetMaintenanceTransactionStatuses;
use App\AssetMaintenanceVendorRelation;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CustomTraits\Inventory\InventoryTrait;
use App\Vendor;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AssetMaintenanceController extends Controller{
    use InventoryTrait;
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

    public function getManageView(Request $request){
        try{
            return view('asset-maintenance.request.manage');
        }catch(\Exception $e){
            $data = [
                'action' => 'Get maintenance request manage view',
                'exception' => $e->getMessage(),
                'params' => $request->all()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getMaintenanceRequestListing(Request $request){
        try{
            $projectSiteId = Session::get('global_project_site');
            $listingData = AssetMaintenance::where('project_site_id',$projectSiteId)->get();
            $status = 200;
            $iTotalRecords = count($listingData);
            $records = array();
            $records['data'] = array();
            $end = $request->length < 0 ? count($listingData) : $request->length;
            for($iterator = 0,$pagination = $request->start; $iterator < $end && $pagination < count($listingData); $iterator++,$pagination++ ){
                $records['data'][$iterator] = [
                    $listingData[$pagination]->asset->name,
                    date('d M Y',strtotime($listingData[$pagination]['created_at'])),
                    '<div class="btn btn-small blue">
                                            <a href="/asset/maintenance/request/view/'.$listingData[$pagination]['id'].'" style="color: white"> 
                                                View
                                            </a>
                                        </div>',
                ];
            }
            $records["draw"] = intval($request->draw);
            $records["recordsTotal"] = $iTotalRecords;
            $records["recordsFiltered"] = $iTotalRecords;
        }catch(\Exception $e){
            $records = array();
            $status = 500;
            $data = [
                'action' => 'Get maintenance request listing',
                'exception' => $e->getMessage(),
                'params' => $request->all()
            ];
        }
        return response()->json($records,$status);
    }

    public function getDetailView(Request $request,$assetMaintenanceId){
        try{
            $assetMaintenance = AssetMaintenance::where('id',$assetMaintenanceId)->first();
            $vendorApproved = $assetMaintenance->assetMaintenanceVendorRelation->where('is_approved',true)->first();
            if(count($assetMaintenance->assetMaintenanceImage) > 0){
                $assetDirectoryName = sha1($assetMaintenanceId);
                $imageData = array();
                $iterator = 0;
                foreach($assetMaintenance->assetMaintenanceImage as $key => $assetMaintenanceImageData){
                    $imageData[$iterator]['id'] = $assetMaintenanceImageData['id'];
                    $imageData[$iterator]['upload_path'] = url('/').env('ASSET_MAINTENANCE_REQUEST_IMAGE_UPLOAD') . DIRECTORY_SEPARATOR . $assetDirectoryName . DIRECTORY_SEPARATOR . $assetMaintenanceImageData['name'];
                    $iterator++;
                }
            }
            return view('asset-maintenance.request.view')->with(compact('assetMaintenance','imageData','vendorApproved'));
        }catch(\Exception $e){
            $data = [
                'action' => 'Get Asset Maintenance Request View',
                'exception' => $e->getMessage(),
                'asset_maintenance_id' => $assetMaintenanceId
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getApprovalManageView(Request $request){
        try{
            return view('asset-maintenance.request.approval.manage');
        }catch(\Exception $e){
            $data = [
                'action' => 'Get Request Maintenance Approval Manage view',
                'exception' => $e->getMessage(),
                'params' => $request->all()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getMaintenanceRequestApprovalListing(Request $request){
        try{
            $projectSiteId = Session::get('global_project_site');
            $vendorAssignedAssetMaintenance = AssetMaintenanceVendorRelation::join('asset_maintenance','asset_maintenance_vendor_relation.asset_maintenance_id','=','asset_maintenance.id')
                                                                            ->where('asset_maintenance.project_site_id',$projectSiteId)
                                                                            ->where('asset_maintenance.asset_maintenance_status_id',AssetMaintenanceStatus::where('slug','vendor-assigned')->pluck('id')->first())
                                                                            ->select('asset_maintenance_vendor_relation.id','asset_maintenance_vendor_relation.asset_maintenance_id','asset_maintenance_vendor_relation.vendor_id','asset_maintenance_vendor_relation.quotation_amount','asset_maintenance_vendor_relation.user_id')
                                                                            ->get();
            $status = 200;
            $iTotalRecords = count($vendorAssignedAssetMaintenance);
            $records = array();
            $records['data'] = array();
            $end = $request->length < 0 ? count($vendorAssignedAssetMaintenance) : $request->length;

            for($iterator = 0,$pagination = $request->start; $iterator < $end && $pagination < count($vendorAssignedAssetMaintenance); $iterator++,$pagination++ ){
                $actionDropDown =  '<button class="btn btn-xs blue"> 
                                            <form action="/asset/maintenance/request/approval/change-status/approve/'.$vendorAssignedAssetMaintenance[$pagination]->id.'" method="post">
                                                <a href="javascript:void(0);" onclick="changeStatus(this)" style="color: white">
                                                     Approve 
                                                </a>
                                                <input type="hidden" name="_token">
                                            </form> 
                                        </button>
                                        <button class="btn btn-xs default "> 
                                            <form action="/asset/maintenance/request/approval/change-status/disapprove/'.$vendorAssignedAssetMaintenance[$pagination]->id.'" method="post">
                                                <a href="javascript:void(0);" onclick="changeStatus(this)" style="color: grey">
                                                    Disapprove 
                                                </a>
                                                <input type="hidden" name="_token">
                                            </form>
                                        </button>';
                $records['data'][$iterator] = [
                    $vendorAssignedAssetMaintenance[$pagination]->assetMaintenance->asset->name,
                    date('d M Y',strtotime($vendorAssignedAssetMaintenance[$pagination]->assetMaintenance['created_at'])),
                    $vendorAssignedAssetMaintenance[$pagination]->vendor->name,
                    $vendorAssignedAssetMaintenance[$pagination]->quotation_amount,
                    $actionDropDown
                ];
            }
            $records["draw"] = intval($request->draw);
            $records["recordsTotal"] = $iTotalRecords;
            $records["recordsFiltered"] = $iTotalRecords;
        }catch(\Exception $e){
            $records = array();
            $status = 500;
            $data =[
                'action' => 'Get Request Maintenance Approval Manage view',
                'exception' => $e->getMessage(),
                'params' => $request->all()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
        return response()->json($records,$status);
    }

    public function changeMaintenanceRequestStatus(Request $request,$status,$assetMaintenanceVendorID){
        try{
            $assetMaintenanceVendor = AssetMaintenanceVendorRelation::where('id',$assetMaintenanceVendorID)->first();
            if($status == 'approve'){
                $assetMaintenanceVendor->update(['is_approved' => true]);
                if($assetMaintenanceVendor->assetMaintenance->assetMaintenanceStatus->slug != 'vendor-approved'){
                    $vendorAssignedStatusId = AssetMaintenanceStatus::where('slug','vendor-approved')->pluck('id')->first();
                    $assetMaintenanceVendor->assetMaintenance->update([
                        'asset_maintenance_status_id' => $vendorAssignedStatusId
                    ]);
                }
            }else{
                $assetMaintenanceVendor->update(['is_approved' => false]);
            }
            return redirect('/asset/maintenance/request/approval/manage');
        }catch(\Exception $e){
            $data =[
                'action' => 'Change Request Maintenance Vendor Status',
                'exception' => $e->getMessage(),
                'params' => $request->all()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getAssetVendorAutoSuggest(Request $request,$keyword,$assetMaintenanceId){
        try{
            $assetMaintenance = AssetMaintenance::where('id',$assetMaintenanceId)->first();
            $alreadyAssignedVendorId = $assetMaintenance->assetMaintenanceVendorRelation->pluck('vendor_id');
            $vendorList = Vendor::join('asset_vendor_relation','asset_vendor_relation.vendor_id','=','vendors.id')
                            ->where('asset_vendor_relation.asset_id',$assetMaintenance['asset_id'])
                            ->whereNotIn('vendors.id',$alreadyAssignedVendorId)
                            ->where('vendors.name','ilike','%'.$keyword.'%')->where('vendors.is_active',true)->select('vendors.id','vendors.name')->get();
            $response = array();
            if(count($vendorList) > 0){
                $response = $vendorList->toArray();
                $iterator = 0;
                foreach($response as $vendorList){
                    $response[$iterator]['vendor_id'] = $vendorList['id'];
                    $response[$iterator]['tr_view'] = '<input name="vendors[]" type="hidden" value="'.$vendorList['id'].'">
                                                        <div class="row">
                                                            <div class="col-md-9"  style="text-align: left">
                                                                <label class="control-label">'.$vendorList['name'].'</label>
                                                            </div>
                                                        </div>';
                    $iterator++;
                }
            }
        }catch(\Exception $e){
            $response = array();
            $data = [
                'action' => 'Auto-Suggest Vendor',
                'param' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
        return response($response,200);
    }

    public function assetMaintenanceVendorAssign(Request $request,$assetMaintenanceId){
        try{
            $user = Auth::user();
            if($request->has('vendor_data')){
                foreach($request['vendor_data'] as $vendorId => $quotationAmount){
                    AssetMaintenanceVendorRelation::create([
                        'asset_maintenance_id' => $assetMaintenanceId,
                        'vendor_id' => $vendorId,
                        'quotation_amount' => $quotationAmount,
                        'user_id' => $user['id']
                    ]);
                }
                AssetMaintenance::where('id',$assetMaintenanceId)->update(['asset_maintenance_status_id' => AssetMaintenanceStatus::where('slug','vendor-assigned')->pluck('id')->first()]);
            }

            $request->session()->flash('success','Vendors assigned successfully');
            return redirect('/asset/maintenance/request/view/'.$assetMaintenanceId);
        }catch(\Exception $e){
            $response = array();
            $data = [
                'action' => 'Auto-Suggest Vendor',
                'param' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function preGrnImageUpload(Request $request){
        try{
            $generatedGrn = $this->generateGRN();
            $assetMaintenance = AssetMaintenance::findOrFail($request->assetMaintenanceId);
            $grnGeneratedStatusId = AssetMaintenanceTransactionStatuses::where('slug','grn-generated')->pluck('id')->first();
            $assetMaintenanceTransactionData = [
                'asset_maintenance_id' => $assetMaintenance->id,
                'asset_maintenance_transaction_status_id' => $grnGeneratedStatusId,
                'grn' => $generatedGrn
            ];
            $assetMaintenanceTransaction = AssetMaintenanceTransaction::create($assetMaintenanceTransactionData);
            $assetMaintenanceDirectoryName = sha1($assetMaintenance->id);
            $assetMaintenanceTransactionDirectoryName = sha1($assetMaintenanceTransaction->id);
            $imageUploadPath = public_path().env('ASSET_MAINTENANCE_REQUEST_IMAGE_UPLOAD').DIRECTORY_SEPARATOR.$assetMaintenanceDirectoryName.DIRECTORY_SEPARATOR.'bill_transaction'.DIRECTORY_SEPARATOR.$assetMaintenanceTransactionDirectoryName;
                Log::info($imageUploadPath);
            if (!file_exists($imageUploadPath)) {
                File::makeDirectory($imageUploadPath, $mode = 0777, true, true);
            }
            foreach($request->pre_grn_image as $preGrnImage){
                $imageArray = explode(';',$preGrnImage);
                $image = explode(',',$imageArray[1])[1];
                $pos  = strpos($preGrnImage, ';');
                $type = explode(':', substr($preGrnImage, 0, $pos))[1];
                $extension = explode('/',$type)[1];
                $filename = mt_rand(1,10000000000).sha1(time()).".{$extension}";
                $fileFullPath = $imageUploadPath.DIRECTORY_SEPARATOR.$filename;
                $transactionImageData = [
                    'asset_maintenance_transaction_id' => $assetMaintenanceTransaction->id,
                    'name' => $filename,
                    'is_pre_grn' => true
                ];
                file_put_contents($fileFullPath,base64_decode($image));
                AssetMaintenanceTransactionImages::create($transactionImageData);
            }
            $response = [
                'asset_maintenance_transaction_id' => $assetMaintenanceTransaction->id,
                'grn' => $generatedGrn
            ];
            $status = 200;
        }catch (\Exception $e){
            $data = [
                'action' => 'Upload Pre GRN images for Asset Maintenance Request',
                'exception' => $e->getMessage(),
                'params' => $request->all()
            ];
            Log::critical(json_encode($data));
            $response = array();
            $status = 500;
        }
        return response()->json($response,$status);
    }

    public function checkGeneratedGRN(Request $request,$assetMaintenanceId){
        try{
            $response = array();
            $grnGeneratedId = AssetMaintenanceTransactionStatuses::where('slug','grn-generated')->pluck('id')->first();
            $grnGeneratedTransaction = AssetMaintenanceTransaction::where('asset_maintenance_transaction_status_id',$grnGeneratedId)->where('asset_maintenance_id',$assetMaintenanceId)->orderBy('created_at','desc')->first();
            if($grnGeneratedTransaction != null){
                $response['grn'] = $grnGeneratedTransaction->grn;
                $response['asset_maintenance_transaction_id'] = $grnGeneratedTransaction->id;
                $transactionImages = AssetMaintenanceTransactionImages::where('asset_maintenance_transaction_id',$grnGeneratedTransaction->id)->where('is_pre_grn', true)->get();
                $response['images'] = array();
                $assetMaintenanceDirectoryName = sha1($assetMaintenanceId);
                $assetMaintenanceTransactionDirectoryName = sha1($grnGeneratedTransaction->id);
                $imagePath = env('ASSET_MAINTENANCE_REQUEST_IMAGE_UPLOAD').DIRECTORY_SEPARATOR.$assetMaintenanceDirectoryName.DIRECTORY_SEPARATOR.'bill_transaction'.DIRECTORY_SEPARATOR.$assetMaintenanceTransactionDirectoryName;
                foreach ($transactionImages as $image){
                    $response['images'][] = $imagePath.DIRECTORY_SEPARATOR.$image['name'];
                }
                $status = 200;
            }else{
                $status = 204;
            }
        }catch(\Exception $e){
            $data = [
                'action' => 'Check GRN Generated Asset Maintenance transaction',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            $response = array();
            $status = 500;
        }
        return response()->json($response,$status);
    }

    public function createTransaction(Request $request){
        try{
            $assetMaintenanceTransactionData = $request->except('_token','pre_grn_image','post_grn_image','component_data','vendor_name','purchase_order_id','purchase_order_transaction_id');
            $assetMaintenanceTransaction = AssetMaintenanceTransaction::findOrFail($request->asset_maintenance_transaction_id);
            $assetMaintenanceTransactionData['asset_maintenance_transaction_status_id'] = AssetMaintenanceTransactionStatuses::where('slug','bill-pending')->pluck('id')->first();
            $assetMaintenanceTransactionData['in_time'] = $assetMaintenanceTransactionData['out_time'] = Carbon::now();
            $assetMaintenanceTransaction->update($assetMaintenanceTransactionData);
            $assetMaintenanceDirectoryName = sha1($request->assetMaintenanceId);
            $assetMaintenanceTransactionDirectoryName = sha1($assetMaintenanceTransaction->id);
            $imageUploadPath = public_path().env('ASSET_MAINTENANCE_REQUEST_IMAGE_UPLOAD').DIRECTORY_SEPARATOR.$assetMaintenanceDirectoryName.DIRECTORY_SEPARATOR.'bill_transaction'.DIRECTORY_SEPARATOR.$assetMaintenanceTransactionDirectoryName;
            if (!file_exists($imageUploadPath)) {
                File::makeDirectory($imageUploadPath, $mode = 0777, true, true);
            }
            if($request->has('post_grn_image') && count($request->post_grn_image) > 0){
                foreach($request->post_grn_image as $postGrnImage){
                    $imageArray = explode(';',$postGrnImage);
                    $image = explode(',',$imageArray[1])[1];
                    $pos  = strpos($postGrnImage, ';');
                    $type = explode(':', substr($postGrnImage, 0, $pos))[1];
                    $extension = explode('/',$type)[1];
                    $filename = mt_rand(1,10000000000).sha1(time()).".{$extension}";
                    $fileFullPath = $imageUploadPath.DIRECTORY_SEPARATOR.$filename;
                    $transactionImageData = [
                        'asset_maintenance_transaction_id' => $assetMaintenanceTransaction->id,
                        'name' => $filename,
                        'is_pre_grn' => false
                    ];
                    file_put_contents($fileFullPath,base64_decode($image));
                    AssetMaintenanceTransactionImages::create($transactionImageData);
                }
            }
            $request->session()->flash('success','Transaction added successfully');
            return redirect('/asset/maintenance/request/view/'.$request->assetMaintenanceId);
        }catch(\Exception $e){
            $data = [
                'action' => 'Create Asset Maintenance Transaction',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function viewTransaction(Request $request,$assetMaintenanceTransactionId){
        try{
                $assetMaintenanceTransaction = AssetMaintenanceTransaction::where('id',$assetMaintenanceTransactionId)->first();
                if(count($assetMaintenanceTransaction->assetMaintenanceTransactionImage) > 0){
                    $assetMaintenanceDirectoryName = sha1($assetMaintenanceTransaction['asset_maintenance_id']);
                    $assetMaintenanceTransactionDirectoryName = sha1($assetMaintenanceTransaction['id']);
                    $imageData = array();
                    $iterator = 0;
                    foreach($assetMaintenanceTransaction->assetMaintenanceTransactionImage as $key => $assetMaintenanceTransactionImageData){
                        $imageData[$iterator]['id'] = $assetMaintenanceTransactionImageData['id'];
                        $imageData[$iterator]['upload_path'] = url('/').env('ASSET_MAINTENANCE_REQUEST_IMAGE_UPLOAD') .DIRECTORY_SEPARATOR.$assetMaintenanceDirectoryName.DIRECTORY_SEPARATOR.'bill_transaction'.DIRECTORY_SEPARATOR.$assetMaintenanceTransactionDirectoryName. DIRECTORY_SEPARATOR . $assetMaintenanceTransactionImageData['name'];
                        $iterator++;
                    }
                }
            return view('partials.asset-maintenance.view-transaction')->with(compact('assetMaintenanceTransaction','imageData'));
        }catch(\Exception $e){
            $data = [
                'action' => 'View Asset Maintenance Transaction Detail',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

}