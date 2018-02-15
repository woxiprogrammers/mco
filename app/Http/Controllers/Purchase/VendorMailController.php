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
        try{
            $status = 200;
            $vendorMailData = PurchaseRequestComponentVendorMailInfo::orderBy('created_at','desc')->get();
            $iTotalRecords = count($vendorMailData);
            if($request->length == -1){
                $length = count($vendorMailData);
            }else{
                $length = $request->length;
            }
            $records = [
                'data' => array(),
                'draw' => intval($request->draw),
                'recordsTotal' => $iTotalRecords,
                'recordsFiltered' => $iTotalRecords
            ];
            for($iterator = 0,$pagination = $request->start; $iterator < $length && $iterator < count($vendorMailData); $iterator++,$pagination++ ){
                switch($vendorMailData[$pagination]['type_slug']){
                    case 'for-quotation':
                        $slug = 'For Quotation';
                        break;

                    case 'for-purchase-order':
                        $slug = 'For Purchase Order';
                        break;

                    default:
                        $slug = '';
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
                $records['data'][] = [
                    ($pagination+1),
                    $name,
                    $slug,
                    '<div id="sample_editable_1_new" class="btn btn-small blue">
                                            <a href="/purchase/vendor-mail/pdf/'.$vendorMailData[$pagination]['id'].'/'.$vendorMailData[$pagination]['type_slug'].'" style="color: white">
                                                PDF <i class="fa fa-download" aria-hidden="true"></i>
                                            </a>
                                        </div>'
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
                $vendorInfo = array();
                $purchaseRequest = PurchaseRequest::where('id',$purchaseRequestComponentVendorMailInfo['reference_id'])->first();
                $materialRequestComponents = MaterialRequestComponents::join('purchase_request_components','purchase_request_components.material_request_component_id','=','material_request_components.id')
                                                ->join('purchase_request_component_vendor_relation','purchase_request_component_vendor_relation.purchase_request_component_id','=','purchase_request_components.id')
                                                ->where('purchase_request_component_vendor_relation.vendor_id',$purchaseRequestComponentVendorMailInfo['vendor_id'])
                                                ->where('purchase_request_components.purchase_request_id',$purchaseRequest['id'])
                                                ->select('material_request_components.material_request_id','material_request_components.id','material_request_components.name','material_request_components.quantity','material_request_components.unit_id')
                                                ->get();
                $iterator = 0;
                $client = $purchaseRequest->projectSite->project->client;
                $vendorInfo['company'] = $client['company'];
                $vendorInfo['mobile'] = $client['mobile'];
                $vendorInfo['email'] = $client['email'];
                $vendorInfo['gstin'] = $client['gstin'];
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
                foreach($materialRequestComponents as $key => $materialRequestComponent){
                    $vendorInfo['materials'][$iterator]['item_name'] = $materialRequestComponent->name;
                    $vendorInfo['materials'][$iterator]['quantity'] = $materialRequestComponent->quantity;
                    $vendorInfo['materials'][$iterator]['unit'] = $materialRequestComponent->unit->name;
                    $iterator++;
                }
                $pdf = App::make('dompdf.wrapper');
                $pdf->loadHTML(view('purchase.purchase-request.pdf.vendor-quotation')->with(compact('vendorInfo','projectSiteInfo','pdfTitle')));
                return $pdf->stream();
            }elseif($slug == 'for-purchase-order'){
                $pdfTitle = 'Purchase Order';
                $pdfFlag = 'after-purchase-order-create';
                $purchaseOrder = PurchaseOrder::where('id',$purchaseRequestComponentVendorMailInfo['reference_id'])->first();
                if($purchaseOrder != null){
                    if($purchaseOrder->is_client_order == true){
                        $vendorInfo = Client::findOrFail($purchaseOrder->client_id)->toArray();
                    }else{
                        $vendorInfo = Vendor::findOrFail($purchaseOrder->vendor_id)->toArray();
                    }
                    $vendorInfo['materials'] = array();
                    $iterator = 0;
                    $projectSiteInfo = array();
                    $projectSiteInfo['project_name'] = $purchaseOrder->purchaseRequest->projectSite->project->name;
                    $projectSiteInfo['project_site_name'] = $purchaseOrder->purchaseRequest->projectSite->name;
                    $projectSiteInfo['project_site_address'] = $purchaseOrder->purchaseRequest->projectSite->address;
                    if($purchaseOrder->purchaseRequest->projectSite->city_id == null){
                        $projectSiteInfo['project_site_city'] = '';
                    }else{
                        $projectSiteInfo['project_site_city'] = $purchaseOrder->purchaseRequest->projectSite->city->name;
                    }
                    foreach($purchaseOrder->purchaseOrderComponent as $purchaseOrderComponent){
                        $vendorInfo['materials'][$iterator] = array();
                        $vendorInfo['materials'][$iterator]['item_name'] = $purchaseOrderComponent->purchaseRequestComponent->materialRequestComponent->name;
                        $vendorInfo['materials'][$iterator]['quantity'] = $purchaseOrderComponent['quantity'];
                        $vendorInfo['materials'][$iterator]['unit'] = Unit::where('id',$purchaseOrderComponent['unit_id'])->pluck('name')->first();
                        $vendorInfo['materials'][$iterator]['rate'] = $purchaseOrderComponent['rate_per_unit'];
                        $vendorInfo['materials'][$iterator]['subtotal'] = MaterialProductHelper::customRound(($purchaseOrderComponent['quantity'] * $purchaseOrderComponent['rate_per_unit']));
                        if($purchaseOrderComponent['cgst_percentage'] == null || $purchaseOrderComponent['cgst_percentage'] == ''){
                            $vendorInfo['materials'][$iterator]['cgst_percentage'] = 0;
                        }else{
                            $vendorInfo['materials'][$iterator]['cgst_percentage'] = $purchaseOrderComponent['cgst_percentage'];
                        }
                        $vendorInfo['materials'][$iterator]['cgst_amount'] = $vendorInfo['materials'][$iterator]['subtotal'] * ($vendorInfo['materials'][$iterator]['cgst_percentage']/100);
                        if($purchaseOrderComponent['sgst_percentage'] == null || $purchaseOrderComponent['sgst_percentage'] == ''){
                            $vendorInfo['materials'][$iterator]['sgst_percentage'] = 0;
                        }else{
                            $vendorInfo['materials'][$iterator]['sgst_percentage'] = $purchaseOrderComponent['sgst_percentage'];
                        }
                        $vendorInfo['materials'][$iterator]['sgst_amount'] = $vendorInfo['materials'][$iterator]['subtotal'] * ($vendorInfo['materials'][$iterator]['sgst_percentage']/100);
                        if($purchaseOrderComponent['igst_percentage'] == null || $purchaseOrderComponent['igst_percentage'] == ''){
                            $vendorInfo['materials'][$iterator]['igst_percentage'] = 0;
                        }else{
                            $vendorInfo['materials'][$iterator]['igst_percentage'] = $purchaseOrderComponent['igst_percentage'];
                        }
                        $vendorInfo['materials'][$iterator]['igst_amount'] = $vendorInfo['materials'][$iterator]['subtotal'] * ($vendorInfo['materials'][$iterator]['igst_percentage']/100);
                        $vendorInfo['materials'][$iterator]['total'] = $vendorInfo['materials'][$iterator]['subtotal'] + $vendorInfo['materials'][$iterator]['cgst_amount'] + $vendorInfo['materials'][$iterator]['sgst_amount'] + $vendorInfo['materials'][$iterator]['igst_amount'];
                        if($purchaseOrderComponent['expected_delivery_date'] == null || $purchaseOrderComponent['expected_delivery_date'] == ''){
                            $vendorInfo['materials'][$iterator]['due_date'] = '';
                        }else{
                            $vendorInfo['materials'][$iterator]['due_date'] = 'Due on '.date('j/n/Y',strtotime($purchaseOrderComponent['expected_delivery_date']));
                        }
                        $iterator++;
                    }
                    $pdf = App::make('dompdf.wrapper');
                    $pdf->loadHTML(view('purchase.purchase-request.pdf.vendor-quotation')->with(compact('vendorInfo','projectSiteInfo','pdfFlag','pdfTitle')));
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
