<?php

namespace App\Http\Controllers\Purchase;

use App\PurchaseRequestComponentVendorMailInfo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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
                $records['data'][] = [
                    ($iterator+1),
                    $vendorMailData[$pagination]->componentVendorRelation->vendor->name
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
}
