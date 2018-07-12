<?php

namespace App\Http\Controllers\Admin;

use App\AssetMaintenanceBillPayment;
use App\BankInfo;
use App\BankInfoTransaction;
use App\PaymentType;
use App\ProjectSite;
use App\ProjectSiteAdvancePayment;
use App\PurchaseOrderAdvancePayment;
use App\PurchaseOrderPayment;
use App\SiteTransferBillPayment;
use App\SubcontractorAdvancePayment;
use App\SubcontractorBillTransaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\BankRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

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
            $user = Auth::user();
            $bank_name = null;
            $account_number = null;
            if ($request->has('search_name')) {
                $bank_name = $request->search_name;
            }

            if ($request->has('account_number')) {
                $account_number = $request->account_number;
            }

            $bankData = array();
            $filterFlag = true;
            $ids = BankInfo::where('is_active',true)
                ->pluck('id')->toArray();


            if($request->has('search_name') && $bank_name != null && $bank_name != "") {
                $ids = BankInfo::where('bank_name','ilike','%'.$bank_name.'%')
                    ->whereIn('id',$ids)
                    ->pluck('id')->toArray();
                if(count($ids) <= 0) {
                    $filterFlag = false;
                }
            }

            if ($request->has('account_number') && $account_number != null && $account_number != "") {
                $ids = BankInfo::where('account_number','ilike','%'.$account_number.'%')
                    ->whereIn('id',$ids)
                    ->pluck('id')->toArray();
                if(count($ids) <= 0) {
                    $filterFlag = false;
                }
            }

            if ($filterFlag) {
                $bankData = BankInfo::whereIn('id',$ids)
                            ->orderBy('bank_name','asc')->get()->toArray();
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
                if($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin' || $user->customHasPermission('approve-manage-bank')){
                    $actionButton =  '<div class="btn-group">
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
                                    </div>';
                }else{
                    $actionButton =  '<div class="btn-group">
                                        <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                            Actions
                                            <i class="fa fa-angle-down"></i>
                                        </button>
                                        <ul class="dropdown-menu pull-left" role="menu">
                                            <li>
                                                <a href="/bank/edit/'.$bankData[$pagination]['id'].'">
                                                <i class="icon-docs"></i> Edit </a>
                                            </li>
                                        </ul>
                                    </div>';
                }

                    $records['data'][$iterator] = [
                        $bankData[$pagination]['bank_name'],
                        $bankData[$pagination]['account_number'],
                        $bank_status,
                        $actionButton
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
            $projectSiteId = Session::get('global_project_site');
            $search_name = null;
            if ($request->has('search_name')) {
                $search_name = $request->search_name;
            }
            $purchaseOrderAdvancePayments = PurchaseOrderAdvancePayment::join('purchase_orders','purchase_orders.id','purchase_order_advance_payments.purchase_order_id')
                ->join('payment_types','payment_types.id','=','purchase_order_advance_payments.payment_id')
                ->join('vendors','vendors.id','=','purchase_orders.vendor_id')
                ->join('purchase_requests','purchase_requests.id','=','purchase_orders.purchase_request_id')
                ->where('purchase_order_advance_payments.paid_from_slug','bank')
                ->where('purchase_order_advance_payments.bank_id',$request['bank_id'])
                ->where('purchase_requests.project_site_id',$projectSiteId)
                ->where('vendors.company','ilike','%'.$search_name.'%')
                ->select('purchase_order_advance_payments.id as payment_id','purchase_order_advance_payments.amount as amount'
                    ,'purchase_order_advance_payments.created_at as created_at','purchase_requests.project_site_id as project_site_id'
                    ,'vendors.company as name'
                    ,'payment_types.name as payment_name'
                    ,'purchase_order_advance_payments.reference_number as reference_number')->get()->toArray();
            $purchaseOrderBillPayments = PurchaseOrderPayment::join('purchase_order_bills','purchase_order_bills.id','=','purchase_order_payments.purchase_order_bill_id')
                ->join('payment_types','payment_types.id','=','purchase_order_payments.payment_id')
                ->join('purchase_orders','purchase_orders.id','=','purchase_order_bills.purchase_order_id')
                ->join('vendors','vendors.id','=','purchase_orders.vendor_id')
                ->join('purchase_requests','purchase_requests.id','=','purchase_orders.purchase_request_id')
                ->where('purchase_order_payments.paid_from_slug','bank')
                ->where('purchase_order_payments.bank_id',$request['bank_id'])
                ->where('vendors.company','ilike','%'.$search_name.'%')
                ->where('purchase_requests.project_site_id',$projectSiteId)
                ->select('purchase_order_payments.id as payment_id','purchase_order_payments.amount as amount'
                    ,'purchase_order_payments.created_at as created_at','purchase_requests.project_site_id as project_site_id'
                    ,'vendors.company as name','payment_types.name as payment_name'
                    ,'purchase_order_payments.reference_number as reference_number')->get()->toArray();

            $subcontractorAdvancePayments = SubcontractorAdvancePayment::join('subcontractor','subcontractor.id','=','subcontractor_advance_payments.subcontractor_id')
                ->join('payment_types','payment_types.id','=','subcontractor_advance_payments.payment_id')
                ->where('subcontractor_advance_payments.paid_from_slug','bank')
                ->where('subcontractor_advance_payments.bank_id',$request['bank_id'])
                ->where('subcontractor_advance_payments.project_site_id',$projectSiteId)
                ->where('subcontractor.company_name','ilike','%'.$search_name.'%')
                ->select('subcontractor_advance_payments.id as payment_id','subcontractor_advance_payments.amount as amount'
                    ,'subcontractor_advance_payments.project_site_id as project_site_id'
                    ,'subcontractor_advance_payments.created_at as created_at'
                    ,'subcontractor.company_name as name'
                    ,'payment_types.name as payment_name'
                    ,'subcontractor_advance_payments.reference_number as reference_number')->get()->toArray();

            $projectSiteAdvancePayments = ProjectSiteAdvancePayment::join('project_sites','project_sites.id','=','project_site_advance_payments.project_site_id')
                ->join('payment_types','payment_types.id','=','project_site_advance_payments.payment_id')
                ->where('project_site_advance_payments.project_site_id',$projectSiteId)
                ->where('project_site_advance_payments.paid_from_slug','bank')
                ->where('project_site_advance_payments.bank_id',$request['bank_id'])
                ->where('project_sites.name','ilike','%'.$search_name.'%')
                ->select('project_site_advance_payments.id as payment_id'
                    ,'project_site_advance_payments.amount as amount'
                    ,'project_site_advance_payments.created_at as created_at'
                    ,'project_site_advance_payments.project_site_id as project_site_id'
                    ,'project_sites.name as name'
                    ,'payment_types.name as payment_name'
                    ,'project_site_advance_payments.reference_number as reference_number')->get()->toArray();

            $siteTransferPayments = SiteTransferBillPayment::join('site_transfer_bills','site_transfer_bills.id','=','site_transfer_bill_payments.site_transfer_bill_id')
                ->join('payment_types','payment_types.id','=','site_transfer_bill_payments.payment_type_id')
                ->join('inventory_component_transfers','inventory_component_transfers.id','=','site_transfer_bills.inventory_component_transfer_id')
                ->join('vendors','vendors.id','=','inventory_component_transfers.vendor_id')
                ->join('inventory_components','inventory_components.id','inventory_component_transfers.inventory_component_id')
                ->where('inventory_components.project_site_id',$projectSiteId)
                ->where('site_transfer_bill_payments.paid_from_slug','bank')
                ->where('site_transfer_bill_payments.bank_id',$request['bank_id'])
                ->where('vendors.company','ilike','%'.$search_name.'%')
                ->select('site_transfer_bill_payments.id as payment_id','site_transfer_bill_payments.amount as amount'
                    ,'site_transfer_bill_payments.created_at as created_at'
                    ,'inventory_components.project_site_id as project_site_id'
                    ,'vendors.company as name','payment_types.name as payment_name'
                    ,'site_transfer_bill_payments.reference_number as reference_number')->get()->toArray();

            $assetMaintenancePayments = AssetMaintenanceBillPayment::join('asset_maintenance_bills','asset_maintenance_bills.id','asset_maintenance_bill_payments.asset_maintenance_bill_id')
                ->join('payment_types','payment_types.id','=','asset_maintenance_bill_payments.payment_id')
                ->join('asset_maintenance','asset_maintenance.id','=','asset_maintenance_bills.asset_maintenance_id')
                ->join('asset_maintenance_vendor_relation','asset_maintenance_vendor_relation.asset_maintenance_id','=','asset_maintenance.id')
                ->where('asset_maintenance_vendor_relation.is_approved',true)
                ->join('vendors','vendors.id','=','asset_maintenance_vendor_relation.vendor_id')
                ->where('asset_maintenance.project_site_id',$projectSiteId)
                ->where('vendors.company','ilike','%'.$search_name.'%')
                ->where('asset_maintenance_bill_payments.paid_from_slug','bank')
                ->where('asset_maintenance_bill_payments.bank_id',$request['bank_id'])
                ->select('asset_maintenance_bill_payments.id as payment_id','asset_maintenance_bill_payments.amount as amount'
                    ,'asset_maintenance_bill_payments.created_at as created_at'
                    ,'asset_maintenance.project_site_id as project_site_id'
                    ,'vendors.company as name'
                    ,'payment_types.name as payment_name'
                    ,'asset_maintenance_bill_payments.reference_number as reference_number')->get()->toArray();

            $subcontractorCashBillTransactions = SubcontractorBillTransaction::join('subcontractor_bills','subcontractor_bills.id','=','subcontractor_bill_transactions.subcontractor_bills_id')
                ->join('subcontractor_structure','subcontractor_structure.id','=','subcontractor_bills.sc_structure_id')
                ->where('subcontractor_structure.project_site_id',$projectSiteId)
                ->where('subcontractor_bill_transactions.paid_from_slug','bank')
                ->where('subcontractor_bill_transactions.bank_id',$request['bank_id'])
                ->join('subcontractor','subcontractor.id','=','subcontractor_structure.subcontractor_id')
                ->where('subcontractor.company_name','ilike','%'.$search_name.'%')
                ->select('subcontractor_bill_transactions.id as payment_id','subcontractor_bill_transactions.subtotal as amount'
                    ,'subcontractor_bill_transactions.created_at as created_at'
                    ,'subcontractor_structure.project_site_id as project_site_id'
                    ,'subcontractor.company_name as name')->get()->toArray();

            $bankTransactions = BankInfoTransaction::join('users','users.id','=','bank_info_transactions.user_id')
                                            ->join('payment_types','payment_types.id','=','bank_info_transactions.payment_type_id')
                                            ->where('bank_info_transactions.bank_id',$request['bank_id'])
                                            ->whereRaw("CONCAT(users.first_name,' ',users.last_name) ilike '%".$request->search_name."%'")
                                            ->select('bank_info_transactions.id as payment_id','bank_info_transactions.amount as amount'
                                                ,'bank_info_transactions.created_at as created_at'
                                                ,'payment_types.name as payment_name'
                                                ,DB::raw("CONCAT(users.last_name,' ',users.first_name) AS name")
                                                ,'bank_info_transactions.reference_number as reference_number')->get()->toArray();
            
            $cashPaymentData = array_merge($bankTransactions,$purchaseOrderAdvancePayments,$purchaseOrderBillPayments,$subcontractorAdvancePayments,$projectSiteAdvancePayments,$siteTransferPayments,$assetMaintenancePayments,$subcontractorCashBillTransactions);
            usort($cashPaymentData, function($a, $b) {
                return $a['created_at'] < $b['created_at'];
            });
            $iTotalRecords = count($cashPaymentData);
            $records = array();
            $records['data'] = array();
            if($request->length == -1){
                $length = $iTotalRecords;
            }else{
                $length = $request->length;
            }
            for($iterator = 0,$pagination = $request->start; $iterator < $length && $pagination < count($cashPaymentData); $iterator++,$pagination++ ){
                $records['data'][] = [
                    date('j M Y',strtotime($cashPaymentData[$pagination]['created_at'])),
                    array_key_exists('project_site_id',$cashPaymentData[$pagination]) ? "Paid" : "Received",
                    ucwords($cashPaymentData[$pagination]['name']),
                    $cashPaymentData[$pagination]['amount'],
                    $cashPaymentData[$pagination]['payment_name'],
                    $cashPaymentData[$pagination]['reference_number']
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
