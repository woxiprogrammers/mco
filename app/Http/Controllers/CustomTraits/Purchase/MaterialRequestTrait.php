<?php
/**
 * Created by Ameya Joshi.
 * Date: 3/10/17
 * Time: 12:59 PM
 */
namespace App\Http\Controllers\CustomTraits\Purchase;

use App\MaterialRequestComponentHistory;
use App\MaterialRequestComponents;
use App\MaterialRequests;
use App\PurchaseRequestComponentStatuses;
use App\Quotation;
use Carbon\Carbon;
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
            $materialRequest = MaterialRequests::create($materialRequestData);
            $pendingStatusId = PurchaseRequestComponentStatuses::where('slug','pending')->pluck('id')->first();
            $prAssignedStatusId = PurchaseRequestComponentStatuses::where('slug','p-r-assigned')->pluck('id')->first();
            $iterator = 0;
            $materialComponentHistoryData = array();
            $materialComponentHistoryData['remark'] = '';
            $materialComponentHistoryData['user_id'] = $user['id'];
            $materialRequestComponent = array();
            foreach($data['item_list'] as $key => $itemData){
                $materialRequestComponentData['material_request_id'] = $materialRequest['id'];
                $materialRequestComponentData['name'] = $itemData['name'];
                $materialRequestComponentData['quantity'] = $itemData['quantity_id'];
                $materialRequestComponentData['unit_id'] = $itemData['unit_id'];
                if($is_purchase_request == true){
                    $materialRequestComponentData['component_status_id'] = $prAssignedStatusId;
                    $materialComponentHistoryData['component_status_id'] = $prAssignedStatusId;
                }else{
                    $materialRequestComponentData['component_status_id'] = $pendingStatusId;
                    $materialComponentHistoryData['component_status_id'] = $pendingStatusId;
                }
                $materialRequestComponentData['component_type_id'] = $itemData['component_type_id'];
                $materialRequestComponentData['component_status_id'] = $pendingStatusId;
                $materialRequestComponentData['created_at'] = Carbon::now();
                $materialRequestComponentData['updated_at'] = Carbon::now();
                $materialRequestComponent[$iterator] = MaterialRequestComponents::insertGetId($materialRequestComponentData);
                $materialComponentHistoryData['material_request_component_id'] = $materialRequestComponent[$iterator];
                MaterialRequestComponentHistory::create($materialComponentHistoryData);
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

    public function getMaterialRequestIDFormat($project_site_id,$created_at,$serial_no){
        $format = "MR".$project_site_id.date_format($created_at,'ymd').$serial_no;
        return $format;
    }
}