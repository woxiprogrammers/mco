<?php

namespace App\Http\Controllers\Purchase;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PurchaseRequestController extends Controller
{
    public function getManageView(Request $request){
        return view('purchase/purchaseRequest/manage');
    }
    public function getCreateView(Request $request){
        return view('purchase/purchaseRequest/create');
    }
}
