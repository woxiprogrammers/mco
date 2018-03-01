<?php

namespace App\Http\Controllers\Peticash;

use App\Asset;
use App\AssetType;
use App\Category;
use App\CategoryMaterialRelation;
use App\Client;
use App\Employee;
use App\GRNCount;
use App\Helper\NumberHelper;
use App\InventoryComponent;
use App\InventoryComponentTransferImage;
use App\InventoryComponentTransfers;
use App\InventoryComponentTransferStatus;
use App\InventoryTransferTypes;
use App\Material;
use App\MaterialRequestComponentTypes;
use App\PaymentType;
use App\PeticashRequestedSalaryTransaction;
use App\PeticashSalaryTransaction;
use App\PeticashSalaryTransactionImages;
use App\PeticashSiteApprovedAmount;
use App\PeticashSiteTransfer;
use App\PeticashStatus;
use App\PeticashTransactionType;
use App\Project;
use App\ProjectSite;
use App\PurcahsePeticashTransaction;
use App\PurchasePeticashTransactionImage;
use App\PurchaseOrderBillPayment;
use App\Quotation;
use App\QuotationMaterial;
use App\QuotationStatus;
use App\Role;
use App\Unit;
use App\User;
use App\UserProjectSiteRelation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

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
                                    <a onclick="detailsPurchaseModal('.$salaryTransactionData[$pagination]['id'].');" href="javascript:void(0);">
                                        <i class="icon-docs"></i> Details
                                    </a>
                                </li>
                               <!-- <li>
                                    <a onclick="openApproveModal('.$salaryTransactionData[$pagination]['id'].');" href="javascript:void(0);">
                                        <i class="icon-tag"></i> Approve / Disapprove
                                    </a>
                                </li>-->
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
                                    <a onclick="detailsPurchaseModal('.$salaryTransactionData[$pagination]['id'].');" href="javascript:void(0);">
                                        <i class="icon-docs"></i> Details
                                    </a>
                                </li>
                                <li>
                                    <a onclick="editPurchaseModal('.$salaryTransactionData[$pagination]['id'].');" href="javascript:void(0);">
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
                                <a onclick="detailsPurchaseModal('.$salaryTransactionData[$pagination]['id'].');" href="javascript:void(0);">
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
                                <a onclick="detailsPurchaseModal('.$salaryTransactionData[$pagination]['id'].');" href="javascript:void(0);">
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
                $unit = '-';
                if ($salaryTransactionData[$pagination]['unit_id'] != null) {
                    $unit = Unit::findOrFail($salaryTransactionData[$pagination]['unit_id'])->toArray()['name'];
                }
                $records['data'][$iterator] = [
                    $salaryTransactionData[$pagination]['id'],
                    $salaryTransactionData[$pagination]['name'],
                    $salaryTransactionData[$pagination]['quantity'],
                    $unit,
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
            $projectSiteId = Session::get('global_project_site');
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
            $ids = PeticashRequestedSalaryTransaction::where('project_site_id',$projectSiteId)->pluck('id');
            $filterFlag = true;

            if ($site_id != 0 && $filterFlag == true) {
                $ids = PeticashRequestedSalaryTransaction::whereIn('id',$ids)->where('project_site_id', $site_id)->pluck('id');
                if(count($ids) <= 0) {
                    $filterFlag = false;
                }
            }

            if ($year != 0 && $filterFlag == true) {
                $ids = PeticashRequestedSalaryTransaction::whereIn('id',$ids)->whereYear('created_at', $year)->pluck('id');
                if(count($ids) <= 0) {
                    $filterFlag = false;
                }
            }

            if ($month != 0 && $filterFlag == true) {
                $ids = PeticashRequestedSalaryTransaction::whereIn('id',$ids)->whereMonth('created_at', $month)->pluck('id');
                if(count($ids) <= 0) {
                    $filterFlag = false;
                }
            }

            if ($emp_id != "" && $filterFlag == true) {
                $ids = PeticashRequestedSalaryTransaction::join('employees','employees.id','=','peticash_requested_salary_transactions.employee_id')
                                            ->whereIn('peticash_requested_salary_transactions.id',$ids)
                                            ->where('employees.employee_id',$emp_id)
                                            ->pluck('peticash_requested_salary_transactions.id');
                if(count($ids) <= 0) {
                    $filterFlag = false;
                }
            }

            if ($status != 0 && $filterFlag == true) {
                $ids = PeticashRequestedSalaryTransaction::whereIn('id',$ids)->where('peticash_status_id', $status)->pluck('id');
                if(count($ids) <= 0) {
                    $filterFlag = false;
                }
            }

            if ($filterFlag) {
                $salaryTransactionData = PeticashRequestedSalaryTransaction::whereIn('id',$ids)->orderBy('id','desc')->get();
            }

            $iTotalRecords = count($salaryTransactionData);
            $records = array();
            $records['data'] = array();
            $end = $request->length < 0 ? count($salaryTransactionData) : $request->length;
            for($iterator = 0,$pagination = $request->start; $iterator < $end && $pagination < count($salaryTransactionData); $iterator++,$pagination++ ){
                $txnStatus = PeticashStatus::findOrFail($salaryTransactionData[$pagination]['peticash_status_id'])->toArray()['slug'];
                switch(strtolower($txnStatus)){
                    case 'pending':
                        $checkbox_enable = '<input type="checkbox" class="salary-transactions" name="salary_txn_ids" value="'.$salaryTransactionData[$pagination]['id'].'">';
                        $user_status = '<td><span class="label label-sm label-warning">'.$txnStatus.' </span></td>';
                        $actionDropDown = '<div class="btn-group">
                            <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                Actions
                                <i class="fa fa-angle-down"></i>
                            </button>
                            <ul class="dropdown-menu pull-left" role="menu">
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
                                <!--<li>
                                <a onclick="detailsSalaryModal('.$salaryTransactionData[$pagination]['id'].');" href="javascript:void(0);">
                                    <i class="icon-docs"></i> Details
                                </a>
                                </li> -->
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
                                <!--<li>
                                <a onclick="detailsSalaryModal('.$salaryTransactionData[$pagination]['id'].');" href="javascript:void(0);">
                                    <i class="icon-docs"></i> Details
                                </a>
                            </li>-->
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
                    $salaryTransactionData[$pagination]->employee->employee_id,
                    $salaryTransactionData[$pagination]->employee->name,
                    $salaryTransactionData[$pagination]->paymentType->name,
                    $salaryTransactionData[$pagination]['amount'],
                    $salaryTransactionData[$pagination]->referenceUser->first_name.' '.$salaryTransactionData[$pagination]->referenceUser->last_name,
                    date('d M Y',strtotime($salaryTransactionData[$pagination]['created_at'])),
                    $salaryTransactionData[$pagination]->projectSite->name,
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
                'action' => 'Get Salary Approval Listing',
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
                $salaryTxn = PeticashRequestedSalaryTransaction::findOrFail($txnId);
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

    public function salaryRequestedChangeStatus(Request $request){
        try{
            $status = 200;
            $approvedAmount = 0;
            $projectSiteWiseAmount = array();
            foreach($request->txn_ids as $txnId){
                $salaryTxn = PeticashRequestedSalaryTransaction::findOrFail($txnId);
                $approvedAmount += $salaryTxn->amount;
                $newStatus = PeticashStatus::where('slug',$request->status)->pluck('id')->first();
                $remark = $request->remark;
                $salaryTxn->update(['peticash_status_id' => $newStatus,'admin_remark' => $remark]);
                if($request->status == 'approved'){
                    if(array_key_exists($salaryTxn->project_site_id,$projectSiteWiseAmount)){
                        $projectSiteWiseAmount[$salaryTxn->project_site_id] += $salaryTxn->amount;
                    }else{
                        $projectSiteWiseAmount[$salaryTxn->project_site_id] = $salaryTxn->amount;
                    }
                }
            }
            foreach($projectSiteWiseAmount as $projectSiteId => $amount){
                $peticashSiteApprovedAmount = PeticashSiteApprovedAmount::where('project_site_id',$projectSiteId)->first();
                if($peticashSiteApprovedAmount == null){
                    $peticashSiteApprovedAmount = PeticashSiteApprovedAmount::create(['project_site_id' => $projectSiteId, 'salary_amount_approved' => $amount]);
                }else{
                    $newAmount = $peticashSiteApprovedAmount->salary_amount_approved + $amount;
                    $peticashSiteApprovedAmount->update(['salary_amount_approved' => $newAmount]);
                }
            }
            $message = 'Peticash Requested Salary Txn Status changed successfully.';
        }catch(\Exception $e){
            $data = [
                'action' => 'Change Requested Salary status',
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
            $user = Auth::user();
            $masterAccountData = PeticashSiteTransfer::where('project_site_id','=', 0)->orderBy('created_at','desc')->get()->toArray();;
            // Here We are considering (project_site_id = 0) => It's Master Peticash Account
            $iTotalRecords = count($masterAccountData);
            $records = array();
            $records['data'] = array();
            $end = $request->length < 0 ? count($masterAccountData) : $request->length;
            for($iterator = 0,$pagination = $request->start; $iterator < $end && $pagination < count($masterAccountData); $iterator++,$pagination++ ){
                $editdata = '';
                if($user->hasPermissionTo('edit-master-account') || ($user->roles[0]->role->slug == 'admin') || ($user->roles[0]->role->slug == 'superadmin')){
                    $editdata =  '<li>
                                <a href="/peticash/master-peticash-account/editpage/'.$masterAccountData[$pagination]['id'].'">
                                <i class="icon-docs"></i> Edit </a>
                            </li>';
                }
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
                           '.$editdata.'
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
            $user = Auth::user();
            if($request->has('search_name')){
                $projectSites = ProjectSite::where('name','ilike','%'.$request->search_name.'%')->orderBy('name','asc')->pluck('id');
                $sitewiseAccountData = PeticashSiteTransfer::where('project_site_id','!=', 0)->whereIn('project_site_id',$projectSites)->orderBy('created_at','desc')->get()->toArray();;
            }else{
                $projectSiteId = Session::get('global_project_site');
                $sitewiseAccountData = PeticashSiteTransfer::where('project_site_id',$projectSiteId)->orderBy('created_at','desc')->get()->toArray();;
            }
            // Here We are considering (project_site_id = 0) => It's Master Peticash Account
            $iTotalRecords = count($sitewiseAccountData);
            $records = array();
            $records['data'] = array();
            $end = $request->length < 0 ? count($sitewiseAccountData) : $request->length;
            for($iterator = 0,$pagination = $request->start; $iterator < $end && $pagination < count($sitewiseAccountData); $iterator++,$pagination++ ){
                $editdata = '';
                if($user->hasPermissionTo('edit-master-account') || ($user->roles[0]->role->slug == 'admin') || ($user->roles[0]->role->slug == 'superadmin')){
                    $editdata = '<li>
                                <a href="/peticash/sitewise-peticash-account/editpage/'.$sitewiseAccountData[$pagination]['id'].'">
                                <i class="icon-docs"></i> Edit </a>
                            </li>';
                }
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
                            '.$editdata.'
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
                'action' => 'Get Sitewise Account Listing',
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
                $stats['salary_amt'] = PeticashSalaryTransaction::
                    where('peticash_transaction_type_id',PeticashTransactionType::where('slug','salary')->pluck('id'))
                    ->where('project_site_id','!=',0)
                    ->where('peticash_status_id',PeticashStatus::where('slug','approved')->pluck('id'))
                    ->sum('payable_amount');
                $stats['advance_amt'] = PeticashSalaryTransaction::
                    where('peticash_transaction_type_id',PeticashTransactionType::where('slug','advance')->pluck('id'))
                    ->where('project_site_id','!=',0)
                    ->where('peticash_status_id',PeticashStatus::where('slug','approved')->pluck('id'))
                    ->sum('amount');
                $stats['purchase_amt'] = PurcahsePeticashTransaction::
                    whereIn('peticash_transaction_type_id', PeticashTransactionType::where('type','=','PURCHASE')->pluck('id'))
                    ->where('project_site_id','!=',0)
                    ->where('peticash_status_id',PeticashStatus::where('slug','approved')->pluck('id'))
                    ->sum('bill_amount');
                $stats['pending_amt'] = $stats['allocated_amt'] - ($stats['salary_amt'] + $stats['advance_amt'] + $stats['purchase_amt'] );
                $stats['site_name'] = $sitesLbl;
            } else {
                $stats['allocated_amt']  = PeticashSiteTransfer::where('project_site_id','=',$request->site_id)->sum('amount');
                $stats['salary_amt'] = PeticashSalaryTransaction::
                    where('peticash_transaction_type_id',PeticashTransactionType::where('slug','salary')->pluck('id'))
                    ->where('project_site_id','=',$request->site_id)
                    ->where('peticash_status_id',PeticashStatus::where('slug','approved')->pluck('id'))
                    ->sum('payable_amount');
                $stats['advance_amt'] = PeticashSalaryTransaction::
                    where('peticash_transaction_type_id',PeticashTransactionType::where('slug','advance')->pluck('id'))
                    ->where('project_site_id','=',$request->site_id)
                    ->where('peticash_status_id',PeticashStatus::where('slug','approved')->pluck('id'))
                    ->sum('amount');
                $stats['purchase_amt'] = PurcahsePeticashTransaction::
                    whereIn('peticash_transaction_type_id', PeticashTransactionType::where('type','=','PURCHASE')->pluck('id'))
                    ->where('project_site_id','=',$request->site_id)
                    ->where('peticash_status_id',PeticashStatus::where('slug','approved')->pluck('id'))
                    ->sum('bill_amount');
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

    public function getSalaryTransactionDetails(Request $request){
        try{
            $salaryTransactionData = PeticashSalaryTransaction::where('id',$request['txn_id'])->first();
            $data['peticash_transaction_id'] = $salaryTransactionData->id;
            $data['employee_name'] = $salaryTransactionData->employee->name;
            $data['project_site_name'] = $salaryTransactionData->projectSite->name;
            $data['amount'] = $salaryTransactionData->amount;
            $data['payable_amount'] = $salaryTransactionData->payable_amount;
            $data['reference_user_name'] = $salaryTransactionData->referenceUser->first_name.' '.$salaryTransactionData->referenceUser->last_name;
            $data['date'] = date('l, d F Y',strtotime($salaryTransactionData->date));
            $data['days'] = $salaryTransactionData->days;
            $data['remark'] = $salaryTransactionData->remark;
            $data['admin_remark'] = ($salaryTransactionData->admin_remark == null) ? '' : $salaryTransactionData->admin_remark;
            $data['peticash_transaction_type'] = $salaryTransactionData->peticashTransactionType->name;
            $data['peticash_status_name'] = $salaryTransactionData->peticashStatus->name;
            $data['payment_type'] = $salaryTransactionData->paymentType->name;
            $transactionImages = PeticashSalaryTransactionImages::where('peticash_salary_transaction_id',$request['txn_id'])->get();
            if(count($transactionImages) > 0){
                $data['list_of_images'] = $this->getUploadedImages($transactionImages,$request['txn_id']);
            }else{
                $data['list_of_images'][0]['image_url'] = null;
            }
            $status = 200;
        }catch(\Exception $e){
            $message = $e->getMessage();
            $status = 500;
            $data = [
                'action' => 'Get Salary Transaction Listing',
                'exception' => $e->getMessage(),
                'params' => $request->all()
            ];
            Log::critical(json_encode($data));
        }

        return response()->json($data,$status);
    }

    public function getUploadedImages($transactionImages,$transactionId){
        $iterator = 0;
        $images = array();
        $sha1SalaryTransactionId = sha1($transactionId);
        $imageUploadPath = env('PETICASH_SALARY_TRANSACTION_IMAGE_UPLOAD').$sha1SalaryTransactionId;
        foreach($transactionImages as $index => $image){
            $images[$iterator]['image_url'] = $imageUploadPath.DIRECTORY_SEPARATOR.$image->name;
            $iterator++;
        }
        return $images;
    }

    public function getPurchaseTransactionDetails(Request $request){
        try{
            $purchaseTransactionData = PurcahsePeticashTransaction::where('id',$request['txn_id'])->first();
            $data['peticash_transaction_id'] = $purchaseTransactionData->id;
            $data['name'] = $purchaseTransactionData->name;
            $data['project_site_name'] = $purchaseTransactionData->projectSite->name;
            $data['grn'] = $purchaseTransactionData->grn;
            $data['date'] = date('l, d F Y',strtotime($purchaseTransactionData->date));
            $data['source_name'] = $purchaseTransactionData->source_name;
            $data['peticash_transaction_type'] = $purchaseTransactionData->peticashTransactionType->name;
            $data['component_type'] = $purchaseTransactionData->componentType->name;
            $component_type_slug = $purchaseTransactionData->componentType->slug;
            $catdata = "";
            if($component_type_slug == "new-material") {
                $categories = Category::where('is_miscellaneous',true)->get(['id','name'])->toArray();
                $catdata = "<select class='form-control' id='edit_category' name='edit_category'>";
                foreach ($categories as $cat) {
                    $catdata = $catdata."<option value='".$cat['id']."'>".$cat['name']."</option>";
                }
                $catdata = $catdata."<select>";
            }
            $data['categorydata'] = $catdata;
            $data['quantity'] = $purchaseTransactionData->quantity;
            $unit = "-";
            $unit_id = "";
            if($purchaseTransactionData->unit) {
                $unit = $purchaseTransactionData->unit->name;
                $unit_id = $purchaseTransactionData->unit->id;
            }
            $data['unit_id'] = $unit_id;
            $data['component_type_slug'] = $component_type_slug;
            $data['unit_name'] = $unit;
            $data['bill_number'] = ($purchaseTransactionData->bill_number != null) ? $purchaseTransactionData->bill_number : '';
            $data['bill_amount'] = ($purchaseTransactionData->bill_amount != null) ? $purchaseTransactionData->bill_amount : '';
            $data['vehicle_number'] = ($purchaseTransactionData->vehicle_number != null) ? $purchaseTransactionData->vehicle_number : '';
            $data['in_time'] = ($purchaseTransactionData->in_time != null) ? $purchaseTransactionData->in_time : '';
            $data['out_time'] = ($purchaseTransactionData->out_time) ? $purchaseTransactionData->out_time : '';
            $data['reference_number'] = ($purchaseTransactionData->reference_number != null) ? $purchaseTransactionData->reference_number : '';
            $data['payment_type'] = ($purchaseTransactionData->payment_type_id != null) ? $purchaseTransactionData->paymentType->name : '';
            $data['peticash_status_name'] = $purchaseTransactionData->peticashStatus->name;
            $data['remark'] = ($purchaseTransactionData->remark != null) ? $purchaseTransactionData->remark : '' ;
            $data['admin_remark'] = ($purchaseTransactionData->admin_remark == null) ? '' : $purchaseTransactionData->admin_remark;
            $transactionImages = PurchasePeticashTransactionImage::where('purchase_peticash_transaction_id',$purchaseTransactionData->id)->get();
            if(count($transactionImages) > 0){
                $data['list_of_images'] = $this->getUploadedImages($transactionImages,$purchaseTransactionData->id);
            }else{
                $data['list_of_images'][0]['image_url'] = null;
            }
            $status = 200;
        }catch(\Exception $e){
            $status = 500;
            $data = [
                'action' => 'Get Purchase Transaction Detail',
                'exception' => $e->getMessage(),
                'params' => $request->all()
            ];
            Log::critical(json_encode($data));
        }
        return response()->json($data,$status);
    }

    public function approvePurchaseAjaxRequest(Request $request) {
        try {

            $now = Carbon::now();
            $user = Auth::user();
            if($request->comp_type == MaterialRequestComponentTypes::where('slug','new-material')->pluck('slug')->first()) {
                $materialData['name'] = ucwords(trim($request->mat_name));
                $categoryMaterialData['category_id'] = $request->category_id;
                $materialData['rate_per_unit'] = round($request->rate_per_unit,3);
                $materialData['unit_id'] = $request->unit_id;
                $materialData['is_active'] = (boolean)1;
                $materialData['created_at'] = $now;
                $materialData['updated_at'] = $now;
                $material = Material::create($materialData);
                $categoryMaterialData['material_id'] = $material['id'];
                CategoryMaterialRelation::create($categoryMaterialData);
                $approvedQuotationId = Quotation::where('quotation_status_id', QuotationStatus::where('slug','approved')->pluck('id')->first())->pluck('id');
                foreach ($approvedQuotationId as $quotationId) {
                    $quotMaterialData = array();
                    $quotMaterialData['material_id'] = $material['id'];
                    $quotMaterialData['rate_per_unit'] = round($request->rate_per_unit,3);
                    $quotMaterialData['unit_id'] = $request->unit_id;
                    $quotMaterialData['quantity'] = $request->qty;
                    $quotMaterialData['is_client_supplied'] = (boolean)0;
                    $quotMaterialData['created_at'] = $now;
                    $quotMaterialData['updated_at'] = $now;
                    $quotMaterialData['quotation_id'] = $quotationId;
                    QuotationMaterial::create($quotMaterialData);
                }
            } elseif ($request->comp_type == MaterialRequestComponentTypes::where('slug','new-asset')->pluck('slug')->first()) {
                $assetData['name'] = ucwords(trim($request->mat_name));
                $assetData['is_active'] = (boolean)1;
                $assetData['created_at'] = $now;
                $assetData['updated_at'] = $now;
                $assetData['asset_types_id'] = AssetType::where('slug','other')->pluck('id')->first();
                Asset::create($assetData);
            }
            $purchaseTxnData = PurcahsePeticashTransaction::findOrFail($request->txn_id)->toArray();
            $project_site_id = $purchaseTxnData['project_site_id'];
            $materialComponentSlug = MaterialRequestComponentTypes::where('id',$purchaseTxnData['component_type_id'])->pluck('slug')->first();
            $alreadyPresent = InventoryComponent::where('name','ilike',$purchaseTxnData['name'])->where('project_site_id',$project_site_id)->first();
            if($alreadyPresent != null){
                $inventoryComponentId = $alreadyPresent['id'];
            } else {
                if($materialComponentSlug == 'quotation-material' || $materialComponentSlug == 'new-material' || $materialComponentSlug == 'structure-material'){
                    $inventoryData['is_material'] = true;
                    $inventoryData['reference_id']  = Material::where('name','ilike',$purchaseTxnData['name'])->pluck('id')->first();
                }else{
                    $inventoryData['is_material'] = false;
                    $inventoryData['reference_id']  =  Asset::where('name','ilike',$purchaseTxnData['name'])->pluck('id')->first();
                }
                $inventoryData['name'] = $purchaseTxnData['name'];
                $inventoryData['project_site_id'] = $project_site_id;
                $inventoryData['opening_stock'] = 0;
                $inventoryData['created_at'] = $now;
                $inventoryData['updated_at'] = $now;
                $inventoryComponentId = InventoryComponent::insertGetId($inventoryData);
            }

            $transferData['inventory_component_id'] = $inventoryComponentId;
            $name = InventoryTransferTypes::where('slug', PeticashTransactionType::where('id',$purchaseTxnData['peticash_transaction_type_id'])->pluck('slug')->first())->pluck('slug')->first();
            $type = 'IN';
            $transferData['quantity'] = $purchaseTxnData['quantity'];
            $transferData['unit_id'] = $purchaseTxnData['unit_id'];
            $transferData['date'] = $purchaseTxnData['created_at'];
            $transferData['in_time'] = $purchaseTxnData['in_time'];
            $transferData['out_time'] = $purchaseTxnData['out_time'];
            $transferData['vehicle_number'] = $purchaseTxnData['vehicle_number'];
            $transferData['bill_number'] = $purchaseTxnData['bill_number'];
            $transferData['bill_amount'] = $purchaseTxnData['bill_amount'];
            $transferData['remark'] = $purchaseTxnData['remark'];
            $transferData['source_name'] = $purchaseTxnData['source_name'];
            $transferData['grn'] = $purchaseTxnData['grn'];
            $transferData['user_id'] = $user['id'];
            $transferData['inventory_component_transfer_status_id'] = InventoryComponentTransferStatus::where('slug','approved')->pluck('id')->first();
            $createdTransferId = $this->createInventoryTransferComponent($transferData, $name, $type);
            $transferData['images'] = array();
            $purchaseOrderBillImages = PurchasePeticashTransactionImage::where('purchase_peticash_transaction_id',$request->txn_id)->get();
            $sha1InventoryComponentId = sha1($inventoryComponentId);
            $sha1InventoryTransferId = sha1($createdTransferId);
            $sha1PurchaseOrderId = sha1($request->txn_id);
            foreach ($purchaseOrderBillImages as $key => $image){
                $tempUploadFile = public_path().env('PETICASH_PURCHASE_TRANSACTION_IMAGE_UPLOAD').$sha1PurchaseOrderId.DIRECTORY_SEPARATOR.$image['name'];
                $imageUploadNewPath = public_path().env('INVENTORY_TRANSFER_IMAGE_UPLOAD').$sha1InventoryComponentId.DIRECTORY_SEPARATOR.'transfers'.DIRECTORY_SEPARATOR.$sha1InventoryTransferId;
                if(!file_exists($imageUploadNewPath)) {
                    File::makeDirectory($imageUploadNewPath, $mode = 0777, true, true);
                }
                $imageUploadNewPath .= DIRECTORY_SEPARATOR.$image['name'];
                File::copy($tempUploadFile,$imageUploadNewPath);
                InventoryComponentTransferImage::create(['name' => $image['name'],'inventory_component_transfer_id' => $createdTransferId]);
            }

            //Purchase Transaction Approval
            $purchaseTxn = PurcahsePeticashTransaction::findOrFail($request->txn_id);
            $newStatus = PeticashStatus::where('slug',$request->status)->pluck('id')->first();
            $remark = $request->admin_remark;
            $purchaseTxn->update(['peticash_status_id' => $newStatus,'admin_remark' => $remark]);
            $status = 200;
            $message = 'Purchase request approved successfully.';
        } catch(\Exception $e) {
            $message = "Purchase request not approved successfully.";
            $data = [
                'action' => 'Approve Purchase Request',
                'exception' => $e->getMessage(),
                'params' => $request->all()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
        return response()->json($message, $status);
    }

    public function createInventoryTransferComponent($request,$name,$type){
        try{
            $inventoryComponentTransferData = $request;
            $selectedTransferType = InventoryTransferTypes::where('slug',$name)->where('type','ilike',$type)->first();
            $inventoryComponentTransferData['transfer_type_id'] = $selectedTransferType->id;
            $inventoryComponentTransferData['created_at'] = $inventoryComponentTransferData['updated_at'] = Carbon::now();
            $inventoryComponentTransferDataId = InventoryComponentTransfers::insertGetId($inventoryComponentTransferData);
        }catch (\Exception $e){
            $data = [
                'action' => 'Create Inventory Transfer Component',
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
        }
        return $inventoryComponentTransferDataId;
    }

    public function getSalaryRequestCreateView(Request $request){
        try{
            $projectSiteId = Session::get('global_project_site');
            $employees = Employee::where('project_site_id',$projectSiteId)->get();
            $transactionTypes = PeticashTransactionType::where('type','ilike','PAYMENT')->select('id','name')->get();
            return view('peticash.salary-request.create')->with(compact('employees','transactionTypes'));
        }catch(\Exception $e){
            $data = [
                'action' => 'Get Labour Create View',
                'exception' => $e->getMessage(),
                'request' => $request->all()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function createSalaryRequestCreate(Request $request){
        try{
            $projectSiteId = Session::get('global_project_site');
            $user = Auth::user();
            foreach($request->employee_ids as $employeeId){
                $peticashRequestSalaryTransactionData = [
                    'project_site_id' => $projectSiteId,
                    'employee_id' => $employeeId,
                    'reference_user_id'=> $user->id,
                    'peticash_transaction_type_id' => $request->employee[$employeeId]['payment_type'],
                    'peticash_status_id' => PeticashStatus::where('slug','pending')->pluck('id')->first(),
                    'per_day_wages' => $request->employee[$employeeId]['per_day_wages']
                ];
                $peticashTransactionSlug = PeticashTransactionType::where('id',$request->employee[$employeeId]['payment_type'])->pluck('slug')->first();
                if($peticashTransactionSlug == 'salary'){
                    $peticashRequestSalaryTransactionData['days'] = $request->employee[$employeeId]['days'];
                    $peticashRequestSalaryTransactionData['amount'] = $request->employee[$employeeId]['amount'];
                }elseif($peticashTransactionSlug == 'advance'){
                    $peticashRequestSalaryTransactionData['days'] = null;
                    $peticashRequestSalaryTransactionData['amount'] = $request->employee[$employeeId]['amount'];
                }else{
                    $request->session()->flash('Payment Type is compulsory');
                    return redirect('/peticash/salary-request/create');
                }
                PeticashRequestedSalaryTransaction::create($peticashRequestSalaryTransactionData);
            }
            $request->session()->flash('success', 'Requested Transactions created successfully');
            return redirect('/peticash/peticash-approval-request/manage-salary-list');
        }catch(\Exception $e){
            $data = [
                'action' => 'Create Salary Request',
                'exception' => $e->getMessage(),
                'request' => $request->all()
            ];
            Log::critical(json_encode($data));
        }
    }

    public function getPurchaseManageView(Request $request){
        try{
            $clients = Client::where('is_active', true)->get();
            return view('peticash.peticash-management.purchase.manage')->with(compact('clients'));
        }catch(\Exception $e){
            $data = [
                'action' => 'Get Purchase Management View',
                'exception' => $e->getMessage(),
                'request' => $request->all()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getSalaryManageView(Request $request){
        try{
            $clients = Client::where('is_active', true)->get();
            return view('peticash.peticash-management.salary.manage')->with(compact('clients'));
        }catch(\Exception $e){
            $data = [
                'action' => 'Get Salary Management View',
                'exception' => $e->getMessage(),
                'request' => $request->all()
            ];
            Log::critical(json_encode($data));
        }
    }

    public function purchaseTransactionListing(Request $request){
        try{
            $projectSiteId = Session::get('global_project_site');
            $status = 200;
            $postdata = null;
            $emp_name = null;
            $month = 0;
            $year = 0;
            $postDataArray = array();
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
                $month = $postDataArray['month'];
                $year = $postDataArray['year'];

            }
            $ids = PurcahsePeticashTransaction::where('project_site_id',$projectSiteId)->pluck('id');
            $filterFlag = true;
            if ($request->has('search_employee_id') && $filterFlag == true) {
                $ids = PurcahsePeticashTransaction::join('employees','employees.id','=','peticash_salary_transactions.employee_id')
                    ->whereIn('peticash_salary_transactions.id',$ids)
                    ->where('employees.employee_id','ilike','%'.$request->search_employee_id.'%')
                    ->pluck('peticash_salary_transactions.id');
                if(count($ids) <= 0) {
                    $filterFlag = false;
                }
            }
            if ($year != 0 && $filterFlag == true) {
                $ids = PurcahsePeticashTransaction::whereIn('id',$ids)->whereYear('created_at', $year)->pluck('id');
                if(count($ids) <= 0) {
                    $filterFlag = false;
                }
            }

            if ($month != 0 && $filterFlag == true) {
                $ids = PurcahsePeticashTransaction::whereIn('id',$ids)->whereMonth('created_at', $month)->pluck('id');
                if(count($ids) <= 0) {
                    $filterFlag = false;
                }
            }
            $purchaseTransactionData = array();
            if ($filterFlag) {
                $purchaseTransactionData = PurcahsePeticashTransaction::whereIn('id',$ids)->orderBy('id','desc')->get();
            }
            $iTotalRecords = count($purchaseTransactionData);
            $records = array();
            $records['data'] = array();
            $end = $request->length < 0 ? count($purchaseTransactionData) : $request->length;
            for($iterator = 0,$pagination = $request->start; $iterator < $end && $pagination < count($purchaseTransactionData); $iterator++,$pagination++ ){
                if($purchaseTransactionData[$pagination]->peticashStatus->slug == 'grn-generated'){
                    $button = '<a class="btn blue" href="/peticash/peticash-management/purchase/transaction/edit/'.$purchaseTransactionData[$pagination]->id.'">Edit</a>';;
                }else{
                    $button = '<a class="btn blue" href="javascript:void(0)" onclick="detailsPurchaseModal('.$purchaseTransactionData[$pagination]->id.')">Details</a>';
                }
                $records['data'][] = [
                    $purchaseTransactionData[$pagination]->id,
                    ucwords($purchaseTransactionData[$pagination]->name),
                    $purchaseTransactionData[$pagination]->quantity,
                    $purchaseTransactionData[$pagination]->unit->name,
                    $purchaseTransactionData[$pagination]->bill_amount,
                    $purchaseTransactionData[$pagination]->referenceUser->first_name.' '.$purchaseTransactionData[$pagination]->referenceUser->last_name,
                    date('j M Y',strtotime($purchaseTransactionData[$pagination]->date)),
                    $purchaseTransactionData[$pagination]->peticashStatus->name,
                    $button
                ];
            }
            $records["draw"] = intval($request->draw);
            $records["recordsTotal"] = $iTotalRecords;
            $records["recordsFiltered"] = $iTotalRecords;
        }catch(\Exception $e){
            $data = [
                'action' => 'Get Purchase Transaction Listing',
                'exception' => $e->getMessage(),
                'request' => $request->all()
            ];
            $records = array();
            $status = 500;
            Log::critical(json_encode($data));
        }
        return response()->json($records,$status);
    }

    public function salaryTransactionListing(Request $request){
        try{
            $projectSiteId = Session::get('global_project_site');
            $status = 200;
            $postdata = null;
            $emp_name = null;
            $site_id = 0;
            $month = 0;
            $year = 0;
            $postDataArray = array();
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
                $month = $postDataArray['month'];
                $year = $postDataArray['year'];
            }
            $ids = PeticashSalaryTransaction::where('project_site_id',$projectSiteId)->pluck('id');
            $filterFlag = true;

            if ($request->has('search_employee_id') && $filterFlag == true) {
                $ids = PeticashSalaryTransaction::join('employees','employees.id','=','peticash_salary_transactions.employee_id')
                                    ->whereIn('peticash_salary_transactions.id',$ids)
                                    ->where('employees.employee_id','ilike','%'.$request->search_employee_id.'%')
                                    ->pluck('peticash_salary_transactions.id');
                if(count($ids) <= 0) {
                    $filterFlag = false;
                }
            }
            if ($year != 0 && $filterFlag == true) {
                $ids = PeticashSalaryTransaction::whereIn('id',$ids)->whereYear('created_at', $year)->pluck('id');
                if(count($ids) <= 0) {
                    $filterFlag = false;
                }
            }

            if ($month != 0 && $filterFlag == true) {
                $ids = PeticashSalaryTransaction::whereIn('id',$ids)->whereMonth('created_at', $month)->pluck('id');
                if(count($ids) <= 0) {
                    $filterFlag = false;
                }
            }
            $salaryTransactionData = array();
            if ($filterFlag) {
                $salaryTransactionData = PeticashSalaryTransaction::whereIn('id',$ids)->orderBy('id','desc')->get();
            }
            $iTotalRecords = count($salaryTransactionData);
            $records = array();
            $records['data'] = array();
            $end = $request->length < 0 ? count($salaryTransactionData) : $request->length;
            for($iterator = 0,$pagination = $request->start; $iterator < $end && $pagination < count($salaryTransactionData); $iterator++,$pagination++ ){
                $actionDropDown =  '<button class="btn btn-xs blue"> 
                                                <a href="/peticash/peticash-management/salary/payment-voucher-pdf/'.$salaryTransactionData[$pagination]->id.'" style="color: white">
                                                     PDF 
                                                </a>
                                                <input type="hidden" name="_token">
                                        </button>
                                        <button class="btn btn-xs default "> 
                                                <a href="javascript:void(0);" onclick="detailsSalaryModal('.$salaryTransactionData[$pagination]->id.')" style="color: grey">
                                                    Details 
                                                </a>
                                                <input type="hidden" name="_token">
                                        </button>';
                $records['data'][] = [
                    $salaryTransactionData[$pagination]->id,
                    $salaryTransactionData[$pagination]->employee->employee_id,
                    $salaryTransactionData[$pagination]->employee->name,
                    $salaryTransactionData[$pagination]->peticashTransactionType->name,
                    $salaryTransactionData[$pagination]->amount,
                    $salaryTransactionData[$pagination]->payable_amount,
                    $salaryTransactionData[$pagination]->referenceUser->first_name.' '.$salaryTransactionData[$pagination]->referenceUser->last_name,
                    date('j M Y',strtotime($salaryTransactionData[$pagination]->date)),
                    $salaryTransactionData[$pagination]->projectSite->project->name.' - '.$salaryTransactionData[$pagination]->projectSite->name,
                    $actionDropDown
                ];
            }
            $records["draw"] = intval($request->draw);
            $records["recordsTotal"] = $iTotalRecords;
            $records["recordsFiltered"] = $iTotalRecords;
        }catch(\Exception $e){
            $status = 500;
            $data = [
                'action' => 'Get Salary Transaction Listing',
                'exception' => $e->getMessage(),
                'request' => $request->all()
            ];
            $records = array();
            Log::critical(json_encode($data));
        }
        return response()->json($records,$status);
    }

    public function getPaymentVoucherPdf(Request $request,$salaryTransactionId){
        try{
            $peticashSalaryTransaction = PeticashSalaryTransaction::where('id',$salaryTransactionId)->first();
            $data['project_site'] = $peticashSalaryTransaction->projectSite->name;
            $data['date'] = date('d/m/Y',strtotime($peticashSalaryTransaction->date));
            $data['paid_to'] = $peticashSalaryTransaction->employee->name;
            $data['amount_in_words'] = ucwords(NumberHelper::getIndianCurrency($peticashSalaryTransaction->amount));;
            $data['particulars'] = $peticashSalaryTransaction->remark;
            $data['amount'] = $peticashSalaryTransaction->amount;
            $data['approved_by'] = $peticashSalaryTransaction->referenceUser->first_name.' '.$peticashSalaryTransaction->referenceUser->last_name;
            $pdf = App::make('dompdf.wrapper');
            $pdf->loadHTML(view('peticash.peticash-management.salary.payment-voucher',$data));
            return $pdf->stream();
        }catch(\Exception $e){
            $data = [
                'action' => 'Get Payment Voucher PDF',
                'exception' => $e->getMessage(),
                'params' => $request->all()
            ];
        }
        Log::critical(json_encode($data));
        abort(500,$e->getMessage());

    }

    public function getPurchaseTransactionCreateView(Request $request){
        try{
            $miscellaneousCategories = Category::where('is_miscellaneous',true)->select('id','name')->get();
            $units = Unit::where('is_active',true)->select('id','name')->get()->toArray();
            $unitOptions = '';
            foreach($units as $unit){
                $unitOptions .= '<option value="'.$unit['id'].'">'.$unit['name'].'</option>';
            }
            $noUnit = Unit::where('slug','nos')->select('id','name')->first();
            $nosUnit = '<option value="'.$noUnit['id'].'">'.$noUnit['name'].'</option>';
            return view('peticash.peticash-management.purchase.transaction.create')->with(compact('unitOptions','nosUnit','miscellaneousCategories'));
        }catch(\Exception $e){
            $data = [
                'action' => "Generate GRN Peticash Purchase Transaction",
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function generateGRN(Request $request){
        try{
            $projectSiteId = Session::get('global_project_site');
            $user = Auth::user();
            $status = 200;
            $now = Carbon::now();
            $componentTypeSlug = MaterialRequestComponentTypes::where('id',$request['component_id'])->pluck('slug')->first();
            $materialId = Material::where('name',$request['component_name'])->pluck('id')->first();
            switch ($componentTypeSlug){
                case 'quotation-material':
                    $quotationId = Quotation::where('project_site_id',$projectSiteId)->pluck('id')->first();
                    $purchasePeticashTransaction['reference_id'] = QuotationMaterial::where('quotation_id',$quotationId)->where('material_id',$materialId)->pluck('id')->first();
                    break;

                case 'system-asset':
                    $purchasePeticashTransaction['reference_id'] = Asset::where('name',$request['component_name'])->pluck('id')->first();
                    break;

                case 'new-asset' :
                    $purchasePeticashTransaction['unit_id'] = Unit::where('slug','nos')->pluck('id')->first();
                    $data = $request['component_name'];
                    $purchaseTransaction['reference_id'] = $this->createMaterial($data,'new-asset');
                    break;

                case 'new-material' :
                    $data = $request->only('miscellaneous_category_id','bill_amount','quantity');
                    $data['unit_id'] = $request['unit'];
                    $data['name'] = $request['component_name'];
                    $purchasePeticashTransaction['reference_id'] = $this->createMaterial($data,'new-material');
            }
            $purchasePeticashTransaction = $request->only('source_name','quantity','bill_amount','component_type_id');
            $purchasePeticashTransaction['name'] = $request['component_name'];
            $purchasePeticashTransaction['component_type_id'] = $request['component_id'];
            $purchasePeticashTransaction['project_site_id'] = $projectSiteId;
            $purchasePeticashTransaction['peticash_transaction_type_id'] = PeticashTransactionType::where('slug','hand')->where('type','PURCHASE')->pluck('id')->first();
            $purchasePeticashTransaction['unit_id'] = $request['unit'];
            $purchasePeticashTransaction['bill_number'] = $request['challan_number'];
            $purchasePeticashTransaction['reference_user_id'] = $user->id;
            $purchaseTransaction['payment_type_id'] = PaymentType::where('slug','peticash')->pluck('id')->first();
            $currentDate = Carbon::now();
            $monthlyGrnGeneratedCount = GRNCount::where('month',$currentDate->month)->where('year',$currentDate->year)->pluck('count')->first();
            if($monthlyGrnGeneratedCount != null){
                $serialNumber = $monthlyGrnGeneratedCount + 1;
            }else{
                $serialNumber = 1;
            }
            $purchasePeticashTransaction['grn'] = "GRN".date('Ym').($serialNumber);
            $purchasePeticashTransaction['peticash_status_id'] = PeticashStatus::where('slug','grn-generated')->pluck('id')->first();
            $purchasePeticashTransaction['in_time'] = $now;
            $purchaseTransaction = PurcahsePeticashTransaction::create($purchasePeticashTransaction);
            if($monthlyGrnGeneratedCount != null) {
                GRNCount::where('month', $currentDate->month)->where('year', $currentDate->year)->update(['count' => $serialNumber]);
            }else{
                GRNCount::create(['month'=> $currentDate->month, 'year'=> $currentDate->year,'count' => $serialNumber]);
            }
        }catch(\Exception $e){
            $status = 500;
            $data = [
                'action' => "Generate GRN",
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
        $response = [
            'purchase_transaction' => $purchaseTransaction
        ];
        return response()->json($response,$status);
    }

    public function createMaterial($data,$componentTypeSlug){
        try{
            $now = Carbon::now();
            if($componentTypeSlug == 'new-material') {
                $materialData['name'] = ucwords(trim($data['name']));
                $categoryMaterialData['category_id'] = $data['miscellaneous_category_id'];
                $materialData['rate_per_unit'] = round(($data['bill_amount'] / $data['quantity']),3);
                $materialData['unit_id'] = $data['unit_id'];
                $materialData['is_active'] = (boolean)1;
                $material = Material::create($materialData);
                $categoryMaterialData['material_id'] = $material['id'];
                CategoryMaterialRelation::create($categoryMaterialData);
                $approvedQuotationIds = Quotation::where('quotation_status_id', QuotationStatus::where('slug','approved')->pluck('id')->first())->pluck('id');
                foreach ($approvedQuotationIds as $quotationId) {
                    $quotationMaterialData = array();
                    $quotationMaterialData['material_id'] = $material['id'];
                    $quotationMaterialData['rate_per_unit'] = round(($data['bill_amount'] / $data['quantity']),3);
                    $quotationMaterialData['unit_id'] = $data['unit_id'];
                    $quotationMaterialData['quantity'] = $data['quantity'];
                    $quotationMaterialData['is_client_supplied'] = false;
                    $quotationMaterialData['created_at'] = $now;
                    $quotationMaterialData['updated_at'] = $now;
                    $quotationMaterialData['quotation_id'] = $quotationId;
                    QuotationMaterial::create($quotationMaterialData);
                }
                $reference_id = $material['id'];
            }elseif($componentTypeSlug == 'new-asset'){
                $assetData['name'] = ucwords(trim($data['name']));
                $assetData['is_active'] = true;
                $assetData['asset_types_id'] = AssetType::where('slug','other')->pluck('id')->first();
                $asset = Asset::create($assetData);
                $reference_id = $asset['id'];
            }
        }catch(\Exception $e){
            $data = [
                'action' => 'Create New Material/Asset',
                'exception' => $e->getMessage(),
                'params' => $data
            ];
            Log::critical(json_encode($data));
        }
        return $reference_id;
    }

    public function createPurchaseTransaction(Request $request){
        try{
            $purchasePeticashTransaction = PurcahsePeticashTransaction::where('id',$request['purchase_peticash_transaction_id'])->first();
            $purchasePeticashTransactionData['reference_number'] = $request['reference_number'];
            $purchasePeticashTransactionData['out_time'] = $purchasePeticashTransactionData['date'] = Carbon::now();
            $purchasePeticashTransaction['peticash_status_id'] = PeticashStatus::where('slug','approved')->pluck('id')->first();
            $purchasePeticashTransaction->update($purchasePeticashTransactionData);
            $request->session()->flash('success', 'Trasaction completed successfully.');
            return redirect('/peticash/peticash-management/purchase/manage');
        }catch(\Exception $e){
            $data = [
                'action' => "Create Peticash Purchase Transaction",
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getPurchaseTransactionEditView(Request $request,$purchasePeticashTransactionId){
        try{
            $purchasePeticashTransaction = PurcahsePeticashTransaction::where('id',$purchasePeticashTransactionId)->first();
            return view('peticash.peticash-management.purchase.transaction.edit')->with(compact('purchasePeticashTransaction'));
        }catch(\Exception $e){
            $data = [
                'action' => "Generate GRN Peticash Purchase Transaction",
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

}
