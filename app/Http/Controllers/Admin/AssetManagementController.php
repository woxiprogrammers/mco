<?php

namespace App\Http\Controllers\Admin;

use App\Asset;
use App\AssetImage;
use App\AssetType;
use App\AssetVendorRelation;
use App\Http\Controllers\CustomTraits\Inventory\InventoryTrait;
use App\InventoryComponent;
use App\InventoryComponentTransfers;
use App\InventoryComponentTransferStatus;
use App\InventoryTransferTypes;
use App\ProjectSite;
use App\Unit;
use App\Vendor;
use Dompdf\Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;


class AssetManagementController extends Controller
{
use InventoryTrait;
    public function __construct(){
        $this->middleware('custom.auth');
    }

    public function getManageView(Request $request){
        return view('admin.asset.manage');
    }

    public function getCreateView(Request $request){
        try{
            $asset_types = AssetType::select('id','name')->get()->toArray();
        }catch(\Exception $e){
            $data = [
                'action' => "Get asset create view",
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
        return view('admin.asset.create')->with(compact('asset_types'));
    }

    public function getEditView(Request $request,$asset){
        try{
            $projectSiteId = Session::get('global_project_site');
            $inventoryComponentTransfers = InventoryComponentTransfers::join('inventory_components','inventory_components.id','=','inventory_component_transfers.inventory_component_id')
                ->where('inventory_components.reference_id',$asset['id'])
                ->where('inventory_components.is_material',false)
                ->where('inventory_component_transfers.inventory_component_transfer_status_id',InventoryComponentTransferStatus::where('slug','approved')->pluck('id')->first())
                ->orderBy('inventory_component_transfers.created_at','asc')->select('inventory_component_transfers.id','inventory_component_transfers.transfer_type_id','inventory_component_transfers.quantity','inventory_component_transfers.related_transfer_id')->get();
            $isAssigned = false;
            $quantityAssigned = $jIterator = 0;
            foreach($inventoryComponentTransfers as $key => $inventoryComponentTransfer){
                if($inventoryComponentTransfer->transferType->type == 'IN'){
                    $isAssigned = true;
                    $quantityAssigned = $quantityAssigned + $inventoryComponentTransfer['quantity'];
                }elseif($inventoryComponentTransfer->transferType->type == 'OUT' && $inventoryComponentTransfer->transferType->slug == 'site' && $inventoryComponentTransfer['related_transfer_id'] != null){
                    $quantityAssigned = $quantityAssigned - $inventoryComponentTransfer['quantity'];
                }else{
                    $isAssigned = false;
                }
            }
            $openingStock = InventoryComponent::where('reference_id',$asset['id'])->where('project_site_id',$projectSiteId)->where('is_material',false)->pluck('opening_stock')->first();
            $quantityAssigned = $quantityAssigned + $openingStock;
            if($asset->assetTypes->slug == 'other'){
                $remainingQuantity = $asset['quantity'] - $quantityAssigned;
                $isAssigned = ($remainingQuantity > 0) ? false : true;
            }else{
                $remainingQuantity = 1;
            }
            $projectSiteData = array();
            $projectSites = ProjectSite::join('projects','projects.id','=','project_sites.project_id')
                                            ->join('clients','clients.id','=','projects.client_id')
                                            ->where('projects.is_active',true)
                                            ->select('project_sites.id','project_sites.name as project_site_name','projects.name as project_name','clients.company')->get()->toArray();
            $iterator = 0;
            foreach($projectSites as $key => $projectSite){
                $projectSiteData[$iterator]['id'] = $projectSite['id'];
                $projectSiteData[$iterator]['name'] = $projectSite['project_name'];
                $iterator++;
            }
            $asset_types = AssetType::select('id','name')->get()->toArray();
            $assetId = $asset['id'];
            $assetImages = AssetImage::where('asset_id',$assetId)->select('id','name')->get();
            if($assetImages != null){
                $assetImage = $this->getImagePath($assetId,$assetImages);
            }
            if ($asset['is_day_wise'] == true){
                $maintenancePeriodType = 'day_wise';
             }elseif ($asset['is_day_wise'] == false){
                $maintenancePeriodType = 'hour_wise';
            }else{
                $maintenancePeriodType = '';
            }
            $vendorsAssigned = AssetVendorRelation::where('asset_id',$asset['id'])->get();
        }catch (\Exception $e){
            $data = [
                'action' => "Get asset edit view",
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
        return view('admin.asset.edit')->with(compact('asset','assetImage','asset_types','projectSiteData','isAssigned','quantityAssigned','remainingQuantity','vendorsAssigned','maintenancePeriodType'));
    }

    public function createAsset(Request $request){
        try{
            $data = Array();
            $data['name'] = $request->name;
            $data['model_number'] = $request->model_number;
            $data['expiry_date'] = $request->expiry_date;
            $data['price'] = $request->price;
            $data['asset_types_id'] = $request->asset_type;
            $data['electricity_per_unit'] = $request->electricity_per_unit;
            $data['litre_per_unit'] = $request->litre_per_unit;
            $data['is_active'] = false;
            $data['quantity'] = $request->qty;
            $data['rent_per_day'] = $request->rent_per_day;
            $data['maintenance_period'] = $request->maintenance_period;
            $data['is_day_wise'] = ($request->has('maintenance_period_type') && $request['maintenance_period_type'] == 'day_wise' && $request['maintenance_period_type'] != null) ? true : false;

            $asset = Asset::create($data);
            if($request->work_order_images != null) {
                $assetId = $asset['id'];
                $work_order_images = $request->work_order_images;
                $assetDirectoryName = sha1($assetId);
                $UploadPath = public_path() . env('ASSET_IMAGE_UPLOAD');
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
                    $data['asset_id'] = $assetId;
                    AssetImage::create($data);
                    $oldFilePath = public_path() . $imagePath;
                    $newFilePath = public_path() . env('ASSET_IMAGE_UPLOAD') . DIRECTORY_SEPARATOR . $assetDirectoryName . DIRECTORY_SEPARATOR . $filename;
                    File::move($oldFilePath, $newFilePath);
                }
            }
            $request->session()->flash('success', 'Asset Created successfully.');
            return redirect('/asset/manage');
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
            $assetData = $request->except('_token','name','asset_type','qty','maintenance_period_type');
            $assetData['is_day_wise'] = ($request->has('maintenance_period_type') && $request['maintenance_period_type'] == 'day_wise' && $request['maintenance_period_type'] != null) ? true : false;
            $assetData['name'] = ucwords(trim($data['name']));
            $assetData['asset_types_id'] = $data['asset_type'];
            $assetData['quantity'] = $data['qty'];
            $asset->update($assetData);
            $work_order_images = $request->work_order_images;
            $assetImages = $request->asset_images;
            $assetId = $asset->id;
            if($work_order_images != null) {
                $assetDirectoryName = sha1($assetId);
                $UploadPath = public_path() . env('ASSET_IMAGE_UPLOAD');
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
                    $data['asset_id'] = $assetId;
                    AssetImage::create($data);
                    $oldFilePath = public_path() . $imagePath;
                    $newFilePath = public_path() . env('ASSET_IMAGE_UPLOAD') . DIRECTORY_SEPARATOR . $assetDirectoryName . DIRECTORY_SEPARATOR . $filename;
                    File::move($oldFilePath, $newFilePath);
                }

            }

            if ($work_order_images != null && $assetImages != null) {
                $existingImages = array_column(array_merge($work_order_images, $assetImages), "image_name");
            } elseif ($work_order_images != null) {
                $existingImages = array_column($work_order_images, "image_name");
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
            $deletedAssetImages = AssetImage::where('asset_id', $assetId)->whereNotIn('name', $filename)->get();
            foreach ($deletedAssetImages as $images) {
                $images->delete();
            }

            $request->session()->flash('success', 'Asset Edited successfully.');
            return redirect('/asset/manage');
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

    public function assignProjectSite(Request $request,$asset){
        try{
            $inventoryComponentTransfers = InventoryComponentTransfers::join('inventory_components','inventory_components.id','=','inventory_component_transfers.inventory_component_id')
                ->where('inventory_components.reference_id',$asset['id'])
                ->where('inventory_components.is_material',false)
                ->where('inventory_component_transfers.inventory_component_transfer_status_id',InventoryComponentTransferStatus::where('slug','approved')->pluck('id')->first())
                ->orderBy('inventory_component_transfers.created_at','asc')->select('inventory_component_transfers.id','inventory_component_transfers.transfer_type_id','inventory_component_transfers.quantity')->get();
            $quantityAssigned = 0;
            foreach($inventoryComponentTransfers as $key => $inventoryComponentTransfer){
                if($inventoryComponentTransfer->transferType->type == 'IN'){
                    $quantityAssigned = $quantityAssigned + $inventoryComponentTransfer['quantity'];
                }elseif($inventoryComponentTransfer->transferType->type == 'OUT' && $inventoryComponentTransfer->transferType->slug == 'site' && $inventoryComponentTransfer['related_transfer_id'] != null){
                    $quantityAssigned = $quantityAssigned - $inventoryComponentTransfer['quantity'];
                }
            }
            $openingStock = InventoryComponent::where('reference_id',$asset['id'])->where('is_material',false)->pluck('opening_stock')->first();
            $quantityAssigned = $quantityAssigned + $openingStock;
            if($asset->assetTypes->slug == 'other'){
                $remainingQuantity = $asset['quantity'] - $quantityAssigned;
            }else{
                $remainingQuantity = 1;
            }
            if($remainingQuantity < $request['quantity']){
                $request->session()->flash('success', 'Allowed Quantity is '.$remainingQuantity);
            }else{
                $user = Auth::user();
                $inventoryComponentId = InventoryComponent::where('project_site_id',$request['project_site_id'])->where('reference_id',$asset['id'])->where('is_material',false)->pluck('id')->first();
                if($inventoryComponentId == null){
                    $inventoryComponentData['name'] = $asset['name'];
                    $inventoryComponentData['is_material'] = false;
                    $inventoryComponentData['project_site_id'] = $request->project_site_id;
                    $inventoryComponentData['opening_stock'] = 0;
                    $inventoryComponentData['reference_id'] = $asset['id'];
                    $inventoryComponent = InventoryComponent::create($inventoryComponentData);
                    $inventoryComponentId = $inventoryComponent['id'];
                }
                $inventoryComponentTransferData = [
                    'inventory_component_id' => $inventoryComponentId,
                    'transfer_type_id' => InventoryTransferTypes::where('type','ilike','IN')->where('slug','office')->pluck('id')->first(),
                    'source_name' => env('OFFICE_PROJECT_SITE_NAME'),
                    'quantity' => $request['quantity'],
                    'unit_id' => Unit::where('slug','nos')->pluck('id')->first(),
                    'user_id' => $user['id'],
                    'inventory_component_transfer_status_id' => InventoryComponentTransferStatus::where('slug','approved')->pluck('id')->first(),
                    'rate_per_unit' => $request['rent_per_day']
                ];
                $inventoryComponentTransfer = $this->createInventoryComponentTransfer($inventoryComponentTransferData);
                if(count($inventoryComponentTransfer) > 0){
                    $request->session()->flash('success', 'Project Site assigned successfully.');
                }else{
                    $request->session()->flash('success', 'Something went wrong.');
                }
            }

            return redirect('/asset/edit/'.$asset->id);
        }catch (Exception $e){
            $data = [
                'action' => 'Assign Asset to Project Site',
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
            $skip = $request->start;
            $take = $request->length;
            $totalRecordCount = 0;
            if($request->has('asset_name')){
                $assetData = Asset::where('name','ilike','%'.$request['asset_name'].'%')
                            ->skip($skip)->take($take)
                            ->orderBy('name','asc')->get();
                $totalRecordCount = Asset::where('name','ilike','%'.$request['asset_name'].'%')
                ->count();
            }else{
                $assetData = Asset::orderBy('name','asc')->skip($skip)->take($take)->get();
                $totalRecordCount = Asset::count();
            }

            if ($request->has('get_total')) {
                $total = 0;
                foreach($assetData as $asset) {
                    $total = $total + $asset['price']*$asset['quantity'];
                }
                $records['total'] = $total;
            } else {
                $iTotalRecords = count($assetData);
                $records = array();
                $records['data'] = array();
                $end = $request->length < 0 ? count($assetData) : $request->length;
                for($iterator = 0,$pagination = 0; $iterator < $end && $pagination < count($assetData); $iterator++,$pagination++ ){
                    if($assetData[$pagination]['is_active'] == true){
                        $asset_status = '<td><span class="label label-sm label-success"> Enabled </span></td>';
                        $status = 'Disable';
                    }else{
                        $asset_status = '<td><span class="label label-sm label-danger"> Disabled</span></td>';
                        $status = 'Enable';
                    }
                    if($assetData[$pagination]->assetTypes == null){
                        $assetType = '';
                    }else{
                        $assetType = $assetData[$pagination]->assetTypes->name;

                    }
                    $assetImagetag = $profilePicAddress = null;
                    $assetImages = AssetImage::where('asset_id',$assetData[$pagination]['id'])->select('id','name')->get();
                    if($assetImages != null){
                        $assetImage = $this->getImagePath($assetData[$pagination]['id'],$assetImages);
                        if($assetImage != null) {
                            $profilePicAddress = env('APP_URL').$assetImage[0]['path'];
                            $assetImagetag = '<a href="'.$profilePicAddress.'" target="_blank"><img src="'.$profilePicAddress.'" height="60" width="60" style="border-radius: 50%;box-shadow: 2px 2px 1px 1px #888888;"></a>';
                        } else {
                            $profilePicAddress = env('APP_URL').'/assets/layouts/layout3/img/no-image.png';
                            $assetImagetag = '<img src="'.$profilePicAddress.'" height="60" width="60" style="border-radius: 50%;box-shadow: 2px 2px 1px 1px #888888;">';
                        }
                    } else {
                        $profilePicAddress = env('APP_URL').'/assets/layouts/layout3/img/no-image.png';
                        $assetImagetag = '<img src="'.$profilePicAddress.'" height="60" width="60" style="border-radius: 50%;box-shadow: 2px 2px 1px 1px #888888;">';
                    }

                    $qty = $assetData[$pagination]['quantity'];
                    $asset_price = $assetData[$pagination]['price'];
                    $asset_cost = $assetData[$pagination]['price']*$assetData[$pagination]['quantity'];
                    $rent_per_day = $assetData[$pagination]['rent_per_day'];
                    $records['data'][$iterator] = [
                        $assetData[$pagination]['id'],
                        $assetImagetag,
                        $assetData[$pagination]['name'],
                        $assetData[$pagination]['model_number'],
                        $qty,
                        $asset_price,
                        $asset_cost,
                        $rent_per_day,
                        $assetType,
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
                $records["recordsTotal"] = $totalRecordCount;
                $records["recordsFiltered"] = $totalRecordCount;
            }
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

    public function projectSiteAssetListing(Request $request,$assetId){
        try{
            //here
            $inventoryComponentTransfer = InventoryComponentTransfers::join('inventory_components','inventory_components.id','=','inventory_component_transfers.inventory_component_id')
                                                                        ->where('inventory_components.reference_id',$assetId)
                                                                        ->where('inventory_components.is_material',false)
                                                                        ->where('inventory_component_transfers.inventory_component_transfer_status_id',InventoryComponentTransferStatus::where('slug','approved')->pluck('id')->first())
                                                                        ->orderBy('inventory_component_transfers.created_at','desc')->get();
            $status = 200;
            $iTotalRecords = count($inventoryComponentTransfer);
            $records = array();
            $records['data'] = array();
            $end = $request->length < 0 ? count($inventoryComponentTransfer) : $request->length;
            for($iterator = 0,$pagination = $request->start; $iterator < $end && $pagination < count($inventoryComponentTransfer); $iterator++,$pagination++ ){
                if($inventoryComponentTransfer[$pagination]->transferType->type == 'IN'){
                    $assetStatus = 'Assigned';
                }else{
                    $assetStatus = 'Released';
                }
                $projectSite = ProjectSite::join('projects','projects.id','=','project_sites.project_id')
                    ->join('clients','clients.id','=','projects.client_id')
                    ->where('project_sites.id',$inventoryComponentTransfer[$pagination]->inventoryComponent->project_site_id)
                    ->select('project_sites.id','project_sites.name as project_site_name','projects.name as project_name','clients.company')->first();
                $records['data'][$iterator] = [
                    $projectSite['company'].' - '.$projectSite['project_name'].' - '.$projectSite['project_site_name'],
                    $inventoryComponentTransfer[$pagination]->quantity,
                    $assetStatus,
                    $inventoryComponentTransfer[$pagination]->rate_per_unit,
                    $inventoryComponentTransfer[$pagination]->date,
                ];
            }
            $records["draw"] = intval($request->draw);
            $records["recordsTotal"] = $iTotalRecords;
            $records["recordsFiltered"] = $iTotalRecords;
        }catch (Exception $e){
            $status = 500;
            $records = array();
            $data = [
                'action' => 'Get Project Site Asset Listing',
                'params' => $request->all(),
                'exception'=> $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
        return response()->json($records,$status);
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

    public function checkModel(Request $request){
        try{
            $modelNumber = $request->search_model_number;
            if($request->has('id')){
                $modelCount = Asset::where('model_number','ilike',$modelNumber)->where('id','!=',$request->id)->count();
            }else{
                $modelCount = Asset::where('model_number','ilike',$modelNumber)->count();
            }
            if($modelCount > 0){
                return 'false';
            }else{
                return 'true';
            }
        }catch(\Exception $e){
            $data = [
                'action' => 'Check Model Number',
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

    public function getVendorAutoSuggest(Request $request,$keyword){
        try{
            $vendorList = Vendor::where('company','ilike','%'.$keyword.'%')->where('is_active',true)->select('id','company')->get();
            $response = array();
            if(count($vendorList) > 0){
                $response = $vendorList->toArray();
                $iterator = 0;
                foreach($response as $vendorList){
                    $response[$iterator]['tr_view'] = '<input name="vendors[]" type="hidden" value="'.$vendorList['id'].'">
                                                        <div class="row">
                                                            <div class="col-md-9"  style="text-align: left">
                                                                <label class="control-label">'.$vendorList['company'].'</label>
                                                            </div>
                                                        </div>';
                    $iterator++;
                }
            }
        }catch(\Exception $e){
            $vendorList = array();
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

    public function assignVendors(Request $request,$asset){
        try{
            if($request->has('vendors')){
                $assetVendorRelationData = array();
                foreach($request->vendors as $vendorID){
                    $check = AssetVendorRelation::where('vendor_id',$vendorID)->where('asset_id',$asset->id)->first();
                    if($check == null){
                        $assetVendorRelationData['vendor_id'] = $vendorID;
                        $assetVendorRelationData['asset_id'] = $asset->id;
                        AssetVendorRelation::create($assetVendorRelationData);
                    }
                    $request->session()->flash('success', 'Vendors assigned to asset successfully.');
                }
                $assetVendors = AssetVendorRelation::where('asset_id',$asset->id)->whereNotIn('vendor_id',$request->vendors)->get();
                foreach ($assetVendors as $assetVendor){
                    $assetVendor->delete();
                }
            }else{
                $assetVendors = AssetVendorRelation::where('asset_id',$asset->id)->get();
                foreach ($assetVendors as $assetVendor){
                    $assetVendor->delete();
                }
            }
            return redirect('/asset/edit/'.$asset->id);
        }catch(\Exception $e){
            $data = [
                'action' => 'Assign Vendor',
                'param' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }
}
