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
        return view('drawing/Images/manage');
    }
    public function getCreateView(Request $request){
        return view('drawing/Images/create');
    }
}
