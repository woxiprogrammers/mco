<?php

namespace App\Http\Controllers\Purchase;

use App\MaterialRequestComponents;
use App\MaterialRequests;
use App\PurchaseRequestComponentStatuses;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PurchaseRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('custom.auth');
    }
    public function getManageView(Request $request){
        return view('purchase/purchase-request/manage');
    }
    public function getCreateView(Request $request){
        $user = Auth::user();
        $materialRequestList = array();
        $inIndentStatusId = PurchaseRequestComponentStatuses::where('slug','in-indent')->pluck('id')->first();
        $iterator = 0;
        $materialRequestComponents = MaterialRequestComponents::where('component_status_id',$inIndentStatusId)->get();
            foreach($materialRequestComponents as $index => $materialRequestComponent){
                $materialRequestList[$iterator]['material_request_component_id'] = $materialRequestComponent->id;
                $materialRequestList[$iterator]['name'] = $materialRequestComponent->name;
                $materialRequestList[$iterator]['quantity'] = $materialRequestComponent->quantity;
                $materialRequestList[$iterator]['unit_id'] = $materialRequestComponent->unit_id;
                $materialRequestList[$iterator]['unit'] = $materialRequestComponent->unit->name;
                $materialRequestList[$iterator]['component_type_id'] = $materialRequestComponent->component_type_id;
                $materialRequestList[$iterator]['component_type'] = $materialRequestComponent->materialRequestComponentTypes->name;
                $materialRequestList[$iterator]['component_status_id'] = $materialRequestComponent->component_status_id;
                $materialRequestList[$iterator]['component_status'] = $materialRequestComponent->purchaseRequestComponentStatuses->name;
                $iterator++;
            }
        return view('purchase/purchase-request/create')->with(compact('materialRequestList'));
    }
    public function getEditView(Request $request,$status){
        if($status == 2){
            return view('purchase/purchase-request/edit-draft');
        }else if($status == 1){
            return view('purchase/purchase-request/edit-approved');
        }
    }
    public function create(Request $request){
      try{

      }catch (\Exception $e){

      }
    }
}
