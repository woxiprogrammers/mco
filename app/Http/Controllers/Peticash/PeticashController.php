<?php

namespace App\Http\Controllers\Peticash;

use App\PaymentType;
use App\PeticashSiteTransfer;
use App\ProjectSite;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

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
            $masterAccountData = PeticashSiteTransfer::where('project_site_id','=', 0)->orderBy('created_at','asc')->get()->toArray();;
            // Here We are considering (project_site_id = 0) => It's Master Peticash Account
            $iTotalRecords = count($masterAccountData);
            $records = array();
            $records['data'] = array();
            $end = $request->length < 0 ? count($masterAccountData) : $request->length;
            for($iterator = 0,$pagination = $request->start; $iterator < $end && $pagination < count($masterAccountData); $iterator++,$pagination++ ){
                $records['data'][$iterator] = [
                    $masterAccountData[$pagination]['id'],
                    User::findOrFail($masterAccountData[$pagination]['user_id'])->toArray()['first_name']." ".User::findOrFail($masterAccountData[$pagination]['user_id'])->toArray()['last_name'],
                    User::findOrFail($masterAccountData[$pagination]['received_from_user_id'])->toArray()['first_name']." ".User::findOrFail($masterAccountData[$pagination]['received_from_user_id'])->toArray()['last_name'],
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
                                <a href="/peticash/master-peticash-account/edit/'.$masterAccountData[$pagination]['id'].'">
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
                $sitewiseAccountData = PeticashSiteTransfer::where('project_site_id','!=', 0)->whereIn('project_site_id',$projectSites)->orderBy('created_at','asc')->get()->toArray();;
            }else{
                $sitewiseAccountData = PeticashSiteTransfer::where('project_site_id','!=', 0)->orderBy('created_at','asc')->get()->toArray();;
            }
            // Here We are considering (project_site_id = 0) => It's Master Peticash Account
            $iTotalRecords = count($sitewiseAccountData);
            $records = array();
            $records['data'] = array();
            $end = $request->length < 0 ? count($sitewiseAccountData) : $request->length;
            for($iterator = 0,$pagination = $request->start; $iterator < $end && $pagination < count($sitewiseAccountData); $iterator++,$pagination++ ){
                $records['data'][$iterator] = [
                    $sitewiseAccountData[$pagination]['id'],
                    User::findOrFail($sitewiseAccountData[$pagination]['user_id'])->toArray()['first_name']." ".User::findOrFail($sitewiseAccountData[$pagination]['user_id'])->toArray()['last_name'],
                    User::findOrFail($sitewiseAccountData[$pagination]['received_from_user_id'])->toArray()['first_name']." ".User::findOrFail($sitewiseAccountData[$pagination]['received_from_user_id'])->toArray()['last_name'],
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
                                <a href="/peticash/sitewise-peticash-account/edit/'.$sitewiseAccountData[$pagination]['id'].'">
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
