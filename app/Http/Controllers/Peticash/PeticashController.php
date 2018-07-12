<?php

namespace App\Http\Controllers\Peticash;

use App\Asset;
use App\AssetMaintenanceBillPayment;
use App\AssetType;
use App\BankInfo;
use App\Category;
use App\CategoryMaterialRelation;
use App\Client;
use App\Employee;
use App\EmployeeImage;
use App\EmployeeImageType;
use App\EmployeeType;
use App\Helper\NumberHelper;
use App\Http\Controllers\CustomTraits\Notification\NotificationTrait;
use App\Http\Controllers\CustomTraits\PeticashTrait;
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
use App\ProjectSiteAdvancePayment;
use App\PurcahsePeticashTransaction;
use App\PurchaseOrderAdvancePayment;
use App\PurchaseOrderPayment;
use App\PurchasePeticashTransactionImage;
use App\PurchaseOrderBillPayment;
use App\Quotation;
use App\QuotationMaterial;
use App\QuotationStatus;
use App\Role;
use App\SiteTransferBillPayment;
use App\SubcontractorAdvancePayment;
use App\SubcontractorBillTransaction;
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
    use NotificationTrait;
    use PeticashTrait;
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
            $paymenttypes = PaymentType::select(['id','name'])->whereIn('slug',['cheque','neft','rtgs','internet-banking'])->get();
            $users = Role::join('user_has_roles','roles.id','=','user_has_roles.role_id')
                ->join('users','user_has_roles.user_id','=','users.id')
                ->whereIn('roles.slug',['admin','superadmin'])
                ->select('users.id','users.first_name as name')->get()->toArray();
            $banks = BankInfo::where('is_active',true)->select('id','bank_name','balance_amount')->get();
            return view('peticash.master-peticash-account.create')->with(compact('paymenttypes','users','banks'));
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
            $accountData['date'] = $request->date;
            $accountData['received_from_user_id'] = $fromuserid;
            $accountData['remark'] = $request->remark;
            $accountData['project_site_id'] = 0; // VALUE 0 FOR MASTER ACCOUNT
            $accountData['paid_from_slug'] = $request['paid_from_slug'];
            if($request['paid_from_slug'] == 'bank'){
                $bank = BankInfo::where('id',$request['bank_id'])->first();
                if($request['amount'] <= $bank['balance_amount']){

                    $accountData['payment_id'] = $request->payment_type;
                    $accountData['bank_id'] = $request['bank_id'];
                    PeticashSiteTransfer::create($accountData);
                    $bankData['balance_amount'] = $bank['balance_amount'] - $request['amount'];
                    $bank->update($bankData);
                    $request->session()->flash('success', 'Amount Added successfully.');
                    return redirect('peticash/master-peticash-account/manage');
                }else{
                    $request->session()->flash('success','Bank Balance Amount is insufficient for this transaction');
                    return redirect('peticash/master-peticash-account/manage');
                }
            }else{
                PeticashSiteTransfer::create($accountData);
                $request->session()->flash('success', 'Amount Added successfully.');
                return redirect('peticash/master-peticash-account/manage');
            }

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
                $data['sitename'] = Project::join('project_sites','project_sites.project_id','=','projects.id')->where('project_sites.project_id', $txn['project_site_id'])->pluck('projects.name')->first();
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
            $accountData['paid_from_slug'] = $request['paid_from_slug'];
            if($request['paid_from_slug'] == 'bank'){
                $bank = BankInfo::where('id',$request['bank_id'])->first();
                if($request['amount'] <= $bank['balance_amount']){
                    PeticashSiteTransfer::create($accountData);
                    $accountData['payment_id'] = $request->payment_type;
                    $accountData['bank_id'] = $request['bank_id'];
                    $bankData['balance_amount'] = $bank['balance_amount'] - $request['amount'];
                    $bank->update($bankData);
                    $request->session()->flash('success', 'Amount Added successfully.');
                    return redirect('/peticash/sitewise-peticash-account/manage');
                }else{
                    $request->session()->flash('success','Bank Balance Amount is insufficient for this transaction');
                    return redirect('/peticash/sitewise-peticash-account/manage');
                }
            }else{
                $masteraccountAmount = PeticashSiteTransfer::where('project_site_id','=',0)->sum('amount');
                $sitewiseaccountAmount = PeticashSiteTransfer::where('project_site_id','!=',0)->sum('amount');
                $cashAllowedLimit = $masteraccountAmount - $sitewiseaccountAmount;
                if($request['amount'] <= $cashAllowedLimit){
                    PeticashSiteTransfer::create($accountData);
                    $request->session()->flash('success', 'Amount Added successfully.');
                    return redirect('/peticash/sitewise-peticash-account/manage');
                }else{
                    $request->session()->flash('success','Bank Balance Amount is insufficient for this transaction');
                    return redirect('/peticash/sitewise-peticash-account/manage');
                }
            }
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
            $masteraccountAmount = PeticashSiteTransfer::where('project_site_id','=',0)->sum('amount');
            $sitewiseaccountAmount = PeticashSiteTransfer::where('project_site_id','!=',0)->sum('amount');
            $balance = $masteraccountAmount - $sitewiseaccountAmount;
            $user = Auth::user();
            $statistics = $this->getAllSitesStatistics($user);
            return view('peticash.sitewise-peticash-account.manage')->with(compact('masteraccountAmount','sitewiseaccountAmount','balance','statistics'));
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
            $paymenttypes = PaymentType::select(['id','name'])->whereIn('slug',['cheque','neft','rtgs','internet-banking'])->get();
            $users = array();
            $sites = ProjectSite::join('projects','projects.id','=','project_sites.project_id')
                                     ->where('projects.is_active', true)
                                     ->select('project_sites.id as id','projects.name as name')->get()->toArray();
            $banks = BankInfo::where('is_active',true)->select('id','bank_name','balance_amount')->get();
            $masteraccountAmount = PeticashSiteTransfer::where('project_site_id','=',0)->sum('amount');
            $sitewiseaccountAmount = PeticashSiteTransfer::where('project_site_id','!=',0)->sum('amount');
            $cashAllowedLimit = $masteraccountAmount - $sitewiseaccountAmount;
            return view('peticash.sitewise-peticash-account.create')->with(compact('paymenttypes','users','sites','banks','cashAllowedLimit'));
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
            $user = Auth::user();
            $projectSiteId = Session::get('global_project_site');
            $postdata = null;
            $emp_id = "";
            $emp_name = null;
            $status = 0;
            $site_id = 0;
            $month = 0;
            $year = 0;
            $total = 0;
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
                                            ->where('employees.employee_id','ilike',"%".$emp_id."%")
                                            ->pluck('peticash_requested_salary_transactions.id');
                if(count($ids) <= 0) {
                    $filterFlag = false;
                }
            }

            if ($emp_name != null && $emp_name != "" && $filterFlag == true) {
                $ids = PeticashRequestedSalaryTransaction::join('employees','employees.id','=','peticash_requested_salary_transactions.employee_id')
                    ->whereIn('peticash_requested_salary_transactions.id',$ids)
                    ->where('employees.name','ilike',"%".$emp_name."%")
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

            if ($request->has('get_total')) {
                if ($filterFlag) {
                    foreach($salaryTransactionData as $salarytxn) {
                        $total = $total + $salarytxn['amount'];
                    }
                }
                $records['total'] = $total;
            } else {
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
                            if($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin' || $user->customHasPermission('approve-salary-request-handler')){
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
                            }else{
                                $actionDropDown = '<div class="btn-group">
                                                        <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                                            Actions
                                                            <i class="fa fa-angle-down"></i>
                                                        </button>
                                                    </div>';
                            }

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
                        $salaryTransactionData[$pagination]->projectSite->project->name,
                        $user_status,
                        $actionDropDown
                    ];
                }
                $records["draw"] = intval($request->draw);
                $records["recordsTotal"] = $iTotalRecords;
                $records["recordsFiltered"] = $iTotalRecords;
            }
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
            $projectSiteWiseAmount = array();
            foreach($request->txn_ids as $txnId){
                $salaryTxn = PeticashRequestedSalaryTransaction::findOrFail($txnId);
                $newStatus = PeticashStatus::where('slug',$request->status)->pluck('id')->first();
                $remark = $request->remark;
                $salaryTxn->update(['peticash_status_id' => $newStatus,'admin_remark' => $remark]);
                if($request->status == 'approved'){
                    if(array_key_exists($salaryTxn->project_site_id,$projectSiteWiseAmount)){
                        $projectSiteWiseAmount[$salaryTxn->project_site_id] += $salaryTxn->amount;
                    }else{
                        $projectSiteWiseAmount[$salaryTxn->project_site_id] = $salaryTxn->amount;
                    }
                    $projectSite = ProjectSite::where('id',$salaryTxn['project_site_id'])->first();
                    $webTokens = [$salaryTxn->referenceUser->web_fcm_token];
                    $mobileTokens = [$salaryTxn->referenceUser->mobile_fcm_token];
                    $notificationString = $projectSite->project->name.' - '.$projectSite->name.' - Approved payment';
                    $this->sendPushNotification('Manisha Construction', $notificationString,$webTokens,$mobileTokens,'p-s-r-a');
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
            $message = 'Peticash Salary Txn Status changed successfully.';
        }catch(\Exception $e){
            $message = "Something went wrong";
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
                    $projectSite = ProjectSite::where('id',$salaryTxn['project_site_id'])->first();
                    $webTokens = [$salaryTxn->referenceUser->web_fcm_token];
                    $mobileTokens = [$salaryTxn->referenceUser->mobile_fcm_token];
                    $notificationString = $projectSite->project->name.' - '.$projectSite->name.' - Approved payment';
                    $this->sendPushNotification('Manisha Construction', $notificationString,$webTokens,$mobileTokens,'p-s-r-a');
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
            $message = "Something went wrong";
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
            $message = "Something went wrong";
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
            $masterAccountData = PeticashSiteTransfer::where('project_site_id','=', 0)->orderBy('created_at','desc')->get();
            // Here We are considering (project_site_id = 0) => It's Master Peticash Account
            $total = 0;
            if ($request->has('get_total')) {
                $total = $masterAccountData->sum('amount');
                $records['total'] = $total;
            } else {
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
                        ($masterAccountData[$pagination]->paymentType != null) ? ucfirst($masterAccountData[$pagination]->paid_from_slug).' - '.$masterAccountData[$pagination]->paymentType->name : ucfirst($masterAccountData[$pagination]->paid_from_slug),
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
            }

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
                $projectSites = Project::join('project_sites','project_sites.project_id','=','projects.id')->where('projects.name','ilike','%'.$request->search_name.'%')->select('project_sites.id')->get()->toArray();
                $sitewiseAccountData = PeticashSiteTransfer::where('project_site_id','!=', 0)->whereIn('project_site_id',$projectSites)->orderBy('created_at','desc')->get();
            }else{
                $sitewiseAccountData = PeticashSiteTransfer::where('project_site_id','!=', 0)->orderBy('created_at','desc')->get();
            }
            // Here We are considering (project_site_id = 0) => It's Master Peticash Account
            $total = 0;
            if ($request->has('get_total')) {
                $total = $sitewiseAccountData->sum('amount');
                $records['total'] = $total;
            } else {$iTotalRecords = count($sitewiseAccountData);
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
                        Project::join('project_sites','project_sites.project_id','=','projects.id')->where('project_sites.id',$sitewiseAccountData[$pagination]['project_site_id'])->pluck('projects.name')->first(),
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
                $records["recordsFiltered"] = $iTotalRecords;}

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
            $data['payment_type'] = ($salaryTransactionData['payment_type_id'] != null) ? $salaryTransactionData->paymentType->name : '';
            $transactionImages = PeticashSalaryTransactionImages::where('peticash_salary_transaction_id',$request['txn_id'])->get();
            if(count($transactionImages) > 0){
                $imageData = $this->getUploadedImages($transactionImages,$request['txn_id'],'salary');
                foreach ($imageData as $image) {
                    $data['list_of_images'][] = $image['image_url'];
                }
            }else{
                $data['list_of_images']= null;
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

    public function getUploadedImages($transactionImages,$transactionId,$type){
        $iterator = 0;
        $images = array();
        $sha1SalaryTransactionId = sha1($transactionId);
        if ($type == "purchase") {
            $imageUploadPath = url('/').env('PETICASH_PURCHASE_TRANSACTION_IMAGE_UPLOAD').$sha1SalaryTransactionId;
        } else {
            $imageUploadPath = url('/').env('PETICASH_SALARY_TRANSACTION_IMAGE_UPLOAD').$sha1SalaryTransactionId;
        }

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
            $data['payment_type'] = $purchaseTransactionData->paymentType->name;
            $data['peticash_status_name'] = $purchaseTransactionData->peticashStatus->name;
            $data['remark'] = ($purchaseTransactionData->remark != null) ? $purchaseTransactionData->remark : '' ;
            $data['admin_remark'] = ($purchaseTransactionData->admin_remark == null) ? '' : $purchaseTransactionData->admin_remark;
            $transactionImages = PurchasePeticashTransactionImage::where('purchase_peticash_transaction_id',$purchaseTransactionData->id)->get();
            if(count($transactionImages) > 0){
                $imageData = $this->getUploadedImages($transactionImages,$purchaseTransactionData->id,'purchase');
                foreach ($imageData as $image) {
                    $data['list_of_images'][] = $image['image_url'];
                }
            }else{
                $data['list_of_images']= null;
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
                $materialData['name'] = ucwords($request->mat_name);
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
                $assetData['name'] = ucwords($request->mat_name);
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
                $projectSite = ProjectSite::where('id',$projectSiteId)->first();
                $peticashTransactionSlug = PeticashTransactionType::where('id',$request->employee[$employeeId]['payment_type'])->pluck('slug')->first();
                if($peticashTransactionSlug == 'salary'){
                    $peticashRequestSalaryTransactionData['days'] = $request->employee[$employeeId]['days'];
                    $peticashRequestSalaryTransactionData['amount'] = $request->employee[$employeeId]['amount'];
                    $peticashRequestSalaryTransactionData['days'] = null;
                    $peticashRequestSalaryTransactionData['amount'] = $request->employee[$employeeId]['amount'];
                }elseif($peticashTransactionSlug == 'advance'){
                    $peticashRequestSalaryTransactionData['days'] = null;
                    $peticashRequestSalaryTransactionData['amount'] = $request->employee[$employeeId]['amount'];
                }else{
                    $request->session()->flash('Payment Type is compulsory');
                    return redirect('/peticash/salary-request/create');
                }
                PeticashRequestedSalaryTransaction::create($peticashRequestSalaryTransactionData);
                $peticashSalaryRequestApproveTokens = User::join('user_has_permissions','user_has_permissions.user_id','=','users.id')
                    ->join('permissions','permissions.id','=','user_has_permissions.permission_id')
                    ->join('user_project_site_relation','user_project_site_relation.user_id','=','users.id')
                    ->where('permissions.name','ilike','approve-peticash-management')
                    ->where('user_project_site_relation.project_site_id',$projectSiteId)
                    ->select('users.web_fcm_token as web_fcm_token','users.mobile_fcm_token')
                    ->get()->toArray();
                $webTokens = array_column($peticashSalaryRequestApproveTokens,'web_fcm_token');
                $mobileTokens = array_column($peticashSalaryRequestApproveTokens,'mobile_fcm_token');
                $notificationString = $projectSite->project->name.'-'.$projectSite->name.' ';
                $notificationString .= $projectSite->name.' - Required payment';
                $this->sendPushNotification('Manisha Construction',$notificationString,$webTokens,$mobileTokens,'c-p-s-r');
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
            $material_name = null;
            $purchase_by = null;
            $month = 0;
            $year = 0;
            $total = 0;
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
            if ($request->has('search_name')) {
                $material_name = $request['search_name'];
            }

            if ($request->has('purchase_by')) {
                $purchase_by = $request['purchase_by'];
            }


            $ids = PurcahsePeticashTransaction::where('project_site_id',$projectSiteId)->pluck('id');
            $filterFlag = true;
            if ($request->has('search_name') && $filterFlag == true) {
                $ids = PurcahsePeticashTransaction::whereIn('id',$ids)
                    ->where('name','ilike','%'.$material_name.'%')->pluck('id');
                if(count($ids) <= 0) {
                    $filterFlag = false;
                }
            }
            if ($request->has('purchase_by') && $filterFlag == true) {
                $ids = PurcahsePeticashTransaction::
                    join('users','users.id','purchase_peticash_transactions.reference_user_id')
                    ->whereIn('purchase_peticash_transactions.id',$ids)
                    ->whereRaw("CONCAT(users.first_name,' ',users.last_name) ilike '%".$purchase_by."%'")
                    ->pluck('purchase_peticash_transactions.id');
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

            if ($request->has('get_total')) {
                if ($filterFlag) {
                    foreach($purchaseTransactionData as $salarytxn) {
                        $total = $total + $salarytxn['bill_amount'];
                    }
                }
                $records['total'] = $total;
            } else {
                $iTotalRecords = count($purchaseTransactionData);
                $records = array();
                $records['data'] = array();
                $end = $request->length < 0 ? count($purchaseTransactionData) : $request->length;
                for($iterator = 0,$pagination = $request->start; $iterator < $end && $pagination < count($purchaseTransactionData); $iterator++,$pagination++ ){
                    if($purchaseTransactionData[$pagination]->is_voucher_created == true){
                        $voucherButtonText = 'Delete';
                        $voucherStatusTest = 'Yes';
                    }else{
                        $voucherButtonText = 'Create';
                        $voucherStatusTest = 'No';
                    }
                    $records['data'][] = [
                        $purchaseTransactionData[$pagination]->id,
                        ucwords($purchaseTransactionData[$pagination]->name),
                        $purchaseTransactionData[$pagination]->quantity,
                        $purchaseTransactionData[$pagination]->unit->name,
                        $purchaseTransactionData[$pagination]->bill_amount,
                        ucwords($purchaseTransactionData[$pagination]->referenceUser->first_name.' '.$purchaseTransactionData[$pagination]->referenceUser->last_name),
                        date('j M Y',strtotime($purchaseTransactionData[$pagination]->date)),
                        $purchaseTransactionData[$pagination]->projectSite->project->name,
                        '<td><span class="label label-sm label-danger"> '.$voucherStatusTest.' </span></td>',
                        '<button class="btn btn-xs blue"> 
                            <a href="javascript:void(0);" onclick="detailsPurchaseModal('.$purchaseTransactionData[$pagination]->id.')" style="color: white">
                                Details
                            </a>
                        </button>
                        <button class="btn btn-xs default "> 
                            <a href="javascript:void(0);" onclick="changeVoucherStatus('.$purchaseTransactionData[$pagination]->id.')" style="color: grey">
                                '.$voucherButtonText.'
                            </a>
                        </button>'

                    ];
                }
                $records["draw"] = intval($request->draw);
                $records["recordsTotal"] = $iTotalRecords;
                $records["recordsFiltered"] = $iTotalRecords;
            }
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

    public function changeVoucherStatus(Request $request){
        try{
            $purchasePeticashTransaction = PurcahsePeticashTransaction::where('id',$request['purchase_transaction_id'])->first();
            $purchasePeticashTransaction->update(['is_voucher_created' => !($purchasePeticashTransaction->is_voucher_created)]);
            return redirect('/peticash/peticash-management/purchase/manage');
        }catch(\Exception $e){
            $data = [
                'action' => 'Change Voucher Status',
                'exception' => $e->getMessage(),
                'request' => $request->all()
            ];
            $status = 500;
            Log::critical(json_encode($data));
        }
    }

    public function salaryTransactionListing(Request $request){
        try{
            $projectSiteId = Session::get('global_project_site');
            $status = 200;
            $postdata = null;
            $emp_name = null;
            $peticashStatus = null;
            $site_id = 0;
            $month = 0;
            $year = 0;
            $total = 0;
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

            if ($request->has('search_name')) {
                $emp_name = $request->search_name;
            }
            if ($request->has('status')) {
                $peticashStatus = $request->status;
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
            if ($emp_name != null && $emp_name != "" && $filterFlag == true) {
                $ids = PeticashSalaryTransaction::join('employees','employees.id','=','peticash_salary_transactions.employee_id')
                    ->whereIn('peticash_salary_transactions.id',$ids)
                    ->where('employees.name','ilike','%'.$emp_name.'%')
                    ->pluck('peticash_salary_transactions.id');
                if(count($ids) <= 0) {
                    $filterFlag = false;
                }
            }

           if ($peticashStatus != 'all' && $peticashStatus != null && $filterFlag == true) {
                $ids = PeticashSalaryTransaction::join('peticash_transaction_types','peticash_transaction_types.id','=','peticash_salary_transactions.peticash_transaction_type_id')
                    ->whereIn('peticash_salary_transactions.id',$ids)
                    ->where('peticash_transaction_types.slug',$peticashStatus)
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

            if ($request->has('get_total')) {
                if ($filterFlag) {
                    foreach($salaryTransactionData as $salarytxn) {
                        $total = $total + $salarytxn['amount'];
                    }
                }
                $records['total'] = $total;
            } else {
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
                        ucwords($salaryTransactionData[$pagination]->employee->name),
                        $salaryTransactionData[$pagination]->peticashTransactionType->name,
                        $salaryTransactionData[$pagination]->amount,
                        $salaryTransactionData[$pagination]->payable_amount,
                        ucwords($salaryTransactionData[$pagination]->referenceUser->first_name.' '.$salaryTransactionData[$pagination]->referenceUser->last_name),
                        date('j M Y',strtotime($salaryTransactionData[$pagination]->date)),
                        $salaryTransactionData[$pagination]->projectSite->project->name,
                        $actionDropDown
                    ];
                }
                $records["draw"] = intval($request->draw);
                $records["recordsTotal"] = $iTotalRecords;
                $records["recordsFiltered"] = $iTotalRecords;
            }
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
            $data['particulars'] = $peticashSalaryTransaction->remark;
            if ($peticashSalaryTransaction->peticashTransactionType->slug == 'salary'){
                    $data['amount_in_words'] = ucwords(NumberHelper::getIndianCurrency($peticashSalaryTransaction->payable_amount));
                    $data['amount'] = $peticashSalaryTransaction->payable_amount;
                } else {
                    $data['amount_in_words'] = ucwords(NumberHelper::getIndianCurrency($peticashSalaryTransaction->amount));
                    $data['amount'] = $peticashSalaryTransaction->amount;
            }
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

    public function getSalaryCreateView(Request $request){
        try{
            $projectSiteId = Session::get('global_project_site');
            $banks = BankInfo::where('is_active',true)->select('id','bank_name','balance_amount')->get();
            $paymentTypes = PaymentType::select('id','name')->whereIn('slug',['cheque','neft','rtgs','internet-banking'])->get();
            $transactionTypes = PeticashTransactionType::where('type','PAYMENT')->select('id','name','slug')->get();
            $peticashApprovedAmount = PeticashSiteApprovedAmount::where('project_site_id',$projectSiteId)->pluck('salary_amount_approved')->first();
            if (count($peticashApprovedAmount) > 0 && $peticashApprovedAmount != null){
                $approvedAmount = $peticashApprovedAmount;
            }else{
                $approvedAmount = '0';
            }
            return view('peticash.peticash-management.salary.create')->with(compact('banks','paymentTypes','transactionTypes','approvedAmount'));
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

    public function autoSuggest(Request $request,$type,$keyword){
        try{
            $projectSiteId = Session::get('global_project_site');
            $response = array();
            $iterator = 0;
            $employeeDetails = Employee::where('employee_id','ilike','%'.$keyword.'%')->orWhere('name','ilike','%'.$keyword.'%')->whereIn('employee_type_id',EmployeeType::whereIn('slug',['labour','staff','partner'])->pluck('id'))->where('is_active',true)->get()->toArray();
            $data = array();
            foreach($employeeDetails as $key => $employeeDetail){
                $data[$iterator]['employee_id'] = $employeeDetail['id'];
                $data[$iterator]['format_employee_id'] = $employeeDetail['employee_id'];
                $data[$iterator]['employee_name'] = $employeeDetail['name'];
                $data[$iterator]['per_day_wages'] = (int)$employeeDetail['per_day_wages'];
                $data[$iterator]['employee_profile_picture'] = '/assets/global/img/logo.jpg';
                $profilePicTypeId = EmployeeImageType::where('slug','profile')->pluck('id')->first();
                $employeeProfilePic = EmployeeImage::where('employee_id',$employeeDetail['id'])->where('employee_image_type_id',$profilePicTypeId)->first();
                if($employeeProfilePic == null){
                    $data[$iterator]['employee_profile_picture'] = "";
                }else{
                    $employeeDirectoryName = sha1($employeeDetail['id']);
                    $imageUploadPath = env('EMPLOYEE_IMAGE_UPLOAD').DIRECTORY_SEPARATOR.$employeeDirectoryName.DIRECTORY_SEPARATOR.'profile';
                    $data[$iterator]['employee_profile_picture'] = $imageUploadPath.DIRECTORY_SEPARATOR.$employeeProfilePic->name;
                }
                $peticashStatus = PeticashStatus::whereIn('slug',['approved','pending'])->select('id','slug')->get();
                $transactionPendingCount = PeticashSalaryTransaction::where('project_site_id',$projectSiteId)->where('employee_id',$employeeDetail['id'])->where('peticash_status_id',$peticashStatus->where('slug','pending')->pluck('id')->first())->count();
                $data[$iterator]['is_transaction_pending'] = ($transactionPendingCount > 0) ? true : false;
                $salaryTransactions = PeticashSalaryTransaction::where('project_site_id',$projectSiteId)->where('employee_id',$employeeDetail['id'])->where('peticash_status_id',$peticashStatus->where('slug','approved')->pluck('id')->first())->select('id','amount','payable_amount','peticash_transaction_type_id','pf','pt','esic','tds','created_at')->get();
                $paymentSlug = PeticashTransactionType::where('type','PAYMENT')->select('id','slug')->get();
                $advanceSalaryTotal = $salaryTransactions->where('peticash_transaction_type_id',$paymentSlug->where('slug','advance')->pluck('id')->first())->sum('amount');
                $actualSalaryTotal = $salaryTransactions->where('peticash_transaction_type_id',$paymentSlug->where('slug','salary')->pluck('id')->first())->sum('amount');
                $payableSalaryTotal = $salaryTransactions->sum('payable_amount');
                $pfTotal = $salaryTransactions->sum('pf');
                $ptTotal = $salaryTransactions->sum('pt');
                $esicTotal = $salaryTransactions->sum('esic');
                $tdsTotal = $salaryTransactions->sum('tds');
                $data[$iterator]['balance'] = $actualSalaryTotal - $advanceSalaryTotal - $payableSalaryTotal-$pfTotal-$ptTotal-$esicTotal-$tdsTotal ;
                $lastSalaryId = $salaryTransactions->where('peticash_transaction_type_id',$paymentSlug->where('slug','salary')->pluck('id')->first())->sortByDesc('created_at')->pluck('id')->first();
                $advanceAfterLastSalary = $salaryTransactions->where('peticash_transaction_type_id',$paymentSlug->where('slug','advance')->pluck('id')->first())->where('id','>',$lastSalaryId)->sum('amount');
                $data[$iterator]['advance_after_last_salary'] = $advanceAfterLastSalary;
                $iterator++;
            }
            $status = 200;
        }catch(\Exception $e){
            $response = array();
            $data = [
                'action' => 'Peticash salary Auto-suggest',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            $status = 500;
        }
        $response = [
            "data" => $data,
        ];
        return response()->json($data,$status);
    }

    public function createSalaryCreate(Request $request){
        try{
            $projectSiteId = Session::get('global_project_site');
            $user = Auth::user();
            $validationAmount = ($request['transaction_type'] == 'salary') ? $request['payable_amount'] : $request['amount'];
            $bank = BankInfo::where('id',$request['bank_id'])->first();
            $peticashApprovedAmount = PeticashSiteApprovedAmount::where('project_site_id',$projectSiteId)->pluck('salary_amount_approved')->first();
            $approvedAmount = (count($peticashApprovedAmount) > 0 && $peticashApprovedAmount != null) ? $peticashApprovedAmount : 0;
            if($validationAmount > $bank['balance_amount'] && $request['paid_from'] == 'bank'){
                    $request->session()->flash('error', 'Bank Balance Amount is insufficient for this transaction');
                    return redirect('peticash/peticash-management/salary/manage');
            }elseif($validationAmount > $approvedAmount && $request['paid_from'] != 'bank'){
                    $request->session()->flash('error', 'Approved Amount is insufficient for this transaction');
                    return redirect('peticash/peticash-management/salary/manage');
            }
            $salaryData = $request->only('employee_id','amount','date','payable_amount','pf','pt','esic','tds','remark');
            $salaryData['reference_user_id'] = $user['id'];
            $salaryData['project_site_id'] = $projectSiteId;
            $salaryData['peticash_transaction_type_id'] = PeticashTransactionType::where('slug','ilike',$request['transaction_type'])->pluck('id')->first();
            if($request['transaction_type'] == 'salary'){
                $salaryData['days'] = $request['working_days'];
            }else{
                $salaryData['days'] = 0;
            }
            $salaryData['peticash_status_id'] = PeticashStatus::where('slug','approved')->pluck('id')->first();
            $salaryData['created_at'] = $salaryData['updated_at'] = Carbon::now();

            if($request['paid_from'] == 'bank'){
                if($request['amount'] < $bank['balance_amount']){
                    $salaryData['payment_type_id'] = $request['payment_id'];
                    $salaryData['bank_id'] = $request['bank_id'];
                    $salaryTransaction = PeticashSalaryTransaction::create($salaryData);
                    $bankData['balance_amount'] = $bank['balance_amount'] - $request['amount'];
                    $bank->update($bankData);
                }else{
                    $request->session()->flash('error', 'Insufficient Bank Balance');
                    return redirect('peticash/peticash-management/salary/manage');
                }

            }else{
                $salaryTransaction = PeticashSalaryTransaction::create($salaryData);
            }
            $peticashSiteApprovedAmount = PeticashSiteApprovedAmount::where('project_site_id',$projectSiteId)->first();
            $updatedPeticashSiteApprovedAmount = $peticashSiteApprovedAmount['salary_amount_approved'] - $request['amount'];
            $peticashSiteApprovedAmount->update(['salary_amount_approved' => $updatedPeticashSiteApprovedAmount]);
            $officeSiteId = ProjectSite::where('name',env('OFFICE_PROJECT_SITE_NAME'))->pluck('id')->first();
            if($projectSiteId == $officeSiteId){
                $activeProjectSites = ProjectSite::join('projects','projects.id','=','project_sites.project_id')
                    ->where('projects.is_active',true)
                    ->where('project_sites.id','!=',$officeSiteId)->get();
                if($request['transaction_type'] == 'advance'){
                    $distributedSiteWiseAmount =  $salaryTransaction['amount'] / count($activeProjectSites);
                }else{
                    $distributedSiteWiseAmount = ($salaryTransaction['payable_amount'] + $salaryTransaction['pf'] + $salaryTransaction['pt'] + $salaryTransaction['tds'] + $salaryTransaction['esic']) / count($activeProjectSites) ;
                }
                foreach ($activeProjectSites as $key => $projectSite){
                    $distributedSalaryAmount = $projectSite['distributed_salary_amount'] + $distributedSiteWiseAmount;
                    $projectSite->update([
                        'distributed_salary_amount' => $distributedSalaryAmount
                    ]);
                }
            }
            $request->session()->flash('success', 'Salary transaction created successfully');
            return redirect('peticash/peticash-management/salary/manage');
        }catch(\Exception $e){
            $data = [
                'action' => 'Create Salary',
                'exception' => $e->getMessage(),
                'request' => $request->all()
            ];
            Log::critical(json_encode($data));
        }
    }

    public function getCashTransactionManage(Request $request){
        try{
            return view('peticash.peticash-management.cash-transaction.manage');
        }catch(\Exception $e){
            $data = [
                'action' => 'Get Salary Management View',
                'exception' => $e->getMessage(),
                'request' => $request->all()
            ];
            Log::critical(json_encode($data));
        }
    }

    public function getCashTransactionListing(Request $request){
        try{
            $status = 200;
            $projectSiteId = Session::get('global_project_site');
            $search_name = null;
            if ($request->has('search_name')) {
                $search_name = $request->search_name;
            }
            $purchaseOrderAdvancePayments = PurchaseOrderAdvancePayment::join('purchase_orders','purchase_orders.id','purchase_order_advance_payments.purchase_order_id')
                                                                            ->join('vendors','vendors.id','=','purchase_orders.vendor_id')
                                                                            ->join('purchase_requests','purchase_requests.id','=','purchase_orders.purchase_request_id')
                                                                            ->where('purchase_order_advance_payments.paid_from_slug','cash')
                                                                            ->where('purchase_requests.project_site_id',$projectSiteId)
                                                                            ->where('vendors.company','ilike','%'.$search_name.'%')
                                                                            ->select('purchase_order_advance_payments.id as payment_id','purchase_order_advance_payments.amount as amount'
                                                                                ,'purchase_order_advance_payments.created_at as created_at','purchase_requests.project_site_id as project_site_id'
                                                                                ,'vendors.company as name')->get()->toArray();

            $purchaseOrderBillPayments = PurchaseOrderPayment::join('purchase_order_bills','purchase_order_bills.id','=','purchase_order_payments.purchase_order_bill_id')
                                                                ->join('purchase_orders','purchase_orders.id','=','purchase_order_bills.purchase_order_id')
                                                                ->join('vendors','vendors.id','=','purchase_orders.vendor_id')
                                                                ->join('purchase_requests','purchase_requests.id','=','purchase_orders.purchase_request_id')
                                                                ->where('purchase_order_payments.paid_from_slug','cash')
                                                                ->where('vendors.company','ilike','%'.$search_name.'%')
                                                                ->where('purchase_requests.project_site_id',$projectSiteId)
                                                                ->select('purchase_order_payments.id as payment_id','purchase_order_payments.amount as amount'
                                                                    ,'purchase_order_payments.created_at as created_at','purchase_requests.project_site_id as project_site_id'
                                                                    ,'vendors.company as name')->get()->toArray();

            $subcontractorAdvancePayments = SubcontractorAdvancePayment::join('subcontractor','subcontractor.id','=','subcontractor_advance_payments.subcontractor_id')
                                                                            ->where('subcontractor_advance_payments.paid_from_slug','cash')
                                                                            ->where('subcontractor_advance_payments.project_site_id',$projectSiteId)
                                                                            ->where('subcontractor.company_name','ilike','%'.$search_name.'%')
                                                                            ->select('subcontractor_advance_payments.id as payment_id','subcontractor_advance_payments.amount as amount'
                                                                                ,'subcontractor_advance_payments.project_site_id as project_site_id'
                                                                                ,'subcontractor_advance_payments.created_at as created_at'
                                                                                ,'subcontractor.company_name as name')->get()->toArray();

            $projectSiteAdvancePayments = ProjectSiteAdvancePayment::join('project_sites','project_sites.id','=','project_site_advance_payments.project_site_id')
                                                                        ->where('project_site_advance_payments.paid_from_slug','cash')
                                                                        ->where('project_site_advance_payments.project_site_id',$projectSiteId)
                                                                        ->where('project_sites.name','ilike','%'.$search_name.'%')
                                                                        ->select('project_site_advance_payments.id as payment_id'
                                                                        ,'project_site_advance_payments.amount as amount'
                                                                        ,'project_site_advance_payments.created_at as created_at'
                                                                        ,'project_site_advance_payments.project_site_id as project_site_id'
                                                                        ,'project_sites.name as name')->get()->toArray();

            $siteTransferPayments = SiteTransferBillPayment::join('site_transfer_bills','site_transfer_bills.id','=','site_transfer_bill_payments.site_transfer_bill_id')
                                                                ->join('inventory_component_transfers','inventory_component_transfers.id','=','site_transfer_bills.inventory_component_transfer_id')
                                                                ->join('vendors','vendors.id','=','inventory_component_transfers.vendor_id')
                                                                ->join('inventory_components','inventory_components.id','inventory_component_transfers.inventory_component_id')
                                                                ->where('site_transfer_bill_payments.paid_from_slug','cash')
                                                                ->where('inventory_components.project_site_id',$projectSiteId)
                                                                ->where('vendors.company','ilike','%'.$search_name.'%')
                                                                ->select('site_transfer_bill_payments.id as payment_id','site_transfer_bill_payments.amount as amount'
                                                                    ,'site_transfer_bill_payments.created_at as created_at'
                                                                    ,'inventory_components.project_site_id as project_site_id'
                                                                    ,'vendors.company as name')->get()->toArray();

            $assetMaintenancePayments = AssetMaintenanceBillPayment::join('asset_maintenance_bills','asset_maintenance_bills.id','asset_maintenance_bill_payments.asset_maintenance_bill_id')
                                            ->join('asset_maintenance','asset_maintenance.id','=','asset_maintenance_bills.asset_maintenance_id')
                                            ->join('asset_maintenance_vendor_relation','asset_maintenance_vendor_relation.asset_maintenance_id','=','asset_maintenance.id')
                                            ->where('asset_maintenance_vendor_relation.is_approved',true)
                                            ->join('vendors','vendors.id','=','asset_maintenance_vendor_relation.vendor_id')
                                            ->where('asset_maintenance.project_site_id',$projectSiteId)
                                            ->where('vendors.company','ilike','%'.$search_name.'%')
                                            ->where('asset_maintenance_bill_payments.paid_from_slug','cash')
                                            ->select('asset_maintenance_bill_payments.id as payment_id','asset_maintenance_bill_payments.amount as amount'
                                                ,'asset_maintenance_bill_payments.created_at as created_at'
                                                ,'asset_maintenance.project_site_id as project_site_id'
                                                ,'vendors.company as name')->get()->toArray();


            $subcontractorCashBillTransactions = SubcontractorBillTransaction::join('subcontractor_bills','subcontractor_bills.id','=','subcontractor_bill_transactions.subcontractor_bills_id')
                ->join('subcontractor_structure','subcontractor_structure.id','=','subcontractor_bills.sc_structure_id')
                ->where('subcontractor_structure.project_site_id',$projectSiteId)
                ->where('subcontractor_bill_transactions.paid_from_slug','cash')
                ->join('subcontractor','subcontractor.id','=','subcontractor_structure.subcontractor_id')
                ->where('subcontractor.company_name','ilike','%'.$search_name.'%')
                ->select('subcontractor_bill_transactions.id as payment_id','subcontractor_bill_transactions.subtotal as amount'
                    ,'subcontractor_bill_transactions.created_at as created_at'
                    ,'subcontractor_structure.project_site_id as project_site_id'
                    ,'subcontractor.company_name as name')->get()->toArray();

            $cashPaymentData = array_merge($purchaseOrderAdvancePayments,$purchaseOrderBillPayments,$subcontractorAdvancePayments,$projectSiteAdvancePayments,$siteTransferPayments,$assetMaintenancePayments,$subcontractorCashBillTransactions);
            $total = 0;
            if ($request->has('get_total')) {
                foreach($cashPaymentData as $salarytxn) {
                    $total = $total + $salarytxn['amount'];
                }
                $records['total'] = $total;
            } else {
                usort($cashPaymentData, function($a, $b) {
                    return $a['created_at'] < $b['created_at'];
                });
                $iTotalRecords = count($cashPaymentData);
                $records = array();
                $records['data'] = array();
                $end = $request->length < 0 ? count($cashPaymentData) : $request->length;
                for($iterator = 0,$pagination = $request->start; $iterator < $end && $pagination < count($cashPaymentData); $iterator++,$pagination++ ){
                    $records['data'][] = [
                        $iterator+1,
                        ProjectSite::where('id',$cashPaymentData[$pagination]['project_site_id'])->pluck('name')->first(),
                        ucwords($cashPaymentData[$pagination]['name']),
                        $cashPaymentData[$pagination]['amount'],
                        date('j M Y',strtotime($cashPaymentData[$pagination]['created_at'])),
                    ];
                }
                $records["draw"] = intval($request->draw);
                $records["recordsTotal"] = $iTotalRecords;
                $records["recordsFiltered"] = $iTotalRecords;
            }
        }catch(\Exception $e){
            $data = [
                'action' => 'Get Cash Transaction Listing',
                'exception' => $e->getMessage(),
                'request' => $request->all()
            ];
            $records = array();
            $status = 500;
            Log::critical(json_encode($data));
        }
        return response()->json($records,$status);
    }
}
