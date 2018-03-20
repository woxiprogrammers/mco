<?php

namespace App\Http\Controllers\Inventory;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class SiteTransferBillingController extends Controller
{
    public function __construct(){
        $this->middleware('custom.auth');
    }

    public function getManageView(Request $request){
        try{
            return view('inventory.site-transfer-bill.manage');
        }catch (\Exception $e){
            $data = [
                'action' => 'Site Transfer Billing get manage view',
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getCreateView(Request $request){
        try{
            return view('inventory.site-transfer-bill.create');
        }catch (\Exception $e){
            $data = [
                'action' => 'Site Transfer Billing get manage view',
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getApprovedTransaction(Request $request){
        try{

        }catch(\Exception $e){
            $data = [
                'action' => 'Get approved Transaction for typeahead',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            $status = 200;
        }
    }
}
