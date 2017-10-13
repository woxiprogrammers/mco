<?php

namespace App\Http\Controllers\Purchase;

use App\Helper\UnitHelper;
use App\Http\Controllers\CustomTraits\Purchase\MaterialRequestTrait;
use App\Material;
use App\PurchaseOrder;
use App\PurchaseOrderComponent;
use App\PurchaseOrderComponentImage;
use App\PurchaseRequest;
use App\PurchaseRequestComponent;
use App\PurchaseRequestComponentStatuses;
use App\PurchaseRequestComponentVendorRelation;
use App\Unit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
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
                $requestComponentVendors = PurchaseRequestComponentVendorRelation::where('purchase_request_component_id',$purchaseRequestComponent->id)->get();
                foreach($requestComponentVendors as $vendorRelation){
                    $purchaseRequestComponents[$iterator] = array();
                    $purchaseRequestComponents[$iterator]['purchase_request_component_id'] = $purchaseRequestComponent->id;
                    $purchaseRequestComponents[$iterator]['name'] = $purchaseRequestComponent->materialRequestComponent->name;
                    $purchaseRequestComponents[$iterator]['quantity'] = $purchaseRequestComponent->materialRequestComponent->quantity;
                    $purchaseRequestComponents[$iterator]['unit_id'] = $purchaseRequestComponent->materialRequestComponent->unit_id;
                    $purchaseRequestComponents[$iterator]['vendor'] = $vendorRelation->vendor->company;
                    $purchaseRequestComponents[$iterator]['vendor_id'] = $vendorRelation->vendor_id;
                    $materialInfo = Material::where('name','ilike',trim($purchaseRequestComponent->materialRequestComponent->name))->first();
                    if($materialInfo == null){
                        $purchaseRequestComponents[$iterator]['rate'] = '0';
                        $purchaseRequestComponents[$iterator]['hsn_code'] = '0';
                    }else{
                        $purchaseRequestComponents[$iterator]['rate'] = UnitHelper::unitConversion($materialInfo['unit_id'],$purchaseRequestComponent->materialRequestComponent->unit_id,$materialInfo['rate_per_unit']);
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
            $today = Carbon::today();
            foreach($request->purchase as $vendorId => $components){
                $approvePurchaseOrderData = $disapprovePurchaseOrderData = array('vendor_id' => $vendorId, 'purchase_request_id' => $request->purchase_request_id);
                $todaysCount = PurchaseOrder::where('created_at','>=',$today)->count();
                $approvedPurchaseOrder = $disapprovePurchaseOrder = null;
                foreach($components as $purchaseRequestComponentId => $component){
                    $purchaseOrderComponentData = array();
                    if($component['status'] == 'approve'){
                        if($approvedPurchaseOrder == null){
                            $approvePurchaseOrderData['is_approved'] = true;
                            $approvePurchaseOrderData['user_id'] = Auth::user()->id;
                            $approvePurchaseOrderData['serial_no'] = ++$todaysCount;
                            $approvedPurchaseOrder = PurchaseOrder::create($approvePurchaseOrderData);
                        }
                        $purchaseOrderComponentData['purchase_order_id'] = $approvedPurchaseOrder['id'];
                    }elseif($component['status'] == 'disapprove'){
                        if($disapprovePurchaseOrder == null){
                            $disapprovePurchaseOrderData['is_approved'] = false;
                            $disapprovePurchaseOrderData['user_id'] = Auth::user()->id;
                            $disapprovePurchaseOrderData['serial_no'] = ++$todaysCount;
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
                $request->session()->flash('success','Purchase Order created successfully');
                return redirect('/purchase/purchase-order/create');
            }
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
}
