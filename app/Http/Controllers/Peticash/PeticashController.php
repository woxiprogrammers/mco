<?php

namespace App\Http\Controllers\Peticash;

use App\PaymentType;
use App\PeticashSiteTransfer;
use App\ProjectSite;
use App\Role;
use App\User;
use App\UserProjectSiteRelation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
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
                ->where('permissions.name','=','create-sitewise-account')
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

    public function getManageViewPeticashApproval(Request $request){
        try{
            return view('peticash.peticash-approval.manage');
        }catch(\Exception $e){
            $data = [
                'action' => 'Get Peticash Request Approval view',
                'exception' => $e->getMessage(),
                'params' => $request->all()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
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

}
