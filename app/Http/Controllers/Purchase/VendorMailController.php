<?php

namespace App\Http\Controllers\Purchase;

use App\MaterialRequestComponents;
use App\PurchaseOrder;
use App\PurchaseRequest;
use App\PurchaseRequestComponentVendorMailInfo;
use App\PurchaseRequestComponentVendorRelation;
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
                foreach($materialRequestComponents as $key => $materialRequestComponent){
                    $projectSite = $materialRequestComponent->materialRequest->projectSite;
                    $projectSiteInfo['project_name'] = $projectSite->project->name;
                    $projectSiteInfo['project_site_name'] = $projectSite->name;
                    $projectSiteInfo['project_site_address'] = $projectSite->address;
                    if($projectSite->city_id == null){
                        $projectSiteInfo['project_site_city'] = '';
                    }else{
                        $projectSiteInfo['project_site_city'] = $projectSite->city->name;
                    }
                    $vendorInfo['materials'][$iterator]['item_name'] = $materialRequestComponent->name;
                    $vendorInfo['materials'][$iterator]['quantity'] = $materialRequestComponent->quantity;
                    $vendorInfo['materials'][$iterator]['unit'] = $materialRequestComponent->unit->name;
                    $iterator++;
                }
                $pdf = App::make('dompdf.wrapper');
                $pdf->loadHTML(view('purchase.purchase-request.pdf.vendor-quotation')->with(compact('vendorInfo','projectSiteInfo')));
                return $pdf->stream();
            }elseif($slug == 'for-purchase-order'){
                $pdfSlug = 'after-purchase-order-create';
                $vendorInfo = array();
                $purchaseOrder = PurchaseOrder::where('id',$purchaseRequestComponentVendorMailInfo['reference_id'])->first();
                $materialRequestComponents = MaterialRequestComponents::join('purchase_request_components','purchase_request_components.material_request_component_id','=','material_request_components.id')
                                                ->join('purchase_request_component_vendor_relation','purchase_request_component_vendor_relation.purchase_request_component_id','=','purchase_request_components.id')
                                                ->join('purchase_requests','purchase_requests.id','=','purchase_request_components.purchase_request_id')
                                                ->join('purchase_orders','purchase_orders.purchase_request_id','=','purchase_requests.id')
                                                ->where('purchase_request_component_vendor_relation.vendor_id',$purchaseRequestComponentVendorMailInfo['vendor_id'])
                                                ->where('purchase_orders.id',$purchaseOrder['id'])
                                                ->select('material_request_components.material_request_id','material_request_components.id','material_request_components.name','material_request_components.quantity','material_request_components.unit_id')
                                                ->get();
                $pdf = App::make('dompdf.wrapper');
                $pdf->loadHTML(view('purchase.purchase-request.pdf.vendor-quotation')->with(compact('vendorInfo','projectSiteInfo','pdfFlag')));
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
