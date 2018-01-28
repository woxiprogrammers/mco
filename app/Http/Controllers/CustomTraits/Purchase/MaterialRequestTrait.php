<?php
/**
 * Created by Ameya Joshi.
 * Date: 3/10/17
 * Time: 12:59 PM
 */
namespace App\Http\Controllers\CustomTraits\Purchase;

use App\MaterialRequestComponentHistory;
use App\MaterialRequestComponentImages;
use App\MaterialRequestComponents;
use App\MaterialRequestComponentVersion;
use App\MaterialRequests;
use App\PurchaseRequestComponentStatuses;
use App\Quotation;
use App\Unit;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

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
                ->whereNotNull('users.web_fcm_token')
                ->where('user_project_site_relation.project_site_id',$data['project_site_id'])
                ->select('users.web_fcm_token as web_fcm_token','users.mobile_fcm_token as mobile_fcm_token')
                ->get()
                ->toArray();
            $tokens = array_merge(array_column($userTokens,'web_fcm_token'), array_column($userTokens,'mobile_fcm_token'));
            foreach($data['item_list'] as $key => $itemData){
                $materialRequestComponentData['material_request_id'] = $materialRequest['id'];
                $materialRequestComponentData['name'] = $itemData['name'];
                $materialRequestComponentData['quantity'] = $materialRequestComponentVersionData['quantity'] = $itemData['quantity_id'];
                $materialRequestComponentData['unit_id'] = $materialRequestComponentVersionData['unit_id'] = $itemData['unit_id'];
                $unitName = Unit::where('id',$materialRequestComponentData['unit_id'])->pluck('name')->first();
                if($is_purchase_request == true){
                    $materialRequestComponentData['component_status_id'] = $prAssignedStatusId;
                    $materialComponentHistoryData['component_status_id'] = $materialRequestComponentVersionData['component_status_id'] = $prAssignedStatusId;
                }else{
                    $materialRequestComponentData['component_status_id'] = $pendingStatusId;
                    $materialComponentHistoryData['component_status_id'] = $materialRequestComponentVersionData['component_status_id'] = $pendingStatusId;
                }
                $materialRequestComponentData['component_type_id'] = $itemData['component_type_id'];
                $materialRequestComponentData['component_status_id'] = $pendingStatusId;
                $materialRequestComponentData['created_at'] = Carbon::now();
                $materialRequestComponentData['updated_at'] = Carbon::now();
                $materialRequestComponentCount = MaterialRequestComponents::whereDate('created_at',$today)->count();
                $materialRequestComponentData['serial_no'] = ($materialRequestComponentCount+1);
                $materialRequestComponentData['format_id'] =  $this->getPurchaseIDFormat('material-request-component',$data['project_site_id'],$materialRequestComponentData['created_at'],$materialRequestComponentData['serial_no']);
                $materialRequestComponent[$iterator] = MaterialRequestComponents::insertGetId($materialRequestComponentData);
                $notificationString = '1 -'.$materialRequest->projectSite->project->name.' '.$materialRequest->projectSite->name;
                $notificationString .= ' '.$user['first_name'].' '.$user['last_name'].'Material Request Created.';
                $notificationString .= ' '.$itemData['name'].' '.$materialRequestComponentData['quantity'].' '.$unitName;
                $this->sendPushNotification('Manisha Construction',$notificationString,$tokens);
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
}