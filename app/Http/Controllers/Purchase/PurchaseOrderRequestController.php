<?php

namespace App\Http\Controllers\Purchase;

use App\PurchaseOrderRequest;
use App\PurchaseRequest;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class PurchaseOrderRequestController extends Controller
{
    public function __construct(){
        $this->middleware('custom.auth');
    }

    public function getManageView(Request $request){
        try{
            return view('purchase.purchase-order-request.manage');
        }catch(\Exception $e){
            $data = [
                'action' => 'Get purchase order request manage view',
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getCreateView(Request $request){
        try{
            return view('purchase.purchase-order-request.create');
        }catch(\Exception $e){
            $data = [
                'action' => 'Get purchase order request create view',
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function createPurchaseOrderRequest(Request $request){
        try{
            return redirect('/purchase/purchase-order-request/manage');
        }catch(\Exception $e){
            $data = [
                'action' => 'Create purchase order request',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function listing(Request $request){
        try{
            if(Session::has('global_project_site')){
                $projectSiteId = Session::get('global_project_site');
                $purchaseOrderRequestsData = PurchaseOrderRequest::join('purchase_requests','purchase_requests.id','=','purchase_order_requests.purchase_request_id')
                                                        ->where('purchase_requests.project_site_id', $projectSiteId)
                                                        ->select('purchase_order_requests.id as id','purchase_order_requests.purchase_request_id as purchase_request_id','purchase_order_requests.user_id as user_id')
                                                        ->get();
            }else{
                $purchaseOrderRequestsData = PurchaseOrderRequest::get();
            }
            $records = array();
            $records['data'] = array();
            $records["draw"] = intval($request->draw);
            $records["recordsTotal"] = $records["recordsFiltered"] = count($purchaseOrderRequestsData);
            $end = $request->length < 0 ? count($purchaseOrderRequestsData) : $request->length;
            for($iterator = 0,$pagination = $request->start; $iterator < $end && $pagination < count($purchaseOrderRequestsData); $iterator++,$pagination++ ){
                $user = User::where('id',$purchaseOrderRequestsData[$pagination]['user_id'])->select('first_name','last_name')->first();
                $purchaseRequestFormat = PurchaseRequest::where('id',$purchaseOrderRequestsData[$pagination])->pluck('format_id')->first();
                $records['data'][] = [
                    $iterator+1,
                    $purchaseRequestFormat,
                    $user['first_name'].' '.$user['last_name'],
                    '<div class="btn-group">
                            <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                Actions
                                <i class="fa fa-angle-down"></i>
                            </button>
                            <ul class="dropdown-menu pull-left" role="menu">
                                <li>
                                    <a href="/purchase/purchase-order-request/edit/'.$purchaseOrderRequestsData[$pagination]['id'].'">
                                    <i class="icon-docs"></i> Edit </a>
                                </li>
                            </ul>
                        </div>'
                ];
            }
            $status = 200;
        }catch(\Exception $e){
            $data = [
                'action' => 'Purchase order request Listing',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            $records = array();
            $status = 500;
        }
        return response()->json($records,$status);
    }

    public function getEditView(Request $request,$purchaseOrderRequest){
        try{
            return view('purchase.purchase-order-request.edit');
        }catch(\Exception $e){
            $data = [
                'action' => 'Edit purchase order request',
                'params' => $request->all(),
                'purchase-order-request'=>$purchaseOrderRequest,
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }
}
