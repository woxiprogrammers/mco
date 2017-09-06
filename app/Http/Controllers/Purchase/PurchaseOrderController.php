<?php

namespace App\Http\Controllers\Purchase;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PurchaseOrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('custom.auth');
    }
    public function getManageView(Request $request){
        return view('purchase/purchaseOrder/manage');
    }
    public function getCreateView(Request $request){
        return view('purchase/purchaseOrder/create');
    }
}
