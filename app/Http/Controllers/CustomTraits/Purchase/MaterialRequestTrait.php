<?php
/**
 * Created by Ameya Joshi.
 * Date: 3/10/17
 * Time: 12:59 PM
 */
namespace App\Http\Controllers\CustomTraits\Purchase;

use App\Asset;
use App\Material;
use App\MaterialRequestComponentHistory;
use App\MaterialRequestComponentImages;
use App\MaterialRequestComponents;
use App\MaterialRequestComponentTypes;
use App\MaterialRequestComponentVersion;
use App\MaterialRequests;
use App\PurchaseRequestComponentStatuses;
use App\Quotation;
use App\QuotationMaterial;
use App\Unit;
use App\UnitConversion;
use App\User;
use Carbon\Carbon;
use Cviebrock\EloquentSluggable\Services\SlugService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Cviebrock\EloquentSluggable\Sluggable;

trait MaterialRequestTrait{
    public function createMaterialRequest($data,$user,$is_purchase_request){
        try{
            $quotationId = Quotation::where('project_site_id',$data['project_site_id'])->pluck('id')->first();
            $materialRequestData = array();
            $materialRequestData['project_site_id'] = $data['project_site_id'];
            $materialRequestData['user_id'] = $user['id'];
            $materialRequestData['quotation_id'] = $quotationId != null ? $quotationId : null;
            $materialRequestData['assigned_to'] = $user['id'];
            $materialRequestData['on_behalf_of'] = $data['user_id'];
            $today = date('Y-m-d');
            $count = MaterialRequests::whereDate('created_at',$today)->count();
            $materialRequestData['serial_no'] = ($count+1);
            $materialRequestData['format_id'] =  $this->getPurchaseIDFormat('material-request',$data['project_site_id'],Carbon::now(),$materialRequestData['serial_no']);
            $materialRequest = MaterialRequests::create($materialRequestData);
            $pendingStatusId = PurchaseRequestComponentStatuses::where('slug','pending')->pluck('id')->first();
            $prAssignedStatusId = PurchaseRequestComponentStatuses::where('slug','p-r-assigned')->pluck('id')->first();
            $iterator = 0;
            $materialComponentHistoryData = array();
            $materialComponentHistoryData['remark'] = $materialRequestComponentVersionData['remark'] = '';
            $materialComponentHistoryData['user_id'] = $materialRequestComponentVersionData['user_id'] = $user['id'];
            $materialRequestComponent = array();
            $userTokens = User::join('user_has_permissions','users.id','=','user_has_permissions.user_id')
                ->join('permissions','permissions.id','=','user_has_permissions.permission_id')
                ->join('user_project_site_relation','users.id','=','user_project_site_relation.user_id')
                ->where('permissions.name','approve-material-request')
                ->where('user_project_site_relation.project_site_id',$data['project_site_id'])
                ->select('users.web_fcm_token as web_fcm_token','users.mobile_fcm_token as mobile_fcm_token')
                ->get()
                ->toArray();
            $webTokens = array_column($userTokens,'web_fcm_token');
            $mobileTokens = array_column($userTokens,'mobile_fcm_token');
            foreach($data['item_list'] as $key => $itemData){
                $itemData['name'] = str_replace("$!@#$",'"',$itemData['name']);
                $materialRequestComponentData = $this->checkComponentType($itemData,$data['project_site_id']);
                $materialRequestComponentData['material_request_id'] = $materialRequest['id'];
                //$materialRequestComponentData['name'] = $itemData['name'];
                $materialRequestComponentData['quantity'] = $materialRequestComponentVersionData['quantity'] = $itemData['quantity_id'];
                $materialRequestComponentVersionData['unit_id'] = $materialRequestComponentData['unit_id'];
                $unitName = Unit::where('id',$materialRequestComponentData['unit_id'])->pluck('name')->first();
                if($is_purchase_request == true){
                    $materialRequestComponentData['component_status_id'] = $prAssignedStatusId;
                    $materialComponentHistoryData['component_status_id'] = $materialRequestComponentVersionData['component_status_id'] = $prAssignedStatusId;
                }else{
                    $materialRequestComponentData['component_status_id'] = $pendingStatusId;
                    $materialComponentHistoryData['component_status_id'] = $materialRequestComponentVersionData['component_status_id'] = $pendingStatusId;
                }
               // $materialRequestComponentData['component_type_id'] = $itemData['component_type_id'];
                $materialRequestComponentData['created_at'] = Carbon::now();
                $materialRequestComponentData['updated_at'] = Carbon::now();
                $materialRequestComponentCount = MaterialRequestComponents::whereDate('created_at',$today)->count();
                $materialRequestComponentData['serial_no'] = ($materialRequestComponentCount+1);
                $materialRequestComponentData['format_id'] =  $this->getPurchaseIDFormat('material-request-component',$data['project_site_id'],$materialRequestComponentData['created_at'],$materialRequestComponentData['serial_no']);
                $materialRequestComponent[$iterator] = MaterialRequestComponents::insertGetId($materialRequestComponentData);
                $notificationString = '1 -'.$materialRequest->projectSite->project->name.' '.$materialRequest->projectSite->name;
                $notificationString .= ' '.$user['first_name'].' '.$user['last_name'].'Material Request Created.';
                $notificationString .= ' '.$itemData['name'].' '.$materialRequestComponentData['quantity'].' '.$unitName;
                $this->sendPushNotification('Manisha Construction',$notificationString,$webTokens,$mobileTokens,'c-m-r');
                $materialComponentHistoryData['material_request_component_id'] = $materialRequestComponentVersionData['material_request_component_id'] = $materialRequestComponent[$iterator];
                MaterialRequestComponentHistory::create($materialComponentHistoryData);
                $materialRequestComponentVersion = MaterialRequestComponentVersion::create($materialRequestComponentVersionData);
                if(array_has($itemData,'images')){
                    $sha1MaterialRequestId = sha1($materialRequest['id']);
                    foreach($itemData['images'] as $key1 => $imageName){
                        $imageUploadNewPath = public_path().env('MATERIAL_REQUEST_IMAGE_UPLOAD').$sha1MaterialRequestId;
                        if(!file_exists($imageUploadNewPath)) {
                            File::makeDirectory($imageUploadNewPath, $mode = 0777, true, true);
                        }
                        $filename = mt_rand(1,10000000000).sha1(time()).".jpg";
                        $imageUploadNewPath .= DIRECTORY_SEPARATOR.$filename;
                        $image_name = explode(",",$imageName);
                        file_put_contents($imageUploadNewPath,base64_decode($image_name[1]));
                        MaterialRequestComponentImages::create(['name' => $filename,'material_request_component_id' => $materialRequestComponent[$iterator]]);
                    }
                }
                $iterator++;
            }
        }catch(\Exception $e){
            $materialRequestComponent = null;
            $errorData = [
                'action' => 'Create Material Request function',
                'params' => $data,
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($errorData));
        }
        return $materialRequestComponent;
    }

