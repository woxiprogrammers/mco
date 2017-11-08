<?php

namespace App\Http\Controllers\Purchase;

use App\Asset;
use App\AssetType;
use App\Category;
use App\CategoryMaterialRelation;
use App\Client;
use App\Http\Controllers\CustomTraits\Inventory\InventoryTrait;
use App\InventoryComponent;
use App\InventoryTransferTypes;
use App\MaterialRequestComponents;
use App\MaterialRequestComponentTypes;
use App\MaterialVersion;
use App\PaymentType;
use App\Project;
use App\ProjectSite;
use App\PurchaseOrder;
use App\Helper\UnitHelper;
use App\Http\Controllers\CustomTraits\Purchase\MaterialRequestTrait;
use App\Material;
use App\PurchaseOrderBill;
use App\PurchaseOrderBillPayment;
use App\PurchaseOrderComponent;
use App\PurchaseRequest;
use App\Quotation;
use App\QuotationStatus;
use App\User;
use App\Vendor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use App\PurchaseOrderComponentImage;
use App\PurchaseRequestComponent;
use App\PurchaseRequestComponentVendorRelation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use App\Unit;


class PurchaseOrderController extends Controller
{
    use MaterialRequestTrait;
    use InventoryTrait;
    public function __construct(){
        $this->middleware('custom.auth');
    }
    public function getManageView(Request $request){
        $approvedQuotationStatus = QuotationStatus::where('slug','approved')->first();
        $projectSiteIds = Quotation::where('quotation_status_id',$approvedQuotationStatus['id'])->pluck('project_site_id')->toArray();
        $projectIds = ProjectSite::whereIn('id',$projectSiteIds)->pluck('project_id')->toArray();
        $clientIds = Project::whereIn('id',$projectIds)->pluck('client_id')->toArray();
        $clients = Client::whereIn('id',$clientIds)->where('is_active',true)->orderBy('id','asc')->get()->toArray();
        return view('purchase/purchase-order/manage')->with(compact('clients'));
    }
    public function getCreateView(Request $request){
        try{
            $adminApprovePurchaseRequestInfo = PurchaseRequest::join('purchase_request_component_statuses','purchase_request_component_statuses.id','=','purchase_requests.purchase_component_status_id')
                                            ->where('purchase_request_component_statuses.slug','p-r-admin-approved')
                                            ->select('purchase_requests.id as id','purchase_requests.project_site_id as project_site_id','purchase_requests.created_at as created_at','purchase_requests.serial_no as serial_no')
                                            ->get()
                                            ->toArray();
            $categories = Category::where('is_miscellaneous',true)->select('id','name')->get()->toArray();
            $purchaseRequests = array();
            foreach($adminApprovePurchaseRequestInfo as $purchaseRequest){

                $purchaseRequests[$purchaseRequest['id']] = $this->getPurchaseIDFormat('purchase-request',$purchaseRequest['project_site_id'],($purchaseRequest['created_at']),$purchaseRequest['serial_no']);
            }
            return view('purchase/purchase-order/create')->with(compact('purchaseRequests','categories'));
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

    public function getListing(Request $request){
        try{
            $postdata = null;
            $po_name = "";
            $status = 0;
            $site_id = 0;
            $month = 0;
            $year = 0;
            $po_count = 0;
            $client_id = 0;
            $project_id = 0;
            $postDataArray = array();
            if ($request->has('po_name')) {
                if ($request['po_name'] != "") {
                    $po_name = $request['po_name'];
                }
            }

            if ($request->has('status')) {
                $status = $request['status'];
            }
            if($request->has('postdata')) {
                $postdata = $request['postdata'];
                if($postdata != null) {
                    $mstr = explode(",",$request['postdata']);
                    foreach($mstr as $nstr)
                    {
                        $narr = explode("=>",$nstr);
                        $narr[0] = str_replace("\x98","",$narr[0]);
                        $ytr[1] = $narr[1];
                        $postDataArray[$narr[0]] = $ytr[1];
                    }
                }
                $client_id = $postDataArray['client_id'];
                $project_id = $postDataArray['project_id'];
                $site_id = $postDataArray['site_id'];
                $month = $postDataArray['month'];
                $year = $postDataArray['year'];
                $po_count = $postDataArray['po_count'];
            }
            $purchaseOrderDetail = array();

            $ids = PurchaseOrder::all()->pluck('id');
            $filterFlag = true;

            if ($site_id != 0 && $filterFlag == true) {
                $ids = PurchaseOrder::join('purchase_requests','purchase_requests.id','=','purchase_orders.purchase_request_id')
                    ->where('purchase_requests.project_site_id',$site_id)->whereIn('purchase_orders.id',$ids)->pluck('purchase_orders.id');
                if(count($ids) <= 0) {
                    $filterFlag = false;
                }
            }

            if ($year != 0 && $filterFlag == true) {
                $ids = PurchaseOrder::whereIn('id',$ids)->whereYear('created_at', $year)->pluck('id');
                if(count($ids) <= 0) {
                    $filterFlag = false;
                }
            }

            if ($month != 0 && $filterFlag == true) {
                $ids = PurchaseOrder::whereIn('id',$ids)->whereMonth('created_at', $month)->pluck('id');
                if(count($ids) <= 0) {
                    $filterFlag = false;
                }
            }
            if ($status != 0 && $filterFlag == true) {
                if ($status == 1 ) {
                    $status = true;
                } else {
                    $status = false;
                }
                $ids = PurchaseOrder::whereIn('id',$ids)->where('is_approved', $status)->pluck('id');
                if(count($ids) <= 0) {
                    $filterFlag = false;
                }
            }

            if ($po_count != 0 && $filterFlag == true) {
                $ids = PurchaseOrder::whereIn('id',$ids)->where('serial_no', $po_count)->pluck('id');
                if(count($ids) <= 0) {
                    $filterFlag = false;
                }
            }

            if ($filterFlag) {
                $purchaseOrderDetail = PurchaseOrder::whereIn('id',$ids)->orderBy('created_at','desc')->get();
            }

            $purchaseOrderList = array();
            $iterator = 0;
            if(count($purchaseOrderDetail) > 0){
                 foreach($purchaseOrderDetail as $key => $purchaseOrder){
                     $projectSite = $purchaseOrder->purchaseRequest->projectSite;
                     $purchaseOrderList[$iterator]['purchase_order_id'] = $purchaseOrder['id'];
                     $purchaseRequest = PurchaseRequest::where('id',$purchaseOrder['purchase_request_id'])->first();
                     $purchaseOrderList[$iterator]['purchase_order_format_id'] = $this->getPurchaseIDFormat('purchase-order',$projectSite['id'],$purchaseOrder['created_at'],$purchaseOrder['serial_no']);
                     $purchaseOrderList[$iterator]['purchase_request_id'] = $purchaseOrder['purchase_request_id'];
                     $purchaseOrderList[$iterator]['purchase_request_format_id'] = $this->getPurchaseIDFormat('purchase-request',$projectSite['id'],$purchaseRequest['created_at'],$purchaseRequest['serial_no']);
                     $project = $projectSite->project;
                     $purchaseOrderList[$iterator]['client_name'] = $project->client->company;
                     $purchaseOrderList[$iterator]['site_name'] = $projectSite->name;
                     $purchaseOrderList[$iterator]['project'] = $project->name;
                     $purchaseOrderList[$iterator]['status'] = ($purchaseOrder['is_approved'] == true) ? '<span class="label label-sm label-success"> Approved </span>' : '<span class="label label-sm label-danger"> Disapproved </span>';
                     $purchaseOrderList[$iterator]['created_at'] = $purchaseOrder['created_at'];
                     $iterator++;
                 }
             }
            $iTotalRecords = count($purchaseOrderList);
            $records = array();
            $records['data'] = array();
            for($iterator = 0,$pagination = $request->start; $iterator < $request->length && $iterator < count($purchaseOrderList); $iterator++,$pagination++ ){
                $records['data'][$iterator] = [
                    $purchaseOrderList[$pagination]['purchase_order_format_id'],
                    $purchaseOrderList[$pagination]['purchase_request_format_id'],
                    $purchaseOrderList[$pagination]['client_name'],
                    $purchaseOrderList[$pagination]['project']." - ".$purchaseOrderList[$pagination]['site_name'],
                    date('d M Y',strtotime($purchaseOrderList[$pagination]['created_at'])),
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
    public function createMaterial(Request $request){
        try{
            $now = Carbon::now();
            $is_present = Material::where('name','ilike',$request->name)->pluck('id')->toArray();
            if($is_present != null){
                     $categoryMaterialData['category_id'] = $request->category;
                    $categoryMaterial = CategoryMaterialRelation::create($categoryMaterialData);
            }else{
                $materialData['name'] = ucwords(trim($request->name));
                $categoryMaterialData['category_id'] = $request->category;
                $materialData['rate_per_unit'] = round($request->rate_per_unit,3);
                $materialData['unit_id'] = $request->unit_id;
                $materialData['is_active'] = (boolean)0;
                $materialData['created_at'] = $now;
                $materialData['updated_at'] = $now;
                $materialData['hsn_code'] = $request->hsn_code;
                $material = Material::create($materialData);
                $categoryMaterialData['material_id'] = $material['id'];
                $categoryMaterial = CategoryMaterialRelation::create($categoryMaterialData);
                $materialVersionData['material_id'] = $material->id;
                $materialVersionData['rate_per_unit'] = round($request->rate_per_unit,3);
                $materialVersionData['unit_id'] = $request->unit_id;
                $materialVersion = MaterialVersion::create($materialVersionData);
            }
            $request->session()->flash('success','Material created successfully.');
            return redirect('/material/create');
        }catch(\Exception $e){
            $data = [
                'action' => 'create material',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
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
                if($purchaseOrderBill['is_amendment'] == true){
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
    public function getPurchaseOrderBillDetails(Request $request){
        try{
                $purchaseOrderBillData = PurchaseOrderBill::where('id',$request['po_id'])->first();
            $status = 200;
            return response()->json($purchaseOrderBillData,$status);
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
            $purchaseOrderComponent = PurchaseOrderComponent::findOrFail($request->purchase_order_component_id);
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
            $purchaseOrderBill['grn'] = $this->generateGRN();
            $purchaseOrderBill['created_at'] = $currentTimeStamp;
            $purchaseOrderBill['updated_at'] = $currentTimeStamp;
            $purchaseOrderBillId = PurchaseOrderBill::insertGetId($purchaseOrderBill);
            $purchaseOrderBillData = PurchaseOrderBill::where('id',$purchaseOrderBillId)->first();
            $purchaseOrderId = $purchaseOrderBillData->purchaseOrderComponent->purchaseOrder['id'];
            $inventoryComponent = InventoryComponent::where('purchase_order_component_id',$request->purchase_order_component_id)->first();
            if($inventoryComponent == null){
                $assetComponentTypeIds = MaterialRequestComponentTypes::whereIn('slug',['system-asset','new-asset'])->pluck('id')->toArray();
                $inventoryComponentData = [
                    'name' => $purchaseOrderComponent->purchaseRequestComponent->materialRequestComponent->name,
                    'purchase_order_component_id' => $purchaseOrderComponent->id,
                    'opening_stock' => 0,
                    'project_site_id' => $purchaseOrderComponent->purchaseOrder->purchaseRequest->project_site_id
                ];
                if(in_array($purchaseOrderComponent->purchaseRequestComponent->materialRequestComponent->component_type_id,$assetComponentTypeIds)){
                    $inventoryComponentData['is_material'] = false;
                    $inventoryComponentData['reference_id'] = Asset::where('name','ilike',$inventoryComponentData['name'])->pluck('id')->first();
                }else{
                    $inventoryComponentData['is_material'] = true;
                    $inventoryComponentData['reference_id'] = Material::where('name','ilike',$inventoryComponentData['name'])->pluck('id')->first();
                }
                $inventoryComponent = InventoryComponent::create($inventoryComponentData);
            }
            $transferTypeId = InventoryTransferTypes::where('slug','supplier')->where('type','ilike','IN')->pluck('id')->first();
            $inventoryComponentTransferData = [
                'inventory_component_id' => $inventoryComponent->id,
                'transfer_type_id' => $transferTypeId,
            ];
            $inventoryComponentTransferData = array_merge($inventoryComponentTransferData,$request->except('type','material','unit_name','vendor_name','purchase_order_component_id'));
            $inventoryComponentTransferData['source_name'] = $request->vendor_name;
            $this->createInventoryComponentTransfer($inventoryComponentTransferData);
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
            $request->session()->flash('success','Payment added successfully');
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

    public function changeStatus(Request $request){
        try{
            PurchaseOrderBill::where('id',$request['purchase_order_bill_id'])->update(['is_paid' => false, 'is_amendment' => false,'remark' => $request['remark']]);
            $request->session()->flash('success','Approved successfully');
            return Redirect::Back();
        }catch (\Exception $e){
            $data = [
                'action' => 'Change Status',
                'exception' => $e->getMessage(),
                'params' => $request->all()
            ];
            $request->session()->flash('danger','Something went wrong');
            return Redirect::Back();
        }
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
                $requestComponentVendors = PurchaseRequestComponentVendorRelation::where('purchase_request_component_id',$purchaseRequestComponent->id)->get();
                foreach($requestComponentVendors as $vendorRelation){
                    $purchaseRequestComponents[$iterator] = array();
                    $materialRequest = $purchaseRequestComponent->materialRequestComponent;
                    $materialRequestComponentSlug = $materialRequest->materialRequestComponentTypes->slug;
                    $purchaseRequestComponents[$iterator]['purchase_request_component_id'] = $purchaseRequestComponent->id;
                    $purchaseRequestComponents[$iterator]['name'] = $materialRequest->name;
                    $purchaseRequestComponents[$iterator]['quantity'] = $purchaseRequestComponent->materialRequestComponent->quantity;
                    $purchaseRequestComponents[$iterator]['unit_id'] = $purchaseRequestComponent->materialRequestComponent->unit_id;
                    $purchaseRequestComponents[$iterator]['vendor'] = $vendorRelation->vendor->company;
                    $purchaseRequestComponents[$iterator]['vendor_id'] = $vendorRelation->vendor_id;
                    $purchaseRequestComponents[$iterator]['material_request_component_slug'] = $materialRequestComponentSlug;
                    $materialInfo = Material::where('name','ilike',trim($purchaseRequestComponent->materialRequestComponent->name))->first();
                    if($materialInfo == null){
                        $purchaseRequestComponents[$iterator]['rate'] = '0';
                        $purchaseRequestComponents[$iterator]['hsn_code'] = '0';
                    }else{
                        $purchaseRequestComponents[$iterator]['rate'] = UnitHelper::unitConversion($materialInfo['unit_id'],$purchaseRequestComponent->materialRequestComponent->unit_id,$materialInfo['rate_per_unit']);
                        if(is_array($purchaseRequestComponents[$iterator]['rate'])){
                            return response()->json(['message' => $purchaseRequestComponents[$iterator]['rate']['message']],203);
                        }
                        $purchaseRequestComponents[$iterator]['hsn_code'] = $materialInfo['hsn_code'];
                    }
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

    public function getClientProjectName(Request $request ,$purchaseRequestId){
        try{
            $status = 200;
            $response = array();
            $purchaseRequest = PurchaseRequest::findOrFail($purchaseRequestId);
            $response['client'] = $purchaseRequest->projectSite->project->client->company;
            $response['project'] = $purchaseRequest->projectSite->project->name.' - '.$purchaseRequest->projectSite->name;
        }catch (\Exception $e){
            $data = [
                'action' => 'Get P.R. client and project name in P.O.',
                'P.R.Id' => $purchaseRequestId,
                'exception' => $e->getMessage()
            ];
            $status = 500;
            $response = null;
            Log::critical(json_encode($data));
        }
        return response()->json($response,$status);
    }

    public function createPurchaseOrder(Request $request){
        try{
            $today = date('Y-m-d');
            $assetComponentTypeIds = MaterialRequestComponentTypes::whereIn('slug',['new-material','system-asset'])->pluck('id')->toArray();
            foreach($request->purchase as $vendorId => $components){
                $approvePurchaseOrderData = $disapprovePurchaseOrderData = array('vendor_id' => $vendorId, 'purchase_request_id' => $request->purchase_request_id);
                $todaysCount = PurchaseOrder::whereDate('created_at',$today)->count();
                $approvedPurchaseOrder = $disapprovePurchaseOrder = null;
                $vendorInfo = Vendor::findOrFail($vendorId)->toArray();
                $vendorInfo['materials'] = array();
                $iterator = 0;
                foreach($components as $purchaseRequestComponentId => $component){
                    $purchaseRequestComponent = PurchaseRequestComponent::findOrFail($purchaseRequestComponentId);
                    $materialRequestComponent = $purchaseRequestComponent->materialRequestComponent;
                    $projectSiteInfo = array();
                    $projectSiteInfo['project_name'] = $materialRequestComponent->materialRequest->projectSite->project->name;
                    $projectSiteInfo['project_site_name'] = $materialRequestComponent->materialRequest->projectSite->name;
                    $projectSiteInfo['project_site_address'] = $materialRequestComponent->materialRequest->projectSite->address;
                    $pdfFlag = 'after-purchase-order-create';
                    $project_site_id = $materialRequestComponent->materialRequest->projectSite->id;
                    if($materialRequestComponent->materialRequest->projectSite->city_id == null){
                        $projectSiteInfo['project_site_city'] = '';
                    }else{
                        $projectSiteInfo['project_site_city'] = $materialRequestComponent->materialRequest->projectSite->city->name;
                    }
                    $purchaseOrderComponentData = array();
                    if($component['status'] == 'approve'){
                        if($approvedPurchaseOrder == null){
                            $approvePurchaseOrderData['is_approved'] = true;
                            $approvePurchaseOrderData['user_id'] = Auth::user()->id;
                            $approvePurchaseOrderData['serial_no'] = ++$todaysCount;
                            $approvePurchaseOrderData['format_id'] = $this->getPurchaseIDFormat('purchase-order',$project_site_id,Carbon::now(),$approvePurchaseOrderData['serial_no']);
                            $approvedPurchaseOrder = PurchaseOrder::create($approvePurchaseOrderData);
                        }
                        $vendorInfo['materials'][$iterator]['item_name'] = $materialRequestComponent->name;
                        $vendorInfo['materials'][$iterator]['quantity'] = $materialRequestComponent->quantity;
                        $vendorInfo['materials'][$iterator]['unit'] = $materialRequestComponent->unit->name;
                        if(is_array($materialRequestComponent->component_type_id,$assetComponentTypeIds)){
                            $vendorInfo['materials'][$iterator]['gst'] = '-';
                        }else{
                            $vendorInfo['materials'][$iterator]['gst'] = Material::where('name','ilike',$materialRequestComponent->name)->pluck('gst')->first();
                            if($vendorInfo['materials'][$iterator]['gst'] == null){
                                $vendorInfo['materials'][$iterator]['gst'] = '-';
                            }
                        }
                        $vendorInfo['materials'][$iterator]['hsn_code'] = $component->hsn_code;
                        $vendorInfo['materials'][$iterator]['rate'] = $component->rate;
                        $iterator++;
                        $purchaseOrderComponentData['purchase_order_id'] = $approvedPurchaseOrder['id'];
                    }elseif($component['status'] == 'disapprove'){
                        if($disapprovePurchaseOrder == null){
                            $disapprovePurchaseOrderData['is_approved'] = false;
                            $disapprovePurchaseOrderData['user_id'] = Auth::user()->id;
                            $disapprovePurchaseOrderData['serial_no'] = ++$todaysCount;
                            $disapprovePurchaseOrderData['format_id'] = $this->getPurchaseIDFormat('purchase-order',$project_site_id,Carbon::now(),$disapprovePurchaseOrderData['serial_no']);
                            $disapprovePurchaseOrder = PurchaseOrder::create($disapprovePurchaseOrderData);
                        }
                        $purchaseOrderComponentData['purchase_order_id'] = $disapprovePurchaseOrder['id'];
                    }
                    if(count($purchaseOrderComponentData) > 0){
                        $purchaseOrderComponentData['purchase_request_component_id'] = $purchaseRequestComponentId;
                        $purchaseOrderComponentData['quantity'] = $component['quantity'];
                        $purchaseOrderComponentData['rate_per_unit'] = $component['rate'];
                        $purchaseOrderComponentData['hsn_code'] = $component['hsn_code'];
                        $purchaseOrderComponentData['unit_id'] = $component['unit_id'];
                        $purchaseOrderComponent = PurchaseOrderComponent::create($purchaseOrderComponentData);
                        if(array_key_exists('vendor_quotation_images',$component) && (count($component['vendor_quotation_images']) > 0)){
                            /*move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)*/
                            $mainDirectoryName = sha1($purchaseOrderComponent['purchase_order_id']);
                            $componentDirectoryName = sha1($purchaseOrderComponent['id']);
                            $uploadPath = public_path().env('PURCHASE_ORDER_IMAGE_UPLOAD').DIRECTORY_SEPARATOR.$mainDirectoryName.DIRECTORY_SEPARATOR.'vendor_quotation_images'.DIRECTORY_SEPARATOR.$componentDirectoryName;
                            if (!file_exists($uploadPath)) {
                                File::makeDirectory($uploadPath, $mode = 0777, true, true);
                            }
                            $iterator = 0;
                            foreach($component['vendor_quotation_images'] as $key => $image){
                                $imageData = [
                                    'purchase_order_component_id' => $purchaseOrderComponent['id'] ,
                                    'name' => $_FILES['purchase']['name'][$vendorId][$purchaseRequestComponentId]['vendor_quotation_images'][$iterator],
                                    'caption' => 'No caption added.'
                                ];
                                $imageUploadPath = $uploadPath.DIRECTORY_SEPARATOR.$imageData['name'];
                                move_uploaded_file($_FILES['purchase']['tmp_name'][$vendorId][$purchaseRequestComponentId]['vendor_quotation_images'][$iterator],$imageUploadPath);
                                PurchaseOrderComponentImage::create($imageData);
                            }
                        }
                        if(array_key_exists('client_approval_images',$component) && (count($component['client_approval_images']) > 0)){
                            $mainDirectoryName = sha1($purchaseOrderComponent['purchase_order_id']);
                            $componentDirectoryName = sha1($purchaseOrderComponent['id']);
                            $uploadPath = public_path().env('PURCHASE_ORDER_IMAGE_UPLOAD').DIRECTORY_SEPARATOR.$mainDirectoryName.DIRECTORY_SEPARATOR.'client_approval_images'.DIRECTORY_SEPARATOR.$componentDirectoryName;
                            if (!file_exists($uploadPath)) {
                                File::makeDirectory($uploadPath, $mode = 0777, true, true);
                            }
                            $iterator = 0;
                            foreach($component['client_approval_images'] as $key => $image){
                                $imageData = [
                                    'purchase_order_component_id' => $purchaseOrderComponent['id'] ,
                                    'name' => $_FILES['purchase']['name'][$vendorId][$purchaseRequestComponentId]['client_approval_images'][$iterator],
                                    'caption' => 'No caption added',
                                    'is_vendor_approval' => false
                                ];
                                $imageUploadPath = $uploadPath.DIRECTORY_SEPARATOR.$imageData['name'];
                                move_uploaded_file($_FILES['purchase']['tmp_name'][$vendorId][$purchaseRequestComponentId]['client_approval_images'][$iterator],$imageUploadPath);
                                PurchaseOrderComponentImage::create($imageData);
                            }
                        }

                    }
                }
                if(count($vendorInfo['materials']) > 0){
                    $pdf = App::make('dompdf.wrapper');
                    $pdf->loadHTML(view('purchase.purchase-request.pdf.vendor-quotation')->with(compact('vendorInfo','projectSiteInfo','pdfFlag')));
                    $pdfDirectoryPath = env('PURCHASE_VENDOR_ASSIGNMENT_PDF_FOLDER');
                    $pdfFileName = sha1($vendorId).'.pdf';
                    $pdfUploadPath = public_path().$pdfDirectoryPath.'/'.$pdfFileName;
                    $pdfContent = $pdf->stream();
                    if(file_exists($pdfUploadPath)){
                        unlink($pdfUploadPath);
                    }
                    if (!file_exists($pdfDirectoryPath)) {
                        File::makeDirectory(public_path().$pdfDirectoryPath, $mode = 0777, true, true);
                    }
                    file_put_contents($pdfUploadPath,$pdfContent);
                    $mailData = ['path' => $pdfUploadPath, 'toMail' => $vendorInfo['email']];
                    Mail::send('purchase.purchase-request.email.vendor-quotation', [], function($message) use ($mailData){
                        $message->subject('Testing with attachment');
                        $message->to($mailData['toMail']);
                        $message->from(env('MAIL_USERNAME'));
                        $message->attach($mailData['path']);
                    });
                    unlink($pdfUploadPath);
                }
            }
            $request->session()->flash('success','Purchase Order created successfully');
            return redirect('/purchase/purchase-order/create');
        }catch (\Exception $e){
            $data = [
                'action' => 'Create Purchase Order',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }
    public function createAsset(Request $request){
        try{
            $is_present = Asset::where('name','ilike',$request->name)->pluck('id')->toArray();
            if($is_present == null){
                $asset_type = AssetType::where('slug','other')->pluck('id')->first();
                $categoryAssetData['asset_types_id'] = $asset_type;
                $categoryAssetData['name'] = $request->name;
                $categoryAssetData['quantity'] = 1;
                $query = Asset::create($categoryAssetData);
            }
            $request->session()->flash('success','New asset created successfully');
            return redirect('/purchase/purchase-order/create');

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
}
