<?php

namespace App\Http\Controllers\Inventory;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class InventoryManageController extends Controller
{
    public function __construct(){
        $this->middleware('custom.auth');
    }
    public function getManageView(Request $request){
        return view('inventory/manage');
    }
    public function getCreateView(Request $request){
        return view('inventory/create');
    }
}
