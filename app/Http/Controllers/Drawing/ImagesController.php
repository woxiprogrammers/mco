<?php

namespace App\Http\Controllers\Drawing;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ImagesController extends Controller
{
    public function __construct(){
        $this->middleware('custom.auth');
    }
    public function getManageView(Request $request){
        return view('drawing/images/manage');
    }
    public function getCreateView(Request $request){
        return view('drawing/images/create');
    }
    public function getEditView(Request $request){
        return view('drawing/images/edit');
    }
}
