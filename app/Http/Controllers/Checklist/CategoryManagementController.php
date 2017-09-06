<?php

namespace App\Http\Controllers\Checklist;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategoryManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('custom.auth')->except('categoryManagementListing');
    }
    public function getManageView(Request $request){
        return view('checklist/categoryManagement/manage');
    }
    public function getEditView(Request $request){
        return view('checklist/categoryManagement/edit');
    }
}