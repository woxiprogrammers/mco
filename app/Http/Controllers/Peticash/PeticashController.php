<?php

namespace App\Http\Controllers\Peticash;

use App\Client;
use App\Employee;
use App\PaymentType;
use App\PeticashSalaryTransaction;
use App\PeticashSiteTransfer;
use App\PeticashStatus;
use App\PeticashTransactionType;
use App\Project;
use App\ProjectSite;
use App\PurcahsePeticashTransaction;
use App\PurchaseOrderBillPayment;
use App\Quotation;
use App\QuotationStatus;
use App\Role;
use App\Unit;
use App\User;
use App\UserProjectSiteRelation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PeticashController extends Controller
{
    public function __construct(){
        $this->middleware('custom.auth');
    }

    public function getManageViewForMasterPeticashAccount(Request $request){
        try {
            $masteraccountAmount = PeticashSiteTransfer::where('project_site_id','=',0)->sum('amount');
            $sitewiseaccountAmount = PeticashSiteTransfer::where('project_site_id','!=',0)->sum('amount');
            $balance = $masteraccountAmount - $sitewiseaccountAmount;
            return view('peticash.master-peticash-account.manage')->with(compact('masteraccountAmount','sitewiseaccountAmount','balance'));
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
            $paymenttypes = PaymentType::get(['id','name'])->toArray();
            $users = Role::join('user_has_roles','roles.id','=','user_has_roles.role_id')
                ->join('users','user_has_roles.user_id','=','users.id')
                ->whereIn('roles.slug',['admin','superadmin'])
                ->select('users.id','users.first_name as name')->get()->toArray();
            return view('peticash.master-peticash-account.create')->with(compact('paymenttypes','users'));
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

    public function getUserBySites(Request $request, $siteid){
        try{
            $status = 200;
            $projectSiteUser = UserProjectSiteRelation::join('users','user_project_site_relation.user_id','=','users.id')
                ->join('user_has_permissions','users.id','=','user_has_permissions.user_id')
                ->join('permissions','permissions.id','=','user_has_permissions.permission_id')
                ->where('permissions.name','=','create-peticash-management')
                ->where('user_project_site_relation.project_site_id',$siteid)
                ->select('users.id','users.first_name as name')->get()->toArray();
            $projectOptions = array();
            for($i = 0 ; $i < count($projectSiteUser); $i++){
                $projectOptions[] = '<option value="'.$projectSiteUser[$i]['id'].'"> '.$projectSiteUser[$i]['name'].' </option>';
            }
        }catch (\Exception $e){
            $projectOptions = array();
            $status = 500;
            $data = [
                'actions' => 'Create New Bill',
                'params' => $request->all(),
                'exception' => $e->getMessage(),
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
        return response()->json($projectOptions,$status);
    }

    public function createMasterPeticashAccount(Request $request) {
        try{
            $accountData = array();
            $user = Auth::user();
            $fromuserid = $user->id;
            $accountData['user_id'] = $request->to_userid;
            $accountData['amount'] = $request->amount;
            $accountData['payment_id'] = $request->payment_type;
            $accountData['date'] = $request->date;
            $accountData['received_from_user_id'] = $fromuserid;
            $accountData['remark'] = $request->remark;
            $accountData['project_site_id'] = 0; // VALUE 0 FOR MASTER ACCOUNT
            $category = PeticashSiteTransfer::create($accountData);
            $request->session()->flash('success', 'Amount Added successfully.');
            return redirect('peticash/master-peticash-account/createpage');
        }catch(\Exception $e){
            $data = [
                'action' => 'Create Master Peticash Account',
                'params' => $request->all(),
                'exception'=> $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function editViewMasterPeticashAccount(Request $request, $txnid) {
        try{
            $accountData = array();
            $txndetail = PeticashSiteTransfer::where('id','=',$txnid)->get()->toArray();
            $data = array();
            foreach ($txndetail as $txn) {
                $data['from_id'] = User::findOrFail($txn['received_from_user_id'])->toArray()['first_name']." ".User::findOrFail($txn['received_from_user_id'])->toArray()['last_name'];
                $data['to_id'] = User::findOrFail($txn['user_id'])->toArray()['first_name']." ".User::findOrFail($txn['user_id'])->toArray()['last_name'];
                $data['amount'] = $txn['amount'];
                $data['payment_id'] = PaymentType::findOrFail($txn['payment_id'])->toArray()['name'];
                $data['date'] = $txn['date'];
                $data['remark'] = $txn['remark'];
                $data['created_on'] = $txn['created_at'];
                $data['txn_id'] = $txn['id'];
            }
            return view('peticash/master-peticash-account/edit', $data);
        }catch(\Exception $e){
            $data = [
                'action' => 'Edit Master Peticash Account',
                'params' => $request->all(),
                'exception'=> $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function editMasterPeticashAccount(Request $request) {
        try{
            PeticashSiteTransfer::where('id','=',$request->txn_id)->update(['amount' => $request->amount]);
            $request->session()->flash('success', 'Amount Edited successfully.');
            return redirect('peticash/master-peticash-account/manage');
        }catch(\Exception $e){
            $data = [
                'action' => 'Edit Master Peticash Account',
                'params' => $request->all(),
                'exception'=> $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function editViewSitewisePeticashAccount(Request $request, $txnid) {
        try{
            $accountData = array();
            $txndetail = PeticashSiteTransfer::where('id','=',$txnid)->get()->toArray();
            $data = array();
            foreach ($txndetail as $txn) {
                $data['from_id'] = User::findOrFail($txn['received_from_user_id'])->toArray()['first_name']." ".User::findOrFail($txn['received_from_user_id'])->toArray()['last_name'];
                $data['to_id'] = User::findOrFail($txn['user_id'])->toArray()['first_name']." ".User::findOrFail($txn['user_id'])->toArray()['last_name'];
                $data['amount'] = $txn['amount'];
                $data['payment_id'] = PaymentType::findOrFail($txn['payment_id'])->toArray()['name'];
                $data['date'] = $txn['date'];
                $data['remark'] = $txn['remark'];
                $data['created_on'] = $txn['created_at'];
                $data['txn_id'] = $txn['id'];
                $data['sitename'] = ProjectSite::findOrFail($txn['project_site_id'])->toArray()['name'];
            }
            return view('peticash/sitewise-peticash-account/edit', $data);
        }catch(\Exception $e){
            $data = [
                'action' => 'Edit Master Peticash Account',
                'params' => $request->all(),
                'exception'=> $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function editSitewisePeticashAccount(Request $request) {
        try{
            PeticashSiteTransfer::where('id','=',$request->txn_id)->update(['amount' => $request->amount]);
            $request->session()->flash('success', 'Amount Edited successfully.');
            return redirect('peticash/sitewise-peticash-account/manage');
        }catch(\Exception $e){
            $data = [
                'action' => 'Edit Master Peticash Account',
                'params' => $request->all(),
                'exception'=> $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function createSitewisePeticashAccount(Request $request) {
        try{
            $accountData = array();
            $user = Auth::user();
            $fromuserid = $user->id;
            $accountData['user_id'] = $request->to_userid;
            $accountData['amount'] = $request->amount;
            $accountData['payment_id'] = $request->payment_type;
            $accountData['date'] = $request->date;
            $accountData['received_from_user_id'] = $fromuserid;
            $accountData['remark'] = $request->remark;
            $accountData['project_site_id'] = $request->project_site_id; // VALUE 0 FOR MASTER ACCOUNT
            $category = PeticashSiteTransfer::create($accountData);
            $request->session()->flash('success', 'Amount Added successfully.');
            return redirect('peticash/sitewise-peticash-account/createpage');
        }catch(\Exception $e){
            $data = [
                'action' => 'Create Sitewise Peticash Account',
                'params' => $request->all(),
                'exception'=> $e->getMessage()
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
            $paymenttypes = PaymentType::get(['id','name'])->toArray();
            $users = array();
            $sites = ProjectSite::get(['id','name','address'])->toArray();
            return view('peticash.sitewise-peticash-account.create')->with(compact('paymenttypes','users','sites'));
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

    public function getManageViewPeticashPurchaseApproval(Request $request){
        try{
            $approvedQuotationStatus = QuotationStatus::where('slug','approved')->first();
            $projectSiteIds = Quotation::where('quotation_status_id',$approvedQuotationStatus['id'])->pluck('project_site_id')->toArray();
            $projectIds = ProjectSite::whereIn('id',$projectSiteIds)->pluck('project_id')->toArray();
            $clientIds = Project::whereIn('id',$projectIds)->pluck('client_id')->toArray();
            $clients = Client::whereIn('id',$clientIds)->where('is_active',true)->orderBy('id','asc')->get()->toArray();
            return view('peticash.peticash-approval.manage-purchase')->with(compact('clients'));
        }catch(\Exception $e){
            $data = [
                'action' => 'Get Peticash Request Purchase Approval view',
                'exception' => $e->getMessage(),
                'params' => $request->all()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getManageViewPeticashSalaryApproval(Request $request){
        try{
            $approvedQuotationStatus = QuotationStatus::where('slug','approved')->first();
            $projectSiteIds = Quotation::where('quotation_status_id',$approvedQuotationStatus['id'])->pluck('project_site_id')->toArray();
            $projectIds = ProjectSite::whereIn('id',$projectSiteIds)->pluck('project_id')->toArray();
            $clientIds = Project::whereIn('id',$projectIds)->pluck('client_id')->toArray();
            $clients = Client::whereIn('id',$clientIds)->where('is_active',true)->orderBy('id','asc')->get()->toArray();
            return view('peticash.peticash-approval.manage-salary')->with(compact('clients'));
        }catch(\Exception $e){
            $data = [
                'action' => 'Get Peticash Request Salary Approval view',
                'exception' => $e->getMessage(),
                'params' => $request->all()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function purchaseApprovalListing(Request $request){
        try{
            $postdata = null;
            $material_name = "";
            $emp_name = null;
            $status = 0;
            $site_id = 0;
            $month = 0;
            $year = 0;
            $postDataArray = array();
            if ($request->has('material_name')) {
                if ($request['material_name'] != "") {
                    $material_name = $request['material_name'];
                }
            }
            if ($request->has('search_name')) {
                $emp_name = $request['search_name'];
            }
            if ($request->has('status')) {
                $status = $request['status'];
            }
            if($request->has('postdata')) {
                $postdata = $request['postdata'];
                if($postdata != null) {
                    $mstr = explode(",",$request['postdata']);
                    foreach($mstr as $nstr)
                    {
                        $narr = explode("=>",$nstr);
                        $narr[0] = str_replace("\x98","",$narr[0]);
                        $ytr[1] = $narr[1];
                        $postDataArray[$narr[0]] = $ytr[1];
                    }
                }
                $site_id = $postDataArray['site_id'];
                $month = $postDataArray['month'];
                $year = $postDataArray['year'];
            }
            $salaryTransactionData = array();
            $ids = PurcahsePeticashTransaction::all()->pluck('id');
            $filterFlag = true;

            if ($site_id != 0 && $filterFlag == true) {
                $ids = PurcahsePeticashTransaction::whereIn('id',$ids)->where('project_site_id', $site_id)->pluck('id');
                if(count($ids) <= 0) {
                    $filterFlag = false;
                }
            }

            if ($year != 0 && $filterFlag == true) {
                $ids = PurcahsePeticashTransaction::whereIn('id',$ids)->whereYear('date', $year)->pluck('id');
                if(count($ids) <= 0) {
                    $filterFlag = false;
                }
            }

            if ($month != 0 && $filterFlag == true) {
                $ids = PurcahsePeticashTransaction::whereIn('id',$ids)->whereMonth('date', $month)->pluck('id');
                if(count($ids) <= 0) {
                    $filterFlag = false;
                }
            }

            if ($material_name != "" && $filterFlag == true) {
                $ids = PurcahsePeticashTransaction::whereIn('id',$ids)->where('name','ilike','%'.$material_name.'%')->pluck('id');
                if(count($ids) <= 0) {
                    $filterFlag = false;
                }
            }

            if ($status != 0 && $filterFlag == true) {
                $ids = PurcahsePeticashTransaction::whereIn('id',$ids)->where('peticash_status_id', $status)->pluck('id');
                if(count($ids) <= 0) {
                    $filterFlag = false;
                }
            }

            if ($filterFlag) {
                $salaryTransactionData = PurcahsePeticashTransaction::whereIn('id',$ids)->orderBy('id','desc')->get()->toArray();
            }

            $iTotalRecords = count($salaryTransactionData);
            $records = array();
            $records['data'] = array();
            $end = $request->length < 0 ? count($salaryTransactionData) : $request->length;
            for($iterator = 0,$pagination = $request->start; $iterator < $end && $pagination < count($salaryTransactionData); $iterator++,$pagination++ ){
                $txnStatus = PeticashStatus::findOrFail($salaryTransactionData[$pagination]['peticash_status_id'])->toArray()['slug'];
                switch(strtolower($txnStatus)){
                    case 'grn-generated':
                        $user_status = '<td><span class="label label-sm label-info">'.$txnStatus.' </span></td>';
                        $actionDropDown = '<div class="btn-group">
                            <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                Actions
                                <i class="fa fa-angle-down"></i>
                            </button>
                            <ul class="dropdown-menu pull-left" role="menu">
                                <li>
                                    <a onclick="openEditRequestApprovalModal('.$salaryTransactionData[$pagination]['id'].');" href="javascript:void(0);">
                                        <i class="icon-docs"></i> Details
                                    </a>
                                </li>
                                <li>
                                    <a onclick="openApproveModal('.$salaryTransactionData[$pagination]['id'].');" href="javascript:void(0);">
                                        <i class="icon-tag"></i> Approve / Disapprove
                                    </a>
                                </li>
                            </ul>
                        </div>';
                        break;
                    case 'pending':
                        $user_status = '<td><span class="label label-sm label-warning">'.$txnStatus.' </span></td>';
                        $actionDropDown = '<div class="btn-group">
                            <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                Actions
                                <i class="fa fa-angle-down"></i>
                            </button>
                            <ul class="dropdown-menu pull-left" role="menu">
                                <li>
                                    <a onclick="openEditRequestApprovalModal('.$salaryTransactionData[$pagination]['id'].');" href="javascript:void(0);">
                                        <i class="icon-docs"></i> Edit With Approve
                                    </a>
                                </li>
                                <li>
                                    <a onclick="openApproveModal('.$salaryTransactionData[$pagination]['id'].');" href="javascript:void(0);">
                                        <i class="icon-tag"></i>Disapprove
                                    </a>
                                </li>
                            </ul>
                        </div>';
                        break;
                    case 'approved':
                        $user_status = '<td><span class="label label-sm label-success">'.$txnStatus.' </span></td>';
                        $actionDropDown = '<div class="btn-group">
                            <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                Actions
                                <i class="fa fa-angle-down"></i>
                            </button>
                            <ul class="dropdown-menu pull-left" role="menu">
                                <li>
                                <a onclick="openEditRequestApprovalModal('.$salaryTransactionData[$pagination]['id'].');" href="javascript:void(0);">
                                    <i class="icon-docs"></i> Details
                                </a>
                                </li>
                            <!--<li>
                                <a onclick="openApproveModal('.$salaryTransactionData[$pagination]['id'].');" href="javascript:void(0);">
                                    <i class="icon-tag"></i> Approve / Disapprove
                                </a>
                            </li>-->
                            </ul>
                        </div>';
                        break;
                    default:
                        $user_status = '<td><span class="label label-sm label-danger">'.$txnStatus.' </span></td>';
                        $actionDropDown = '<div class="btn-group">
                            <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                Actions
                                <i class="fa fa-angle-down"></i>
                            </button>
                            <ul class="dropdown-menu pull-left" role="menu">
                                <li>
                                <a onclick="openEditRequestApprovalModal('.$salaryTransactionData[$pagination]['id'].');" href="javascript:void(0);">
                                    <i class="icon-docs"></i> Details
                                </a>
                            </li>
                            <!--<li>
                                <a onclick="openApproveModal('.$salaryTransactionData[$pagination]['id'].');" href="javascript:void(0);">
                                    <i class="icon-tag"></i> Approve / Disapprove
                                </a>
                            </li>-->
                            </ul>
                        </div>';
                        break;
                }
                $records['data'][$iterator] = [
                    $salaryTransactionData[$pagination]['id'],
                    $salaryTransactionData[$pagination]['name'],
                    $salaryTransactionData[$pagination]['quantity'],
                    Unit::findOrFail($salaryTransactionData[$pagination]['unit_id'])->toArray()['name'],
                    $salaryTransactionData[$pagination]['bill_amount'],
                    User::findOrFail($salaryTransactionData[$pagination]['reference_user_id'])->toArray()['first_name']." ".User::findOrFail($salaryTransactionData[$pagination]['reference_user_id'])->toArray()['last_name'],
                    date('d M Y',strtotime($salaryTransactionData[$pagination]['date'])),
                    ProjectSite::findOrFail($salaryTransactionData[$pagination]['project_site_id'])->toArray()['name'],
                    $user_status,
                    $actionDropDown
                ];
            }
            $records["draw"] = intval($request->draw);
            $records["recordsTotal"] = $iTotalRecords;
            $records["recordsFiltered"] = $iTotalRecords;
        }catch(\Exception $e){
            $records = array();
            $data = [
                'action' => 'Get Purchase Material Account Listing',
                'params' => $request->all(),
                'exception'=> $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
        return response()->json($records);
    }
    public function salaryApprovalListing(Request $request){
        try{
            $postdata = null;
            $emp_id = "";
            $emp_name = null;
            $status = 0;
            $site_id = 0;
            $month = 0;
            $year = 0;
            $postDataArray = array();
            if ($request->has('emp_id')) {
                if ($request['emp_id'] != "") {
                    $emp_id = $request['emp_id'];
                }
            }
            if ($request->has('search_name')) {
                $emp_name = $request['search_name'];
            }
            if ($request->has('status')) {
                $status = $request['status'];
            }
            if($request->has('postdata')) {
                $postdata = $request['postdata'];
                if($postdata != null) {
                    $mstr = explode(",",$request['postdata']);
                    foreach($mstr as $nstr)
                    {
                        $narr = explode("=>",$nstr);
                        $narr[0] = str_replace("\x98","",$narr[0]);
                        $ytr[1] = $narr[1];
                        $postDataArray[$narr[0]] = $ytr[1];
                    }
                }
                $site_id = $postDataArray['site_id'];
                $month = $postDataArray['month'];
                $year = $postDataArray['year'];
            }

            $salaryTransactionData = array();
            $ids = PeticashSalaryTransaction::all()->pluck('id');
            $filterFlag = true;

            if ($site_id != 0 && $filterFlag == true) {
                $ids = PeticashSalaryTransaction::whereIn('id',$ids)->where('project_site_id', $site_id)->pluck('id');
                if(count($ids) <= 0) {
                    $filterFlag = false;
                }
            }

            if ($year != 0 && $filterFlag == true) {
                $ids = PeticashSalaryTransaction::whereIn('id',$ids)->whereYear('date', $year)->pluck('id');
                if(count($ids) <= 0) {
                    $filterFlag = false;
                }
            }

            if ($month != 0 && $filterFlag == true) {
                $ids = PeticashSalaryTransaction::whereIn('id',$ids)->whereMonth('date', $month)->pluck('id');
                if(count($ids) <= 0) {
                    $filterFlag = false;
                }
            }

            if ($emp_id != "" && $filterFlag == true) {
                $ids = PeticashSalaryTransaction::whereIn('id',$ids)->where('employee_id',$emp_id)->pluck('id');
                if(count($ids) <= 0) {
                    $filterFlag = false;
                }
            }

            if ($status != 0 && $filterFlag == true) {
                $ids = PeticashSalaryTransaction::whereIn('id',$ids)->where('peticash_status_id', $status)->pluck('id');
                if(count($ids) <= 0) {
                    $filterFlag = false;
                }
            }

            if ($filterFlag) {
                $salaryTransactionData = PeticashSalaryTransaction::whereIn('id',$ids)->orderBy('id','desc')->get()->toArray();
            }

            $iTotalRecords = count($salaryTransactionData);
            $records = array();
            $records['data'] = array();
            $end = $request->length < 0 ? count($salaryTransactionData) : $request->length;
            for($iterator = 0,$pagination = $request->start; $iterator < $end && $pagination < count($salaryTransactionData); $iterator++,$pagination++ ){
                $txnStatus = PeticashStatus::findOrFail($salaryTransactionData[$pagination]['peticash_status_id'])->toArray()['slug'];
                switch(strtolower($txnStatus)){
                    case 'pending':
                        $checkbox_enable = '<input type="checkbox" name="salary_txn_ids" value="'.$salaryTransactionData[$pagination]['id'].'">';
                        $user_status = '<td><span class="label label-sm label-warning">'.$txnStatus.' </span></td>';
                        $actionDropDown = '<div class="btn-group">
                            <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                Actions
                                <i class="fa fa-angle-down"></i>
                            </button>
                            <ul class="dropdown-menu pull-left" role="menu">
                                <li>
                                    <a onclick="openEditRequestApprovalModal('.$salaryTransactionData[$pagination]['id'].');" href="javascript:void(0);">
                                        <i class="icon-docs"></i> Details
                                    </a>
                                </li>
                                <li>
                                    <a onclick="openApproveModal('.$salaryTransactionData[$pagination]['id'].');" href="javascript:void(0);">
                                        <i class="icon-tag"></i> Approve / Disapprove
                                    </a>
                                </li>
                            </ul>
                        </div>';
                        break;
                    case 'approved':
                        $checkbox_enable = '<input  disabled type="checkbox" name="salary_txn_ids" value="'.$salaryTransactionData[$pagination]['id'].'">';
                        $user_status = '<td><span class="label label-sm label-success">'.$txnStatus.' </span></td>';
                        $actionDropDown = '<div class="btn-group">
                            <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                Actions
                                <i class="fa fa-angle-down"></i>
                            </button>
                            <ul class="dropdown-menu pull-left" role="menu">
                                <li>
                                <a onclick="openEditRequestApprovalModal('.$salaryTransactionData[$pagination]['id'].');" href="javascript:void(0);">
                                    <i class="icon-docs"></i> Details
                                </a>
                                </li>
                            <!--<li>
                                <a onclick="openApproveModal('.$salaryTransactionData[$pagination]['id'].');" href="javascript:void(0);">
                                    <i class="icon-tag"></i> Approve / Disapprove
                                </a>
                            </li>-->
                            </ul>
                        </div>';
                        break;
                    default:
                        $checkbox_enable = '<input  disabled type="checkbox" name="salary_txn_ids" value="'.$salaryTransactionData[$pagination]['id'].'">';
                        $user_status = '<td><span class="label label-sm label-danger">'.$txnStatus.' </span></td>';
                        $actionDropDown = '<div class="btn-group">
                            <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                Actions
                                <i class="fa fa-angle-down"></i>
                            </button>
                            <ul class="dropdown-menu pull-left" role="menu">
                                <li>
                                <a onclick="openEditRequestApprovalModal('.$salaryTransactionData[$pagination]['id'].');" href="javascript:void(0);">
                                    <i class="icon-docs"></i> Details
                                </a>
                            </li>
                            <!--<li>
                                <a onclick="openApproveModal('.$salaryTransactionData[$pagination]['id'].');" href="javascript:void(0);">
                                    <i class="icon-tag"></i> Approve / Disapprove
                                </a>
                            </li>-->
                            </ul>
                        </div>';
                        break;
                }
                $records['data'][$iterator] = [
                    $checkbox_enable,
                    $salaryTransactionData[$pagination]['id'],
                    $salaryTransactionData[$pagination]['employee_id'],
                    Employee::findOrFail($salaryTransactionData[$pagination]['employee_id'])->toArray()['name'],
                    PeticashTransactionType::findOrFail($salaryTransactionData[$pagination]['peticash_transaction_type_id'])->toArray()['name'],
                    $salaryTransactionData[$pagination]['amount'],
                    ($salaryTransactionData[$pagination]['payable_amount'] == null) ? "-" : $salaryTransactionData[$pagination]['payable_amount'],
                    User::findOrFail($salaryTransactionData[$pagination]['reference_user_id'])->toArray()['first_name']." ".User::findOrFail($salaryTransactionData[$pagination]['reference_user_id'])->toArray()['last_name'],
                    date('d M Y',strtotime($salaryTransactionData[$pagination]['date'])),
                    ProjectSite::findOrFail($salaryTransactionData[$pagination]['project_site_id'])->toArray()['name'],
                    $user_status,
                    $actionDropDown
                ];
            }
            $records["draw"] = intval($request->draw);
            $records["recordsTotal"] = $iTotalRecords;
            $records["recordsFiltered"] = $iTotalRecords;
        }catch(\Exception $e){
            $records = array();
            $data = [
                'action' => 'Get Master Account Listing',
                'params' => $request->all(),
                'exception'=> $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
        return response()->json($records);
    }

      public function changeSalaryStatus(Request $request){
        try{
            $status = 200;
            foreach($request->txn_ids as $txnId){
                $salaryTxn = PeticashSalaryTransaction::findOrFail($txnId);
                $newStatus = PeticashStatus::where('slug',$request->status)->pluck('id')->first();
                $remark = $request->remark;
                $salaryTxn->update(['peticash_status_id' => $newStatus,'admin_remark' => $remark]);
            }
            $message = 'Peticash Salary Txn Status changed successfully.';
        }catch(\Exception $e){
            $data = [
                'action' => 'Change Salary status',
                'param' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
          return response()->json($message,$status);
    }

    public function changePurchaseStatus(Request $request){
        try{
            $status = 200;
            foreach($request->txn_ids as $txnId){
                $purchaseTxn = PurcahsePeticashTransaction::findOrFail($txnId);
                $newStatus = PeticashStatus::where('slug',$request->status)->pluck('id')->first();
                $remark = $request->remark;
                $purchaseTxn->update(['peticash_status_id' => $newStatus,'admin_remark' => $remark]);
            }
            $message = 'Peticash Purchase Txn Disapproved successfully.';
        }catch(\Exception $e){
            $data = [
                'action' => 'Change Salary status',
                'param' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
        return response()->json($message,$status);
    }

    public function getManageViewPeticashManagement(Request $request){
        try{
            return view('peticash.peticash-management.manage');
        }catch(\Exception $e){
            $data = [
                'action' => 'Get Peticash Management view',
                'exception' => $e->getMessage(),
                'params' => $request->all()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function masterAccountListing(Request $request){
        try{
            $masterAccountData = PeticashSiteTransfer::where('project_site_id','=', 0)->orderBy('created_at','desc')->get()->toArray();;
            // Here We are considering (project_site_id = 0) => It's Master Peticash Account
            $iTotalRecords = count($masterAccountData);
            $records = array();
            $records['data'] = array();
            $end = $request->length < 0 ? count($masterAccountData) : $request->length;
            for($iterator = 0,$pagination = $request->start; $iterator < $end && $pagination < count($masterAccountData); $iterator++,$pagination++ ){
                $records['data'][$iterator] = [
                    $masterAccountData[$pagination]['id'],
                    User::findOrFail($masterAccountData[$pagination]['received_from_user_id'])->toArray()['first_name']." ".User::findOrFail($masterAccountData[$pagination]['received_from_user_id'])->toArray()['last_name'],
                    User::findOrFail($masterAccountData[$pagination]['user_id'])->toArray()['first_name']." ".User::findOrFail($masterAccountData[$pagination]['user_id'])->toArray()['last_name'],
                    $masterAccountData[$pagination]['amount'],
                    PaymentType::findOrFail($masterAccountData[$pagination]['payment_id'])->toArray()['name'],
                    $masterAccountData[$pagination]['remark'],
                    date('d M Y',strtotime($masterAccountData[$pagination]['date'])),
                    '<div class="btn-group">
                        <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                            Actions
                            <i class="fa fa-angle-down"></i>
                        </button>
                        <ul class="dropdown-menu pull-left" role="menu">
                            <li>
                                <a href="/peticash/master-peticash-account/editpage/'.$masterAccountData[$pagination]['id'].'">
                                <i class="icon-docs"></i> Edit </a>
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
                'action' => 'Get Master Account Listing',
                'params' => $request->all(),
                'exception'=> $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
        return response()->json($records);
    }

    public function sitewiseAccountListing(Request $request){
        try{

            if($request->has('search_name')){
                $projectSites = ProjectSite::where('name','ilike','%'.$request->search_name.'%')->orderBy('name','asc')->pluck('id');
                $sitewiseAccountData = PeticashSiteTransfer::where('project_site_id','!=', 0)->whereIn('project_site_id',$projectSites)->orderBy('created_at','desc')->get()->toArray();;
            }else{
                $sitewiseAccountData = PeticashSiteTransfer::where('project_site_id','!=', 0)->orderBy('created_at','desc')->get()->toArray();;
            }
            // Here We are considering (project_site_id = 0) => It's Master Peticash Account
            $iTotalRecords = count($sitewiseAccountData);
            $records = array();
            $records['data'] = array();
            $end = $request->length < 0 ? count($sitewiseAccountData) : $request->length;
            for($iterator = 0,$pagination = $request->start; $iterator < $end && $pagination < count($sitewiseAccountData); $iterator++,$pagination++ ){
                $records['data'][$iterator] = [
                    $sitewiseAccountData[$pagination]['id'],
                    User::findOrFail($sitewiseAccountData[$pagination]['received_from_user_id'])->toArray()['first_name']." ".User::findOrFail($sitewiseAccountData[$pagination]['received_from_user_id'])->toArray()['last_name'],
                    User::findOrFail($sitewiseAccountData[$pagination]['user_id'])->toArray()['first_name']." ".User::findOrFail($sitewiseAccountData[$pagination]['user_id'])->toArray()['last_name'],
                    ProjectSite::findOrFail($sitewiseAccountData[$pagination]['project_site_id'])->toArray()['name'],
                    $sitewiseAccountData[$pagination]['amount'],
                    PaymentType::findOrFail($sitewiseAccountData[$pagination]['payment_id'])->toArray()['name'],
                    $sitewiseAccountData[$pagination]['remark'],
                    date('d M Y',strtotime($sitewiseAccountData[$pagination]['date'])),
                    '<div class="btn-group">
                        <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                            Actions
                            <i class="fa fa-angle-down"></i>
                        </button>
                        <ul class="dropdown-menu pull-left" role="menu">
                            <li>
                                <a href="/peticash/sitewise-peticash-account/editpage/'.$sitewiseAccountData[$pagination]['id'].'">
                                <i class="icon-docs"></i> Edit </a>
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
                'action' => 'Get Master Account Listing',
                'params' => $request->all(),
                'exception'=> $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
        return response()->json($records);
    }

    public function getProjects(Request $request, $client){
        try{
            $status = 200;
            if ($client == 0) {
                $projectOptions[] = '<option value="0">ALL</option>';
            } else {
                $approvedQuotationStatus = QuotationStatus::where('slug','approved')->first();
                $projectSiteIds = Quotation::where('quotation_status_id',$approvedQuotationStatus['id'])->pluck('project_site_id')->toArray();
                $projectIds = ProjectSite::whereIn('id',$projectSiteIds)->pluck('project_id')->toArray();
                $projects = Project::where('client_id',$client)->whereIn('id',$projectIds)->get()->toArray();
                $projectOptions = array();
                for($i = 0 ; $i < count($projects); $i++){
                    $projectOptions[] = '<option value="'.$projects[$i]['id'].'"> '.$projects[$i]['name'].' </option>';
                }
            }
        }catch (\Exception $e){
            $projectOptions = array();
            $status = 500;
            $data = [
                'actions' => 'Get Project from client',
                'params' => $request->all(),
                'exception' => $e->getMessage(),
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
        return response()->json($projectOptions,$status);
    }

    public function getProjectSites(Request $request,$project){
        try{
            $status = 200;
            if ($project == 0) {
                $projectSitesOptions[] = '<option value="0">ALL</option>';
            } else {
                $projectSites = ProjectSite::where('project_id', $project)->get()->toArray();
                $projectSitesOptions = array();
                for($i = 0 ; $i < count($projectSites); $i++){
                    $projectSitesOptions[] = '<option value="'.$projectSites[$i]['id'].'"> '.$projectSites[$i]['name'].' </option>';
                }
            }
        }catch (\Exception $e){
            $projectSitesOptions = array();
            $status = 500;
            $data = [
                'actions' => 'Get Project Site',
                'params' => $request->all(),
                'exception' => $e->getMessage(),
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
        return response()->json($projectSitesOptions,$status);
    }

    public function getSalaryStats(Request $request){
        try{
            $status = 200;
            $stats = array();
            $sitesLbl = "All Sites";
            if($request->site_id == 0) {
                $stats['allocated_amt']  = PeticashSiteTransfer::where('project_site_id','!=',0)->sum('amount');
                $stats['salary_amt'] = PeticashSalaryTransaction::where('peticash_transaction_type_id',PeticashTransactionType::where('slug','salary')->pluck('id'))->where('project_site_id','!=',0)->sum('payable_amount');
                $stats['advance_amt'] = PeticashSalaryTransaction::where('peticash_transaction_type_id',PeticashTransactionType::where('slug','advance')->pluck('id'))->where('project_site_id','!=',0)->sum('amount');
                $stats['purchase_amt'] = PurcahsePeticashTransaction::whereIn('peticash_transaction_type_id', PeticashTransactionType::where('type','=','PURCHASE')->pluck('id'))->where('project_site_id','!=',0)->sum('bill_amount');
                $stats['pending_amt'] = $stats['allocated_amt'] - ($stats['salary_amt'] + $stats['advance_amt'] + $stats['purchase_amt'] );
                $stats['site_name'] = $sitesLbl;
            } else {
                $stats['allocated_amt']  = PeticashSiteTransfer::where('project_site_id','=',$request->site_id)->sum('amount');
                $stats['salary_amt'] = PeticashSalaryTransaction::where('peticash_transaction_type_id',PeticashTransactionType::where('slug','salary')->pluck('id'))->where('project_site_id','=',$request->site_id)->sum('payable_amount');
                $stats['advance_amt'] = PeticashSalaryTransaction::where('peticash_transaction_type_id',PeticashTransactionType::where('slug','advance')->pluck('id'))->where('project_site_id','=',$request->site_id)->sum('amount');
                $stats['purchase_amt'] = PurcahsePeticashTransaction::whereIn('peticash_transaction_type_id', PeticashTransactionType::where('type','=','PURCHASE')->pluck('id'))->where('project_site_id','=',$request->site_id)->sum('bill_amount');
                $stats['pending_amt'] = $stats['allocated_amt'] - ($stats['salary_amt'] + $stats['advance_amt'] + $stats['purchase_amt'] );
                $stats['site_name'] = ProjectSite::findorfail($request->site_id)['name'];
            }
        } catch (\Exception $e) {
            $stats = array();
            $status = 500;
            $data = [
                'actions' => 'Get Peticash Salary Stats',
                'params' => $request->all(),
                'exception' => $e->getMessage(),
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
        return response()->json($stats,$status);
    }

}
