<?php

namespace App\Http\Controllers\Purchase;

use App\Http\Controllers\CustomTraits\Purchase\PurchaseTrait;
use App\MaterialRequestComponents;
use App\PurchaseOrder;
use App\PurchaseRequest;
use Dompdf\Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PurchaseOrderController extends Controller
{
    use PurchaseTrait;
    public function __construct(){
        $this->middleware('custom.auth');
    }
    public function getManageView(Request $request){
        return view('purchase/purchase-order/manage');
    }
    public function getCreateView(Request $request){
        return view('purchase/purchase-order/create');
    }
    public function getListing(Request $request){
        try{
             $purchaseOrderDetail = PurchaseOrder::get();
             $purchaseOrderList = array();
             $iterator = 0;
            if(count($purchaseOrderDetail) > 0){
                 foreach($purchaseOrderDetail as $key => $purchaseOrder){
                     $purchaseOrderList[$iterator]['purchase_order_id'] = $purchaseOrder['id'];
                     $projectSite = $purchaseOrder->purchaseRequest->projectSite;
                     $purchaseRequest = PurchaseRequest::where('id',$purchaseOrder['purchase_request_id'])->first();
                     $purchaseOrderList[$iterator]['purchase_order_format_id'] = $this->getPurchaseIDFormat('purchase-order',$projectSite['id'],$purchaseOrder['created_at'],$purchaseOrder['serial_no']);
                     $purchaseOrderList[$iterator]['purchase_request_id'] = $purchaseOrder['purchase_request_id'];
                     $purchaseOrderList[$iterator]['purchase_request_format_id'] = $this->getPurchaseIDFormat('purchase-request',$projectSite['id'],$purchaseRequest['created_at'],$purchaseRequest['serial_no']);
                     $project = $projectSite->project;
                     $purchaseOrderList[$iterator]['client_name'] = $project->client->company;
                     $purchaseOrderList[$iterator]['project'] = $project->name;
                     $purchaseOrderList[$iterator]['status'] = ($purchaseOrder['is_approved'] == true) ? '<span class="label label-sm label-success"> Approved </span>' : '<span class="label label-sm label-danger"> Disapproved </span>';
                     $iterator++;
                 }
             }
            $iTotalRecords = count($purchaseOrderList);
            $records = array();
            $iterator = 0;
            $records['data'] = array();
            for($iterator = 0,$pagination = $request->start; $iterator < $request->length && $iterator < count($purchaseOrderList); $iterator++,$pagination++ ){
                $records['data'][$iterator] = [
                    $purchaseOrderList[$pagination]['client_name'],
                    $purchaseOrderList[$pagination]['project'],
                    $purchaseOrderList[$pagination]['purchase_request_format_id'],
                    $purchaseOrderList[$pagination]['purchase_order_format_id'],
                    $purchaseOrderList[$pagination]['status'],
                    '<div id="sample_editable_1_new" class="btn btn-small blue" ><a href="/purchase/purchase-order/edit/'.$purchaseOrderList[$iterator]['purchase_order_id'].'" style="color: white">&nbsp; Edit
                </a></div>'
                ];
            }
            $records["draw"] = intval($request->draw);
            $records["recordsTotal"] = $iTotalRecords;
            $records["recordsFiltered"] = $iTotalRecords;
            $responseStatus = 200;
            return response()->json($records,$responseStatus);

        }catch(\Exception $e){
            $data = [
                'action' => 'Purchase Requests listing',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            $responseStatus = 500;
            $records = array();
        }
    }
    public function getEditView(Request $request,$id)
    {
        try{
            $purchaseOrder =PurchaseOrder::where('id',$id)->first();
            $purchaseOrderList = array();
            $iterator = 0;
            if(count($purchaseOrder) > 0){
                    $purchaseOrderList['purchase_order_id'] = $purchaseOrder['id'];
                    $projectSite = $purchaseOrder->purchaseRequest->projectSite;
                    $purchaseRequest = PurchaseRequest::where('id',$purchaseOrder['purchase_request_id'])->first();
                    $purchaseOrderList['purchase_order_format_id'] = $this->getPurchaseIDFormat('purchase-order',$projectSite['id'],$purchaseOrder['created_at'],$purchaseOrder['serial_no']);
                    $purchaseOrderList['purchase_request_id'] = $purchaseOrder['purchase_request_id'];
                    $purchaseOrderList['purchase_request_format_id'] = $this->getPurchaseIDFormat('purchase-request',$projectSite['id'],$purchaseRequest['created_at'],$purchaseRequest['serial_no']);
                    $project = $projectSite->project;
                    $purchaseOrderList['client_name'] = $project->client->company;
                    $purchaseOrderList['project'] = $project->name;
                    $purchaseOrderList['vendor_name'] = $purchaseOrder->vendor->name;
                    $purchaseOrderList['status'] = ($purchaseOrder['is_approved'] == true) ? '<span class="label label-sm label-success"> Approved </span>' : '<span class="label label-sm label-danger"> Disapproved </span>';
            }
            $materialList = array();
            foreach($purchaseOrder->purchaseOrderComponent as $key => $purchaseOrderComponent){
                $materialRequestComponent = $purchaseOrderComponent->purchaseRequestComponent->materialRequestComponent;
                $materialList[$iterator]['purchase_order_component_id'] = $purchaseOrderComponent['id'];
                $materialList[$iterator]['material_request_component_id'] = $materialRequestComponent['id'];
                $materialList[$iterator]['material_component_name'] = $materialRequestComponent['name'];
                $materialList[$iterator]['material_component_unit_id'] = $materialRequestComponent['unit_id'];
                $materialList[$iterator]['material_component_unit_name'] = $materialRequestComponent->unit->name;
                $materialList[$iterator]['material_component_quantity'] = $materialRequestComponent->quantity;
                $materialList[$iterator]['material_component_images'][0]['image_id'] = 1;
                $materialList[$iterator]['material_component_images'][0]['image_url'] = '/assets/global/img/logo.jpg';
                $iterator++;
            }
        }catch (\Exception $e){
            $message = "Fail";
            $status = 500;
        }
        return view('purchase/purchase-order/edit')->with(compact('purchaseOrderList','materialList'));
    }
}
