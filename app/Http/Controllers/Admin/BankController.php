<?php

namespace App\Http\Controllers\Admin;

use App\BankInfo;
use App\BankInfoTransaction;
use App\PaymentType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\BankRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
            return redirect('/bank/manage');
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

    public function bankListing(Request $request){
        try{
            if($request->has('search_name')){
                $bankData = BankInfo::where('bank_name','ilike','%'.$request->search_name.'%')->orderBy('bank_name','asc')->get()->toArray();
            }else{
                $bankData = BankInfo::orderBy('bank_name','asc')->get()->toArray();
            }
            $iTotalRecords = count($bankData);
            $records = array();
            $records['data'] = array();
            $end = $request->length < 0 ? count($bankData) : $request->length;
            for($iterator = 0,$pagination = $request->start; $iterator < $end && $pagination < count($bankData); $iterator++,$pagination++ ){
                if($bankData[$pagination]['is_active'] == true){
                    $bank_status = '<td><span class="label label-sm label-success"> Enabled </span></td>';
                    $status = 'Disable';
                }else{
                    $bank_status = '<td><span class="label label-sm label-danger"> Disabled</span></td>';
                    $status = 'Enable';
                }

                    $records['data'][$iterator] = [
                        $bankData[$pagination]['bank_name'],
                        $bankData[$pagination]['account_number'],
                        $bank_status,
                        '<div class="btn-group">
                            <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                Actions
                                <i class="fa fa-angle-down"></i>
                            </button>
                            <ul class="dropdown-menu pull-left" role="menu">
                                <li>
                                    <a href="/bank/edit/'.$bankData[$pagination]['id'].'">
                                    <i class="icon-docs"></i> Edit </a>
                                </li>
                                <li>
                                    <a href="/bank/change-status/'.$bankData[$pagination]['id'].'">
                                        <i class="icon-tag"></i> '.$status.' </a>
                                </li>
                            </ul>
                        </div>'
                    ];

            }
            $records["draw"] = intval($request->draw);
            $records["recordsTotal"] = $iTotalRecords;
            $records["recordsFiltered"] = $iTotalRecords;
        }catch(\Exception $e){
            $records = array();
            $data = [
                'action' => 'Get Bank Listing',
                'params' => $request->all(),
                'exception'=> $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
        return response()->json($records);
    }

    public function getEditView(Request $request,$bank){
        try{
            $bank = $bank->toArray();
            $paymentModes = PaymentType::get();
            $bankTransactions = BankInfoTransaction::where('bank_id',$bank['id'])->orderBy('created_at','desc')->get();
            return view('admin.bank.edit')->with(compact('bank','paymentModes','bankTransactions'));
        }catch(\Exception $e){
            $data = [
                'action' => "Get bank edit view",
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function editBank(Request $request, $bank){
        try{
            $data = $request->all();
            $bankData['bank_name'] = ucwords(trim($data['bank_name']));
            $bankData['account_number'] = $data['account_number'];
            $bankData['ifs_code'] = $data['ifs_code'];
            $bankData['branch_id'] = $data['branch_id'];
            $bankData['branch_name'] = $data['branch_name'];
            $bank->update($bankData);
            $request->session()->flash('success', 'Bank Edited successfully.');
            return redirect('/bank/manage');
        }catch(\Exception $e){
            $data = [
                'action' => 'Create Bank',
                'params' => $request->all(),
                'exception'=> $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function changeBankStatus(Request $request, $bank){
        try{
            $newStatus = (boolean)!$bank->is_active;
            $bank->update(['is_active' => $newStatus]);
            $request->session()->flash('success', 'Bank Status changed successfully.');
            return redirect('/bank/manage');
        }catch(\Exception $e){
            $data = [
                'action' => 'Change bank status',
                'param' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function createTransaction(Request $request,$bank){
        try{
            $user = Auth::user();
            $bankTransactionData = $request->except('_token');
            $bankTransactionData['bank_id'] = $bank['id'];
            $bankTransactionData['user_id'] = $user['id'];
            BankInfoTransaction::create($bankTransactionData);
            $bankData['balance_amount'] = $bank['balance_amount'] + $request['amount'];
            $bankData['total_amount'] = $bank['total_amount'] + $request['amount'];
            $bank->update($bankData);
            $request->session()->flash('success','Transaction created successfully');
            return redirect('/bank/edit/'.$bank['id']);
        }catch(\Exception $e){
            $data = [
                'action' => 'create bank transaction',
                'exception' => $e->getMessage(),
                'params' => $request->all()
            ];
        }
        Log::critical(json_encode($data));
        abort(500);
    }

    public function getBankTransactionListing(Request $request){
        try{
            $status = 200;
            $bankTransactions = BankInfoTransaction::where('bank_id',$request['bank_id'])->orderBy('created_at','desc')->get();
            $iTotalRecords = count($bankTransactions);
            $records = array();
            $records['data'] = array();
            if($request->length == -1){
                $length = $iTotalRecords;
            }else{
                $length = $request->length;
            }
            for($iterator = 0,$pagination = $request->start; $iterator < $length && $iterator < count($bankTransactions); $iterator++,$pagination++ ){
                $records['data'][] = [
                    date('d M Y',strtotime($bankTransactions[$pagination]['created_at'])),
                    $bankTransactions[$pagination]->user->first_name,
                    $bankTransactions[$pagination]['amount'],
                    $bankTransactions[$pagination]->paymentType->name,
                    $bankTransactions[$pagination]['reference_number']
                ];
            }
            $records["draw"] = intval($request->draw);
            $records["recordsTotal"] = $iTotalRecords;
            $records["recordsFiltered"] = $iTotalRecords;
        }catch(\Exception $e){
            $records = array();
            $data = [
                'action' => 'Get Bank Transaction Listing',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            $status = 200;
        }
        return response()->json($records,$status);
    }
}
