<?php

namespace App\Http\Controllers\Drawing;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategoryManagementController extends Controller
{
    public function __construct(){
        $this->middleware('custom.auth');
    }
    public function getManageView(Request $request){
        return view('drawing/category-management/manage');
    }
    public function getCreateMainView(Request $request){
        return view('drawing/category-management/create-main');
    }
    public function getCreateSubView(Request $request){
        return view('drawing/category-management/create-sub');
    }
    public function getMainEditView(Request $request)
    {
        return view('drawing/category-management/edit-main');
    }
    public function getSubEditView(Request $request)
    {
        return view('drawing/category-management/edit-sub');
    }
}