    public function getPurchaseIDFormat($slug,$project_site_id,$created_at,$serial_no = 1){
        try{
            if($serial_no == null){
                $serial_no = 0;
            }
            switch ($slug){
                case 'material-request' :
                    $format = "MR".$project_site_id.date('Ymd',strtotime($created_at)).$serial_no;
                    break;

                case 'material-request-component' :
                    $format = "M".$project_site_id.date('Ymd',strtotime($created_at)).$serial_no;
                    break;

                case 'purchase-request' :
                    $format = "PR".$project_site_id.date('Ymd',strtotime($created_at)).$serial_no;
                    break;

                case 'purchase-order' :
                    $format = "PO".$project_site_id.date('Ymd',strtotime($created_at)).$serial_no;
                    break;

                case 'purchase-order-bill':
                    $format = "BI".$project_site_id.date('Ymd',strtotime($created_at)).$serial_no;
                    break;

                default :
                    $format = "";
                    break;
            }
        }catch(\Exception $e){
            $format = "";
            $data = [
                'action' => 'Get Purchase ID Format',
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
        }
        return $format;
    }

    public function checkComponentType($componentData,$projectSiteId){
        try{
            $materialComponentTypes = MaterialRequestComponentTypes::whereIn('slug',['quotation-material','structure-material','new-material'])->select('id','slug')->get();
            $assetComponentTypes = MaterialRequestComponentTypes::whereIn('slug',['system-asset','new-asset'])->select('id','slug')->get();
            if(in_array($componentData['component_type_id'], array_column($materialComponentTypes->toArray(), 'id'))){
                $quotation = Quotation::where('project_site_id',$projectSiteId)->first();
                if(count($quotation) != null){
                    $quotationMaterial = Material::join('quotation_materials','quotation_materials.material_id','=','materials.id')
                                            ->where('quotation_materials.id',$quotation['id'])
                                            ->where(strtolower('materials.name'),'ilike',strtolower($componentData['name']))
                                            ->select('materials.name','materials.unit_id')->first();
                    if(count($quotationMaterial) > 0){
                        $materialRequestComponentData['name'] = $quotationMaterial['name'];
                        $materialRequestComponentData['component_type_id'] = $materialComponentTypes->where('slug','quotation-material')->pluck('id')->first();
                        $unitConversionIds1 = UnitConversion::where('unit_1_id',$quotationMaterial['unit_id'])->pluck('unit_2_id')->toArray();
                        $unitConversionIds2 = UnitConversion::where('unit_2_id',$quotationMaterial['unit_id'])->pluck('unit_1_id')->toArray();
                        $unitIds = array_merge(array($quotationMaterial['unit_id']),$unitConversionIds1,$unitConversionIds2);
                        if(in_array($componentData['unit_id'],$unitIds)){
                            $materialRequestComponentData['unit_id'] = $componentData['unit_id'];
                        }else{
                            $materialRequestComponentData['unit_id'] = $quotationMaterial['unit_id'];
                        }
                    }else{
                        $materialName = Material::where(strtolower('name'),'ilike',strtolower($componentData['name']))
                                        ->select('name','unit_id')->first();
                        if(count($materialName) > 0){
                            $materialRequestComponentData['name'] = $materialName['name'];
                            $materialRequestComponentData['component_type_id'] = $materialComponentTypes->where('slug','structure-material')->pluck('id')->first();
                            $unitConversionIds1 = UnitConversion::where('unit_1_id',$materialName['unit_id'])->pluck('unit_2_id')->toArray();
                            $unitConversionIds2 = UnitConversion::where('unit_2_id',$materialName['unit_id'])->pluck('unit_1_id')->toArray();
                            $unitIds = array_merge(array($materialName['unit_id']),$unitConversionIds1,$unitConversionIds2);
                            if(in_array($componentData['unit_id'],$unitIds)){
                                $materialRequestComponentData['unit_id'] = $componentData['unit_id'];
                            }else{
                                $materialRequestComponentData['unit_id'] = $materialName['unit_id'];
                            }
                        }else{
                            $materialRequestComponentData['name'] = $componentData['name'];
                            $materialRequestComponentData['component_type_id'] = $materialComponentTypes->where('slug','new-material')->pluck('id')->first();
                            $materialRequestComponentData['unit_id'] = $componentData['unit_id'];
                        }
                    }

                }else {
                    $materialName = Material::where(strtolower('name'), 'ilike', strtolower($componentData['name']))
                        ->select('name', 'unit_id')->first();
                    if (count($materialName) > 0) {
                        $materialRequestComponentData['name'] = $materialName['name'];
                        $materialRequestComponentData['component_type_id'] = $materialComponentTypes->where('slug', 'structure-material')->pluck('id')->first();
                        $unitConversionIds1 = UnitConversion::where('unit_1_id', $materialName['unit_id'])->pluck('unit_2_id')->toArray();
                        $unitConversionIds2 = UnitConversion::where('unit_2_id', $materialName['unit_id'])->pluck('unit_1_id')->toArray();
                        $unitIds = array_merge(array($materialName['unit_id']), $unitConversionIds1, $unitConversionIds2);
                        if (in_array($componentData['unit_id'], $unitIds)) {
                            $materialRequestComponentData['unit_id'] = $componentData['unit_id'];
                        } else {
                            $materialRequestComponentData['unit_id'] = $materialName['unit_id'];
                        }
                    } else {
                        $materialRequestComponentData['name'] = $componentData['name'];
                        $materialRequestComponentData['component_type_id'] = $materialComponentTypes->where('slug', 'new-material')->pluck('id')->first();
                    }
                }
            }else{
                $assetName = Asset::where(strtolower('name'),'ilike',strtolower($componentData['name']))->pluck('name')->first();
                if(count($assetName) > 0){
                    $materialRequestComponentData['name'] = $assetName;
                    $materialRequestComponentData['component_type_id'] = $assetComponentTypes->where('slug','system-asset')->pluck('id')->first();
                    $materialRequestComponentData['unit_id'] = Unit::where('slug','nos')->pluck('name')->first();
                }else{
                    $materialRequestComponentData['name'] = $componentData['name'];
                    $materialRequestComponentData['component_type_id'] = $assetComponentTypes->where('slug','new-asset')->pluck('id')->first();
                    $materialRequestComponentData['unit_id'] = $componentData['unit_id'];
                }
            }
        }catch(\Exception $e){
            $materialRequestComponentData = null;
            $errorData = [
                'action' => 'Check Component',
                'params' => $componentData,
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($errorData));
        }
        return $materialRequestComponentData;
    }
}
