<?php

namespace App\Http\Controllers\Admin;

use App\BankInfo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\BankRequest;

class BankController extends Controller
{

    public function __construct()
    {
        $this->middleware('custom.auth');
    }
    public function getManageView(Request $request){
        try{
            return view('admin.bank.manage');
        }catch(\Exception $e){
            $data = [
                'action' => 'Get bank manage view',
                'exception'=> $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getCreateView(Request $request){
        try{
            return view('admin.bank.create');
        }catch(\Exception $e){
            $data = [
                'action' => 'Get bank create view',
                'exception'=> $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }

    }

    public function CreateBank(BankRequest $request){
        try{
            $data = $request->except('_token');
            $data['is_active'] = (boolean)false;
            $bank = BankInfo::create($data);
            $request->session()->flash('success', 'Bank created successfully');
            return redirect('/bank/create');
        }catch(\Exception $e){
            $data = [
                'action' => 'Create new Client',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }

    }
}
