<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    public function __construct(){
        $this->middleware('guest');
    }
    /*
	View Login
    */
    public function viewLogin(Request $request){
        try{
            return view('admin.login');
        }catch(\Exception $e){
            $data = [
                'action' => 'View Login Page',
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
        }
    }
}
