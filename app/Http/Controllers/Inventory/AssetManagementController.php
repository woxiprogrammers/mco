<?php

namespace App\Http\Controllers\Inventory;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AssetManagementController extends Controller
{
    public function __construct(){
        $this->middleware('custom.auth');
    }
    public function getManageView(Request $request){
        return view('inventory/asset/manage');
    }
    public function getCreateView(Request $request){
        return view('inventory/asset/create');
    }
    public function getEditView(Request $request){
        return view('inventory/asset/edit');
    }
}
