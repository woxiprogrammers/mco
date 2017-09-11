<?php

namespace App\Http\Controllers\Purchase;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
        return view('purchase/purchase-request/create');
    }
    public function getEditView(Request $request,$status){
        if($status == 2){
            return view('purchase/purchase-request/edit-draft');
        }else if($status == 1){
            return view('purchase/purchase-request/edit-approved');
        }
    }
}
