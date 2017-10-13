<?php

namespace App\Http\Controllers\Purchase;

use App\Http\Controllers\CustomTraits\Purchase\PurchaseTrait;
use App\MaterialRequestComponents;
use App\PaymentType;
use App\PurchaseOrder;
use App\PurchaseOrderBill;
use App\PurchaseOrderBillImage;
use App\PurchaseOrderBillPayment;
use App\PurchaseOrderComponent;
use App\PurchaseRequest;
use App\User;
use Carbon\Carbon;
use Dompdf\Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;

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
            $purchaseOrderComponentIDs = PurchaseOrderComponent::where('purchase_order_id',$id)->pluck('id');
            $purchaseOrderBillData = PurchaseOrderBill::whereIn('purchase_order_component_id',$purchaseOrderComponentIDs)->get();
            $purchaseOrderBillListing = array();
            $iterator = 0;
            foreach($purchaseOrderBillData as $key => $purchaseOrderBill){
                $purchaseOrderComponent = $purchaseOrderBill->purchaseOrderComponent;
                $purchaseRequestComponent = $purchaseOrderComponent->purchaseRequestComponent;
                $purchaseOrderBillListing[$iterator]['purchase_order_bill_id'] = $purchaseOrderBill['id'];
                $purchaseOrderBillListing[$iterator]['material_name'] = $purchaseRequestComponent->materialRequestComponent->name;
                $purchaseOrderBillListing[$iterator]['material_quantity'] = $purchaseOrderBill['quantity'];
                $purchaseOrderBillListing[$iterator]['unit_id'] = $purchaseOrderBill['unit_id'];
                $purchaseOrderBillListing[$iterator]['unit_name'] = $purchaseOrderBill->unit->name;
                $purchaseOrderBillListing[$iterator]['purchase_bill_grn'] = $purchaseOrderBill['grn'];
                $purchaseOrderBillListing[$iterator]['bill_amount'] = $purchaseOrderBill['bill_amount'];
                if($purchaseOrderComponent['is_amendment'] == true){
                    $purchaseOrderBillListing[$iterator]['status'] = 'Amendment Pending';
                }else{
                    $purchaseOrderBillListing[$iterator]['status'] = ($purchaseOrderBill['is_paid'] == true) ? 'Bill Paid' : 'Bill Pending';
                }
                $iterator++;
            }
            $systemUsers = User::where('is_active',true)->select('id','first_name','last_name')->get();
            $transaction_types = PaymentType::select('slug')->get();
        }catch (\Exception $e){
            $message = "Fail";
            $status = 500;
        }
        return view('purchase/purchase-order/edit')->with(compact('transaction_types','purchaseOrderList','materialList','purchaseOrderBillListing','systemUsers'));
    }
    public function getPurchaseOrderComponentDetails(Request $request){
           $data = $request->all();
           try{
               $purchaseOrderComponent = PurchaseOrderComponent::where('id',$data['component_id'])->first();
               $vendorName = $purchaseOrderComponent->purchaseOrder->vendor->name;
               $purchaseOrderComponentData['purchase_order_component_id'] = $purchaseOrderComponent['id'];
               $purchaseOrderComponentData['hsn_code'] = $purchaseOrderComponent['hsn_code'];
               $purchaseOrderComponentData['rate_per_unit'] = $purchaseOrderComponent['rate_per_unit'];
               $materialRequestComponent = $purchaseOrderComponent->purchaseRequestComponent->materialRequestComponent;
               //$purchaseOrderComponentData['quantity'] = $purchaseOrderComponent['quantity'];
               $purchaseOrderComponentData['name'] = $materialRequestComponent['name'];
               $purchaseOrderComponentData['quantity'] = $materialRequestComponent['quantity'];
               $purchaseOrderComponentData['material_component_id'] = $materialRequestComponent['id'];
               $purchaseOrderComponentData['unit_name'] = $materialRequestComponent->unit->name;
               $purchaseOrderComponentData['unit_id'] = $materialRequestComponent['unit_id'];
               $purchaseOrderComponentData['vendor_name'] = $vendorName;
               $status = 200;
               return response()->json($purchaseOrderComponentData,$status);
           }catch(\Exception $e){
                $message = $e->getMessage();
                $status = 500;
               return response()->json($message,$status);
           }
    }
    public function getPurchaseOrderMaterials(Request $request){
        try{
            $materialRequestComponent = MaterialRequestComponents::where('id',$request['material_request_component_id'])->first();
            return response()->json($materialRequestComponent);
        }catch (\Exception $e){

        }
    }
    public function createTransaction(Request $request){
        try{
            $purchaseOrderBill = $request->except('type','material','unit_name','vendor_name');
            switch($request['type']){
                case 'upload_bill' :
                    $purchaseOrderBill['is_amendment'] = false;
                    break;

                case 'create-amendment' :
                    $purchaseOrderBill['is_amendment'] = true;
                    break;
            }
            $purchaseOrderBill['is_paid'] = false;
            $currentTimeStamp = Carbon::now();
            $serialNoCount = PurchaseOrderBill::whereMonth('created_at',date_format($currentTimeStamp,'m'))->whereYear('created_at',date_format($currentTimeStamp,'Y'))->count();
            $purchaseOrderBill['grn'] = "GRN".date_format($currentTimeStamp,'Y').date_format($currentTimeStamp,'m').($serialNoCount + 1);
            $purchaseOrderBill['created_at'] = $currentTimeStamp;
            $purchaseOrderBill['updated_at'] = $currentTimeStamp;
            $purchaseOrderBillId = PurchaseOrderBill::insertGetId($purchaseOrderBill);
            $purchaseOrderBillData = PurchaseOrderBill::where('id',$purchaseOrderBillId)->first();
            $purchaseOrderId = $purchaseOrderBillData->purchaseOrderComponent->purchaseOrder['id'];
            $request->session()->flash('success','Transaction added successfully');
            return Redirect::Back();
        }catch(\Exception $e){
            $message = "Fail";
            $status = 500;
            $request->session()->flash('danger','Something went wrong');
        }
    }
    public function createPayment(Request $request){
        try{
            $purchaseOrderBillPayment['purchase_order_bill_id'] = $request['purchase_order_bill_id'];
            $purchaseOrderBillPayment['payment_id'] = PaymentType::where('slug',$request['payment_slug'])->pluck('id')->first();
            $purchaseOrderBillPayment['amount'] = $request['amount'];
            $purchaseOrderBillPayment['reference_number'] = $request['reference_number'];
            $purchaseOrderBillPayment['remark'] = $request['remark'];
            $purchaseOrderBillPayment['created_at'] = $purchaseOrderBillPayment['updated_at'] = Carbon::now();
            $purchaseOrderBillPaymentId = PurchaseOrderBillPayment::insertGetId($purchaseOrderBillPayment);
            PurchaseOrderBill::where('id',$request['purchase_order_bill_id'])->update(['is_paid' => true, 'is_amendment' => false]);
            $request->session()->flas('success','Payment added successfully');
            return Redirect::Back();
       }catch (\Exception $e){
            $data = [
                'action' => 'Create Bill Payment',
                'exception' => $e->getMessage(),
                'params' => $request->all()
            ];
            $request->session()->flash('danger','Something went wrong');
            return Redirect::Back();
        }
    }
}
