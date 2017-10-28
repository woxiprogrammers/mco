<?php

namespace App\Http\Controllers\Peticash;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PeticashController extends Controller
{
    public function __construct(){
        $this->middleware('custom.auth');
    }

    public function getManageViewForMasterPeticashAccount(Request $request){
        try{
            return view('peticash.master-peticash-account.manage');
        }catch(\Exception $e){
            $data = [
                'action' => 'Get Peticash Manage view',
                'exception' => $e->getMessage(),
                'params' => $request->all()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getCreateViewForMasterPeticashAccount(Request $request){
        try{
            return view('peticash.master-peticash-account.create');
        }catch(\Exception $e){
            $data = [
                'action' => "Get Peticash Add Amount View",
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getManageViewForSitewisePeticashAccount(Request $request){
        try{
            return view('peticash.sitewise-peticash-account.manage');
        }catch(\Exception $e){
            $data = [
                'action' => 'Get Peticash Sitewise Manage view',
                'exception' => $e->getMessage(),
                'params' => $request->all()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getCreateViewForSitewisePeticashAccount(Request $request){
        try{
            return view('peticash.sitewise-peticash-account.create');
        }catch(\Exception $e){
            $data = [
                'action' => "Get Peticash Add Amount Sitewise View",
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getManageViewPeticashApproval(Request $request){
        try{
            return view('peticash.peticash-approval.manage');
        }catch(\Exception $e){
            $data = [
                'action' => 'Get Peticash Sitewise Manage view',
                'exception' => $e->getMessage(),
                'params' => $request->all()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

}
