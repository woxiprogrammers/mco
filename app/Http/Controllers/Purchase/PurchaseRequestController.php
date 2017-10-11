<?php

namespace App\Http\Controllers\Purchase;

use App\Http\Controllers\CustomTraits\Purchase\MaterialRequestTrait;
use App\Material;
use App\MaterialRequestComponentHistory;
use App\MaterialRequestComponents;
use App\MaterialRequestComponentTypes;
use App\PurchaseRequest;
use App\PurchaseRequestComponent;
use App\PurchaseRequestComponentStatuses;
use App\Quotation;
use App\Unit;
use App\Vendor;
use App\VendorMaterialRelation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PurchaseRequestController extends Controller
{
    use MaterialRequestTrait;
    public function __construct()
    {
        $this->middleware('custom.auth');
    }
    public function getManageView(Request $request){
        return view('purchase/purchase-request/manage');
    }
    public function getCreateView(Request $request){
        try{
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
        }catch(\Exception $e){
            $data = [
                'action' => 'Get Purchase Request create view',
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getEditView(Request $request,$status,$id){
        try{
            $user = Auth::user();
            $userRole = $user->roles[0]->role->slug;
            if($status == "p-r-admin-approved"){
                $purchaseRequest = PurchaseRequest::where('id',$id)->first();
                $materialRequestComponentIds = PurchaseRequestComponent::where('purchase_request_id',$id)->pluck('material_request_component_id');
                $materialRequestComponentDetails = MaterialRequestComponents::whereIn('id',$materialRequestComponentIds)->orderBy('id','asc')->get();
                $materialRequestComponentID = MaterialRequestComponentTypes::where('slug','quotation-material')->pluck('id')->first();
                $allVendors = Vendor::where('is_active','true')->select('id','company')->get()->toArray();
                $iterator = 0;
                foreach($materialRequestComponentDetails as $key => $materialRequestComponent){
                    if($materialRequestComponentID == $materialRequestComponent->component_type_id){
                        $material_id = Material::where('name','like',$materialRequestComponent->name)->pluck('id');
                        $vendorAssignedIds = VendorMaterialRelation::where('material_id',$material_id)->pluck('vendor_id');
                        if(count($vendorAssignedIds) > 0){
                            $materialRequestComponentDetails[$iterator]['vendors'] = Vendor::whereIn('id',$vendorAssignedIds)->select('id','company')->get()->toArray();
                        }else{
                            $materialRequestComponentDetails[$iterator]['vendors'] = $allVendors;
                        }
                    }else{
                        $materialRequestComponentDetails[$iterator]['vendors'] = $allVendors;
                    }
                    $iterator++;
                }
                return view('purchase/purchase-request/edit-approved')->with(compact('purchaseRequest','materialRequestComponentDetails','userRole'));
            }else{
                return view('purchase/purchase-request/edit-draft');
            }
        }catch(\Exception $e){
            $data = [
                'action' => 'Get Purchase Request Edit View',
                'params' => $request->all(),
                'status' => $status,
                'id' => $id,
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
        }
    }

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

    public function purchaseRequestListing(Request $request){
        try{
            $response = array();
            $responseStatus = 200;
            $purchaseRequests = PurchaseRequest::all();
            $start = $request->start;
            $end = ($start + $request->length) > count($purchaseRequests) ? count($purchaseRequests) : ($start + $request->length);
            $iTotalRecords = count($purchaseRequests);
            $records = array();
            $records['data'] = array();
            $records["draw"] = intval($request->draw);
            $records["recordsTotal"] = $iTotalRecords;
            $records["recordsFiltered"] = $iTotalRecords;
            $purchaseRequestsData = $purchaseRequests->slice($start,$request->length);
            $iterator = 0;
            foreach($purchaseRequestsData as $purchaseRequest){
                switch ($purchaseRequest->status->slug){
                    case 'purchase-requested':
                        $status = "<span class=\"btn btn-xs btn-warning\"> ".$purchaseRequest->status->name." </span>";
                        $action = '<div class="btn-group">
                            <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                Actions
                                <i class="fa fa-angle-down"></i>
                            </button>
                            <ul class="dropdown-menu pull-left" role="menu">
                                <li>.'
                                    .'<a href="/purchase/purchase-request/edit/'.$purchaseRequest->status->slug.'">'.
                                        '<i class="icon-docs"></i> Edit 
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);" onclick="openApproveModal('.$purchaseRequest->id.')">
                                        <i class="icon-tag"></i> Approve / Disapprove 
                                    </a>
                                </li>
                            </ul>
                        </div>';
                        break;

                    case 'p-r-admin-approved':
                    case 'p-r-manager-approved':
                        $status = "<span class=\"btn btn-xs green-meadow\"> ".$purchaseRequest->status->name." </span>";
                        $action = '<div class="btn-group">
                            <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                Actions
                                <i class="fa fa-angle-down"></i>
                            </button>
                            <ul class="dropdown-menu pull-left" role="menu">
                                <li>'
                            .'<a href="/purchase/purchase-request/edit/'.$purchaseRequest->status->slug.'/'.$purchaseRequest->id.'">'.
                                        '<i class="icon-docs"></i> Edit 
                                    </a>
                                </li>
                            </ul>
                        </div>';
                        break;

                    case 'p-r-manager-disapproved':
                    case 'p-r-admin-disapproved':
                        $status = "<span class=\"btn btn-xs btn-danger\"> ".$purchaseRequest->status->name." </span>";
                        $action = '<div class="btn-group">
                            <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                Actions
                                <i class="fa fa-angle-down"></i>
                            </button>
                            <ul class="dropdown-menu pull-left" role="menu">
                                <li>'
                            .'<a href="/purchase/purchase-request/edit/'.$purchaseRequest->status->slug.'">'.
                                        '<i class="icon-docs"></i> Edit 
                                    </a>
                                </li>
                            </ul>
                        </div>';
                        break;

                    default:
                        $status = "<span class=\"btn btn-xs btn-success\"> ".$purchaseRequest->status->name." </span>";
                        $action = '<div class="btn-group">
                            <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                Actions
                                <i class="fa fa-angle-down"></i>
                            </button>
                            <ul class="dropdown-menu pull-left" role="menu">
                                <li>'
                            .'<a href="/purchase/purchase-request/edit/'.$purchaseRequest->status->slug.'">'.
                                        '<i class="icon-docs"></i> Edit 
                                    </a>
                                </li>
                            </ul>
                        </div>';
                        break;
                }
                $records['data'][$iterator] = [
                    $purchaseRequest->id,
                    $purchaseRequest->projectSite->project->client->company,
                    $purchaseRequest->projectSite->project->name.' - '.$purchaseRequest->projectSite->name,
                    $status,
                    $action
                ];
                $iterator++;
            }
        }catch (\Exception $e){
            $data = [
                'action' => 'Purchase Requests listing',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            $responseStatus = 500;
            $records = array();
        }
        return response()->json($records,$responseStatus);
    }

    public function changePurchaseRequestStatus(Request $request,$newStatus,$purchaseRequestId = null){
        try{
            if($purchaseRequestId == null){
                $purchaseRequestId = $request->purchaseRequestId;
            }
            $user = Auth::user();
            $materialComponentHistoryData = array();
            $materialComponentHistoryData['remark'] = $request->remark;
            $materialComponentHistoryData['user_id'] = $user->id;
            switch ($newStatus){
                case 'approved':
                    if($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin'){
                        $approveStatusId = PurchaseRequestComponentStatuses::where('slug','p-r-admin-approved')->pluck('id')->first();
                    }else{
                        $approveStatusId = PurchaseRequestComponentStatuses::where('slug','p-r-manager-approved')->pluck('id')->first();
                    }
                    PurchaseRequest::where('id',$purchaseRequestId)->update([
                                        'purchase_component_status_id' => $approveStatusId
                                    ]);
                    $materialComponentIds = PurchaseRequestComponent::where('purchase_request_id',$purchaseRequestId)->pluck('material_request_component_id')->toArray();
                    MaterialRequestComponents::whereIn('id',$materialComponentIds)->update(['component_status_id' => $approveStatusId]);
                    $materialComponentHistoryData['component_status_id'] = $approveStatusId;
                    foreach($materialComponentIds as $materialComponentId) {
                        $materialComponentHistoryData['material_request_component_id'] = $materialComponentId;
                        MaterialRequestComponentHistory::create($materialComponentHistoryData);
                    }
                    break;

                case 'disapproved':
                    if($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin'){
                        $disapproveStatusId = PurchaseRequestComponentStatuses::where('slug','p-r-admin-disapproved')->pluck('id')->first();
                    }else{
                        $disapproveStatusId = PurchaseRequestComponentStatuses::where('slug','p-r-manager-disapproved')->pluck('id')->first();
                    }
                    PurchaseRequest::where('id',$purchaseRequestId)->update([
                        'purchase_component_status_id' => $disapproveStatusId
                    ]);
                    $materialComponentIds = PurchaseRequestComponent::where('purchase_request_id',$purchaseRequestId)->pluck('material_request_component_id')->toArray();
                    MaterialRequestComponents::whereIn('id',$materialComponentIds)->update(['component_status_id' => $disapproveStatusId]);
                    $materialComponentHistoryData['component_status_id'] = $disapproveStatusId;
                    foreach($materialComponentIds as $materialComponentId) {
                        $materialComponentHistoryData['material_request_component_id'] = $materialComponentId;
                        MaterialRequestComponentHistory::create($materialComponentHistoryData);
                    }
                    break;

                default:
                    break;
            }
            $request->session()->flash('success', 'Purchase Request status changed successfully.');
            return redirect('/purchase/purchase-request/manage');
        }catch (\Exception $e){
            $data = [
                'action' => 'Change Purchase request status',
                'params' => $request->all(),
                'newStatus' => $newStatus,
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getVendorAssignmentPartial(Request $request){
        try{
            dd($request->all());
        }catch (\Exception $e){
            $data = [
                'action' => 'Get Vendor Assignment Partial Blade',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function createVendorQuotationPdf(Request $request){
        try{
            $pdf = App::make('dompdf.wrapper');
            $pdf->loadHTML(view('purchase.purchase-request.pdf.vendor-quotation'));
            return $pdf->stream();
        }catch(\Exception $e){
            $data = [
                'action' => 'Create vendor quotation PDF',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }
}
