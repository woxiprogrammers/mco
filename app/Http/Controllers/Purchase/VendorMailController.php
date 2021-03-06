<?php

namespace App\Http\Controllers\Purchase;

use App\Client;
use App\Helper\MaterialProductHelper;
use App\Material;
use App\MaterialRequestComponents;
use App\MaterialRequestComponentTypes;
use App\PurchaseOrder;
use App\PurchaseRequest;
use App\PurchaseRequestComponentVendorMailInfo;
use App\PurchaseRequestComponentVendorRelation;
use App\Unit;
use App\Vendor;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class VendorMailController extends Controller
{
    public function __construct()
    {
        $this->middleware('custom.auth');
    }

    public function getManageView(Request $request){
        try{
            return view('purchase.vendor-email.manage');
        }catch (\Exception $e){
            $data = [
                'action' => 'Get Vendor Email manage page',
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function listing(Request $request){
        try {
            $skip = $request->start;
            $take = $request->length;
            $status = 200;
            $vendor_name = null;
            $statusId = null;
            $vendorMailData = array();
            $totalRecordCount = 0;
            if ($request->has('vendor_name')) {
                if ($request['vendor_name'] != "") {
                    $vendor_name = $request['vendor_name'];
                }
            }

            if ($request->has('status_id')) {
                if ($request['status_id'] != "") {
                    $statusId = $request['status_id'];
                }
            }

            $ids = PurchaseRequestComponentVendorMailInfo::all()->pluck('id');
            $filterFlag = true;
            if ($vendor_name != null && $filterFlag == true) {
                $idsVendor = PurchaseRequestComponentVendorMailInfo::join('vendors','purchase_request_component_vendor_mail_info.vendor_id','=','vendors.id')
                    ->where('vendors.company','ilike', '%'.$vendor_name.'%')
                    ->whereIn('purchase_request_component_vendor_mail_info.id',$ids)
                    ->pluck('purchase_request_component_vendor_mail_info.id')->toArray();
                $idsClients = PurchaseRequestComponentVendorMailInfo::join('clients','purchase_request_component_vendor_mail_info.vendor_id','=','clients.id')
                    ->where('clients.company','ilike', '%'.$vendor_name.'%')
                    ->whereIn('purchase_request_component_vendor_mail_info.id',$ids)
                    ->pluck('purchase_request_component_vendor_mail_info.id')->toArray();
                $ids = array_merge($idsVendor, $idsClients);
                if(count($ids) <= 0) {
                    $filterFlag = false;
                }
            }

            if (($statusId != null && $statusId != "all") && $filterFlag == true) {
                $ids = PurchaseRequestComponentVendorMailInfo::where('type_slug',$statusId)
                    ->whereIn('id',$ids)->pluck('id')->toArray();
                if(count($ids) <= 0) {
                    $filterFlag = false;
                }
            }

            if ($filterFlag) {
                $totalRecordCount = PurchaseRequestComponentVendorMailInfo::
                                    whereIn('id',$ids)->count();
                $vendorMailData = PurchaseRequestComponentVendorMailInfo::whereIn('id',$ids)
                                ->orderBy('created_at','desc')
                                ->skip($skip)->take($take)
                                ->get();
            }

            $iTotalRecords = count($vendorMailData);
            if($request->length == -1){
                $length = count($vendorMailData);
            }else{
                $length = $request->length;
            }
            $records = [
                'data' => array(),
                'draw' => intval($request->draw),
                'recordsTotal' => $totalRecordCount,
                'recordsFiltered' => $totalRecordCount
            ];
            for($iterator = 0,$pagination = 0; $iterator < $length && $pagination < count($vendorMailData); $iterator++,$pagination++ ){
                switch($vendorMailData[$pagination]['type_slug']){
                    case 'for-quotation':
                        $slug = 'For Quotation';
                        $action = '<div id="sample_editable_1_new" class="btn btn-small blue">
                                    <a href="/purchase/vendor-mail/pdf/'.$vendorMailData[$pagination]['id'].'/'.$vendorMailData[$pagination]['type_slug'].'" style="color: white">
                                        PDF <i class="fa fa-download" aria-hidden="true"></i>
                                    </a>
                                </div>';
                        break;

                    case 'for-purchase-order':
                        $slug = 'For Purchase Order';
                        $action = '<div id="sample_editable_1_new" class="btn btn-small blue">
                                    <a href="/purchase/vendor-mail/pdf/'.$vendorMailData[$pagination]['id'].'/'.$vendorMailData[$pagination]['type_slug'].'" style="color: white">
                                        PDF <i class="fa fa-download" aria-hidden="true"></i>
                                    </a>
                                </div>';
                        break;

                    case 'disapprove-purchase-order':
                        $slug = 'For Disapproved Purchase Order';
                        $action = '-';
                        break;

                    default:
                        $slug = '';
                        $action = '-';
                }

                if($vendorMailData[$pagination]['is_client'] == true){
                    if($vendorMailData[$pagination]['client_id'] == null){
                        $name = '';
                    }else{
                        $name = $vendorMailData[$pagination]->client->company;
                    }
                }else{
                    if($vendorMailData[$pagination]['vendor_id'] == null){
                        $name = '';
                    }else{
                        $name = $vendorMailData[$pagination]->vendor->company;
                    }
                }
                $createdAt = date('d M Y h:i:s',strtotime($vendorMailData[$pagination]['created_at']));
                $records['data'][] = [
                    $vendorMailData[$pagination]['id'],
                    $name,
                    $slug,
                    $createdAt,
                    $action
                ];
            }
        }catch (\Exception $e){
            $data = [
                'action' => 'Get Vendor Email listing',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            $status = 500;
            $records = [];
        }
        return response()->json($records,$status);
    }

    public function getPDF(Request $request,$purchaseRequestComponentVendorMailId,$slug){
        try{
            $purchaseRequestComponentVendorMailInfo = PurchaseRequestComponentVendorMailInfo::where('id',$purchaseRequestComponentVendorMailId)->first();
            if($slug == 'for-quotation'){
                $pdfTitle = 'Purchase Request';
                $purchaseRequest = PurchaseRequest::where('id',$purchaseRequestComponentVendorMailInfo['reference_id'])->first();
                $formatId = $purchaseRequest->format_id;
                $materialRequestComponents = MaterialRequestComponents::join('purchase_request_components','purchase_request_components.material_request_component_id','=','material_request_components.id')
                                                ->join('purchase_request_component_vendor_relation','purchase_request_component_vendor_relation.purchase_request_component_id','=','purchase_request_components.id')
                                                ->where('purchase_request_component_vendor_relation.vendor_id',$purchaseRequestComponentVendorMailInfo['vendor_id'])
                                                ->where('purchase_request_components.purchase_request_id',$purchaseRequest['id'])
                                                ->select('material_request_components.material_request_id','material_request_components.id','material_request_components.name','material_request_components.quantity','material_request_components.unit_id')
                                                ->get();
                $iterator = 0;
                if($purchaseRequestComponentVendorMailInfo->is_client == true){
                    $vendorInfo = Client::findOrFail($purchaseRequestComponentVendorMailInfo->client_id)->toArray();
                }else{
                    $vendorInfo = Vendor::findOrFail($purchaseRequestComponentVendorMailInfo->vendor_id)->toArray();
                }
                $vendorInfo['materials'] = array();
                $projectSiteInfo = array();
                $projectSiteInfo['project_name'] = $purchaseRequest->projectSite->project->name;
                $projectSiteInfo['project_site_name'] = $purchaseRequest->projectSite->name;
                $projectSiteInfo['project_site_address'] = $purchaseRequest->projectSite->address;
                if($purchaseRequest->projectSite->city_id == null){
                    $projectSiteInfo['project_site_city'] = '';
                }else{
                    $projectSiteInfo['project_site_city'] = $purchaseRequest->projectSite->city->name;
                }
                $projectSiteInfo['delivery_address'] = $projectSiteInfo['project_name'].', '.$projectSiteInfo['project_site_name'].', '.$projectSiteInfo['project_site_address'].', '.$projectSiteInfo['project_site_city'];
                foreach($materialRequestComponents as $key => $materialRequestComponent){
                    $vendorInfo['materials'][$iterator]['item_name'] = $materialRequestComponent->name;
                    $vendorInfo['materials'][$iterator]['quantity'] = $materialRequestComponent->quantity;
                    $vendorInfo['materials'][$iterator]['unit'] = $materialRequestComponent->unit->name;
                    $iterator++;
                }
                $pdf = App::make('dompdf.wrapper');
                $pdf->loadHTML(view('purchase.purchase-request.pdf.vendor-quotation')->with(compact('vendorInfo','projectSiteInfo','pdfTitle','formatId')));
                return $pdf->stream();
            }elseif($slug == 'for-purchase-order'){
                $pdfTitle = 'Purchase Order';
                $pdfFlag = 'after-purchase-order-create';
                $purchaseOrder = PurchaseOrder::where('id',$purchaseRequestComponentVendorMailInfo['reference_id'])->first();
                $formatId = $purchaseOrder->format_id;
                if($purchaseOrder != null){
                    if($purchaseOrder->is_client_order == true){
                        $vendorInfo = Client::findOrFail($purchaseOrder->client_id)->toArray();
                    }else{
                        $vendorInfo = Vendor::findOrFail($purchaseOrder->vendor_id)->toArray();
                    }
                    $vendorInfo['materials'] = array();
                    $iterator = 0;
                    $projectSiteInfo = array();
                    $projectSiteInfo['project_site_address'] = $purchaseOrder->purchaseRequest->projectSite->address;
                    if($purchaseOrder->purchaseOrderRequest->delivery_address != null){
                        $projectSiteInfo['delivery_address'] = $purchaseOrder->purchaseOrderRequest->delivery_address;
                    }else{
                        $projectSiteInfo['project_name'] = $purchaseOrder->purchaseRequest->projectSite->project->name;
                        $projectSiteInfo['project_site_name'] = $purchaseOrder->purchaseRequest->projectSite->name;

                        if($purchaseOrder->purchaseRequest->projectSite->city_id == null){
                            $projectSiteInfo['project_site_city'] = '';
                        }else{
                            $projectSiteInfo['project_site_city'] = $purchaseOrder->purchaseRequest->projectSite->city->name;
                        }
                        $projectSiteInfo['delivery_address'] = $projectSiteInfo['project_name'].', '.$projectSiteInfo['project_site_name'].', '.$projectSiteInfo['project_site_address'].', '.$projectSiteInfo['project_site_city'];
                    }

                    $projectSiteInfo['por_remarks'] = ($purchaseOrder->purchaseOrderRequest->por_remarks !=null && $purchaseOrder->purchaseOrderRequest->por_remarks != "")? $purchaseOrder->purchaseOrderRequest->por_remarks : "-";

                    foreach($purchaseOrder->purchaseOrderComponent as $purchaseOrderComponent){
                        $vendorInfo['materials'][$iterator] = array();
                        $vendorInfo['materials'][$iterator]['item_name'] = $purchaseOrderComponent->purchaseRequestComponent->materialRequestComponent->name;
                        $vendorInfo['materials'][$iterator]['quantity'] = $purchaseOrderComponent['quantity'];
                        $vendorInfo['materials'][$iterator]['unit'] = Unit::where('id',$purchaseOrderComponent['unit_id'])->pluck('name')->first();
                        $vendorInfo['materials'][$iterator]['rate'] = $purchaseOrderComponent['rate_per_unit'];
                        $vendorInfo['materials'][$iterator]['subtotal'] = round(($purchaseOrderComponent['quantity'] * $purchaseOrderComponent['rate_per_unit']),3);
                        if($purchaseOrderComponent['cgst_percentage'] == null || $purchaseOrderComponent['cgst_percentage'] == ''){
                            $vendorInfo['materials'][$iterator]['cgst_percentage'] = 0;
                        }else{
                            $vendorInfo['materials'][$iterator]['cgst_percentage'] = $purchaseOrderComponent['cgst_percentage'];
                        }
                        $vendorInfo['materials'][$iterator]['cgst_amount'] = round(($vendorInfo['materials'][$iterator]['subtotal'] * ($vendorInfo['materials'][$iterator]['cgst_percentage']/100)),3);
                        if($purchaseOrderComponent['sgst_percentage'] == null || $purchaseOrderComponent['sgst_percentage'] == ''){
                            $vendorInfo['materials'][$iterator]['sgst_percentage'] = 0;
                        }else{
                            $vendorInfo['materials'][$iterator]['sgst_percentage'] = $purchaseOrderComponent['sgst_percentage'];
                        }
                        $vendorInfo['materials'][$iterator]['sgst_amount'] = round(($vendorInfo['materials'][$iterator]['subtotal'] * ($vendorInfo['materials'][$iterator]['sgst_percentage']/100)),3);
                        if($purchaseOrderComponent['igst_percentage'] == null || $purchaseOrderComponent['igst_percentage'] == ''){
                            $vendorInfo['materials'][$iterator]['igst_percentage'] = 0;
                        }else{
                            $vendorInfo['materials'][$iterator]['igst_percentage'] = $purchaseOrderComponent['igst_percentage'];
                        }
                        $vendorInfo['materials'][$iterator]['igst_amount'] = round(($vendorInfo['materials'][$iterator]['subtotal'] * ($vendorInfo['materials'][$iterator]['igst_percentage']/100)),3);
                        $vendorInfo['materials'][$iterator]['total'] = round(($vendorInfo['materials'][$iterator]['subtotal'] + $vendorInfo['materials'][$iterator]['cgst_amount'] + $vendorInfo['materials'][$iterator]['sgst_amount'] + $vendorInfo['materials'][$iterator]['igst_amount']),3);
                        if($purchaseOrderComponent['expected_delivery_date'] == null || $purchaseOrderComponent['expected_delivery_date'] == ''){
                            $vendorInfo['materials'][$iterator]['due_date'] = '';
                        }else{
                            $vendorInfo['materials'][$iterator]['due_date'] = 'Due on '.date('j/n/Y',strtotime($purchaseOrderComponent['expected_delivery_date']));
                        }
                        $purchaseOrderRequestComponent = $purchaseOrderComponent->purchaseOrderRequestComponent;
                        if($purchaseOrderRequestComponent['transportation_amount'] == null || $purchaseOrderRequestComponent['transportation_amount'] == ''){
                            $vendorInfo['materials'][$iterator]['transportation_amount'] = 0;
                        }else{
                            $vendorInfo['materials'][$iterator]['transportation_amount'] = $purchaseOrderRequestComponent['transportation_amount'];
                        }
                        if($purchaseOrderRequestComponent['transportation_cgst_percentage'] == null || $purchaseOrderRequestComponent['transportation_cgst_percentage'] == ''){
                            $vendorInfo['materials'][$iterator]['transportation_cgst_percentage'] = 0;
                        }else{
                            $vendorInfo['materials'][$iterator]['transportation_cgst_percentage'] = $purchaseOrderRequestComponent['transportation_cgst_percentage'];
                        }
                        if($purchaseOrderRequestComponent['transportation_sgst_percentage'] == null || $purchaseOrderRequestComponent['transportation_sgst_percentage'] == ''){
                            $vendorInfo['materials'][$iterator]['transportation_sgst_percentage'] = 0;
                        }else{
                            $vendorInfo['materials'][$iterator]['transportation_sgst_percentage'] = $purchaseOrderRequestComponent['transportation_sgst_percentage'];
                        }
                        if($purchaseOrderRequestComponent['transportation_igst_percentage'] == null || $purchaseOrderRequestComponent['transportation_igst_percentage'] == ''){
                            $vendorInfo['materials'][$iterator]['transportation_igst_percentage'] = 0;
                        }else{
                            $vendorInfo['materials'][$iterator]['transportation_igst_percentage'] = $purchaseOrderRequestComponent['transportation_igst_percentage'];
                        }
                        $vendorInfo['materials'][$iterator]['transportation_cgst_amount'] = round((($vendorInfo['materials'][$iterator]['transportation_cgst_percentage'] * $vendorInfo['materials'][$iterator]['transportation_amount']) / 100 ),3);
                        $vendorInfo['materials'][$iterator]['transportation_sgst_amount'] = round((($vendorInfo['materials'][$iterator]['transportation_sgst_percentage'] * $vendorInfo['materials'][$iterator]['transportation_amount']) / 100 ),3);
                        $vendorInfo['materials'][$iterator]['transportation_igst_amount'] = round((($vendorInfo['materials'][$iterator]['transportation_igst_percentage'] * $vendorInfo['materials'][$iterator]['transportation_amount']) / 100 ),3);
                        $vendorInfo['materials'][$iterator]['transportation_total_amount'] = round(($vendorInfo['materials'][$iterator]['transportation_amount'] + $vendorInfo['materials'][$iterator]['transportation_cgst_amount'] + $vendorInfo['materials'][$iterator]['transportation_sgst_amount'] + $vendorInfo['materials'][$iterator]['transportation_igst_amount']),3);
                        $iterator++;
                    }
                    $pdf = App::make('dompdf.wrapper');
                    $pdf->loadHTML(view('purchase.purchase-request.pdf.vendor-quotation')->with(compact('vendorInfo','projectSiteInfo','pdfFlag','pdfTitle','formatId')));
                    return $pdf->stream();
                }else{

                }

            }
        }catch(\Exception $e){
            $data = [
                'action' => 'Get Vendor Email listing PDF',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            $status = 500;
        }
    }
}
