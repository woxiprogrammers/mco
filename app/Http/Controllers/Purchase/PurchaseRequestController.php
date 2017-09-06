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
        return view('purchase/purchaseRequest/manage');
    }
    public function getCreateView(Request $request){
        return view('purchase/purchaseRequest/create');
    }
    public function getEditView(Request $request,$status){
        if($status == 2){
            return view('purchase/purchaseRequest/editDraft');
        }else if($status == 1){
            return view('purchase/purchaseRequest/editApproved');
        }
    }
}
