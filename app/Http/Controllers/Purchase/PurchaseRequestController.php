<?php

namespace App\Http\Controllers\Purchase;

use App\Http\Controllers\CustomTraits\Purchase\MaterialRequestTrait;
use App\MaterialRequestComponentHistory;
use App\MaterialRequestComponentImages;
use App\MaterialRequestComponents;
use App\MaterialRequests;
use App\PurchaseRequest;
use App\PurchaseRequestComponent;
use App\PurchaseRequestComponentStatuses;
use App\Quotation;
use App\Unit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class PurchaseRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('custom.auth');
    }
    public function getManageView(Request $request){
        return view('purchase/purchase-request/manage');
    }
    public function getCreateView(Request $request){
        $user = Auth::user();
        $nosUnitId = Unit::where('slug','nos')->pluck('id')->first();
        $materialRequestList = array();
        $inIndentStatusId = PurchaseRequestComponentStatuses::where('slug','in-indent')->pluck('id')->first();
        $iterator = 0;
        $materialRequestComponents = MaterialRequestComponents::where('component_status_id',$inIndentStatusId)->get();
        foreach($materialRequestComponents as $index => $materialRequestComponent){
            $materialRequestList[$iterator]['material_request_component_id'] = $materialRequestComponent->id;
            $materialRequestList[$iterator]['name'] = $materialRequestComponent->name;
            $materialRequestList[$iterator]['quantity'] = $materialRequestComponent->quantity;
            $materialRequestList[$iterator]['unit_id'] = $materialRequestComponent->unit_id;
            $materialRequestList[$iterator]['unit'] = $materialRequestComponent->unit->name;
            $materialRequestList[$iterator]['component_type_id'] = $materialRequestComponent->component_type_id;
            $materialRequestList[$iterator]['component_type'] = $materialRequestComponent->materialRequestComponentTypes->name;
            $materialRequestList[$iterator]['component_status_id'] = $materialRequestComponent->component_status_id;
            $materialRequestList[$iterator]['component_status'] = $materialRequestComponent->purchaseRequestComponentStatuses->name;
            $iterator++;
        }
        return view('purchase/purchase-request/create')->with(compact('materialRequestList','nosUnitId'));
    }
    public function getEditView(Request $request,$status){
        if($status == 2){
            return view('purchase/purchase-request/edit-draft');
        }else if($status == 1){
            return view('purchase/purchase-request/edit-approved');
        }
    }

    use MaterialRequestTrait;
    public function create(Request $request){
        try{
            $user = Auth::user();
            $requestData = $request->all();
            if($request->has('item_list')){
                $materialRequestComponentId = $this->createMaterialRequest($request->except('material_request_component_ids'),$user,true);
                if($materialRequestComponentId == null){
                    $request->session()->flash('error', 'Something Went Wrong');
                    return redirect('purchase/purchase-request/create');
                }else{
                    $materialRequestComponentIds = array_merge($materialRequestComponentId,$request['material_request_component_ids']);
                }
            }else{
                $materialRequestComponentIds = $request['material_request_component_ids'];
            }
            $purchaseRequestData = array();
            $quotationId = Quotation::where('project_site_id',$requestData['project_site_id'])->first();
            if($quotationId != null){
                $purchaseRequestData['quotation_id'] = $quotationId['id'];
            }
            $purchaseRequestData['project_site_id'] = $request['project_site_id'];
            $purchaseRequestData['user_id'] = $user['id'];
            $purchaseRequestData['behalf_of_user_id'] = $requestData['user_id'];
            $purchaseRequestedStatus = PurchaseRequestComponentStatuses::where('slug','purchase-requested')->first();
            $purchaseRequestData['purchase_component_status_id'] = $purchaseRequestedStatus->id;
            $purchaseRequest = PurchaseRequest::create($purchaseRequestData);
            foreach($materialRequestComponentIds as $materialRequestComponentId){
                PurchaseRequestComponent::create([
                    'purchase_request_id' => $purchaseRequest['id'],
                    'material_request_component_id' => $materialRequestComponentId
                ]);
            }
            $PRAssignedStatusId = PurchaseRequestComponentStatuses::where('slug','p-r-assigned')->pluck('id')->first();
            MaterialRequestComponents::whereIn('id',$request['material_request_component_ids'])->update(['component_status_id' => $PRAssignedStatusId]);
            $materialComponentHistoryData = array();
            $materialComponentHistoryData['remark'] = '';
            $materialComponentHistoryData['user_id'] = $user['id'];
            $materialComponentHistoryData['component_status_id'] = $PRAssignedStatusId;
            foreach($request['material_request_component_ids'] as $materialRequestComponentId){
                $materialComponentHistoryData['material_request_component_id'] = $materialRequestComponentId;
                MaterialRequestComponentHistory::create($materialComponentHistoryData);
            }
            $request->session()->flash('success', 'Purchase Request created successfully.');
            return redirect('purchase/purchase-request/create');
        }catch (\Exception $e){
            $data = [
                'action' => 'Create Purchase Request',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
        }
    }
}
