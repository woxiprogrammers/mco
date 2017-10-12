<?php

namespace App\Http\Controllers\Purchase;

use App\Helper\UnitHelper;
use App\Http\Controllers\CustomTraits\Purchase\MaterialRequestTrait;
use App\Material;
use App\PurchaseOrderComponent;
use App\PurchaseRequest;
use App\PurchaseRequestComponent;
use App\PurchaseRequestComponentStatuses;
use App\PurchaseRequestComponentVendorRelation;
use App\Unit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class PurchaseOrderController extends Controller
{
    use MaterialRequestTrait;
    public function __construct(){
        $this->middleware('custom.auth');
    }
    public function getManageView(Request $request){
        return view('purchase/purchase-order/manage');
    }
    public function getCreateView(Request $request){
        try{
            $adminApprovePurchaseRequestInfo = PurchaseRequest::join('purchase_request_component_statuses','purchase_request_component_statuses.id','=','purchase_requests.purchase_component_status_id')
                                            ->where('purchase_request_component_statuses.slug','p-r-admin-approved')
                                            ->select('purchase_requests.id as id','purchase_requests.project_site_id as project_site_id','purchase_requests.created_at as created_at','purchase_requests.serial_no as serial_no')
                                            ->get()
                                            ->toArray();
            $purchaseRequests = array();
            foreach($adminApprovePurchaseRequestInfo as $purchaseRequest){
                $purchaseRequests[$purchaseRequest['id']] = $this->getPurchaseIDFormat('purchase-request',$purchaseRequest['project_site_id'],strtotime($purchaseRequest['created_at']));
            }
            return view('purchase/purchase-order/create')->with(compact('purchaseRequests'));
        }catch(\Exception $e){
            $data = [
                'action' => 'Get Purchase order create view',
                'exception' => $e->getMessage(),
                'params' => $request->all()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }

    }
    public function getEditView(Request $request)
    {

        return view('purchase/purchase-order/edit');
    }

    public function getPurchaseRequestComponents(Request $request,$purchaseRequestId){
        try{
            $purchaseOrderComponentIds = PurchaseOrderComponent::pluck('purchase_request_component_id');
            $purchaseRequestComponentData = PurchaseRequestComponent::where('purchase_request_id',$purchaseRequestId)
                                                                ->whereNotIn('id',$purchaseOrderComponentIds)
                                                                ->get();
            $purchaseRequestComponents = array();
            $iterator = 0;
            foreach ($purchaseRequestComponentData as $purchaseRequestComponent){
                Log::info('P.R. component');
                Log::info($purchaseRequestComponent);
                $requestComponentVendors = PurchaseRequestComponentVendorRelation::where('purchase_request_component_id',$purchaseRequestComponent->id)->get();
                Log::info('vendrs');
                Log::info($requestComponentVendors);
                foreach($requestComponentVendors as $vendorRelation){
                    $purchaseRequestComponents[$iterator] = array();
                    $purchaseRequestComponent[$iterator]['purchase_request_component_id'] = $purchaseRequestComponent->id;
                    $purchaseRequestComponents[$iterator]['name'] = $purchaseRequestComponent->materialRequestComponent->name;
                    $purchaseRequestComponents[$iterator]['quantity'] = $purchaseRequestComponent->materialRequestComponent->quantity;
                    $purchaseRequestComponents[$iterator]['unit_id'] = $purchaseRequestComponent->materialRequestComponent->unit_id;
                    $purchaseRequestComponents[$iterator]['vendor'] = $vendorRelation->vendor->company;
                    $purchaseRequestComponents[$iterator]['vendor_id'] = $vendorRelation->vendor_id;
                    $materialInfo = Material::where('name','ilike',$purchaseRequestComponent->materialRequestComponent->name)->first();
                    if($materialInfo == null){
                        $purchaseRequestComponents[$iterator]['rate'] = '0';
                        $purchaseRequestComponents[$iterator]['hsn_code'] = '0';
                    }else{
                        $purchaseRequestComponents[$iterator]['rate'] = UnitHelper::unitConversion($materialInfo['unit_id'],$purchaseRequestComponent->materialRequestComponent->unit_id,$materialInfo['rate_per_unit']);
                        $purchaseRequestComponents[$iterator]['hsn_code'] = $materialInfo['hsn_code'];
                    }
                    Log::info('final line');
                    Log::info($purchaseRequestComponents[$iterator]);
                    $iterator++;
                }
            }
            $unitInfo = Unit::where('is_active', true)->select('id','name')->get()->toArray();
            return view('partials.purchase.purchase-order.material-listing')->with(compact('purchaseRequestComponents','unitInfo'));
        }catch(\Exception $e){
            $data = [
                'action' => 'Get P.R. component listing in P.O.',
                'P.R.Id' => $purchaseRequestId,
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
        }
    }
}
