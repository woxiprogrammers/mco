<?php

namespace App\Http\Controllers\Labour;

use App\Employee;
use App\EmployeeType;
use App\Labour;
use App\ProjectSite;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class LabourController extends Controller
{
    public function __construct(){
        $this->middleware('custom.auth');
    }

    public function getCreateView(Request $request){
        try{
            $projectSites = ProjectSite::select('id','name')->get()->toArray();
            return view('labour.create')->with(compact('projectSites'));
        }catch (\Exception $e){
            $data = [
                'action' => 'Get Labour Create View',
                'exception' => $e->getMessage(),
                'request' => $request->all()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function createLabour(Request $request){
        try{
            if($request['project_site_id'] == -1){
                $labourData = $request->except('_token','project_site_id');
            }else{
                $labourData = $request->except('_token');
            }
            $labourData['is_active'] = (boolean)false;
            $labourData['employee_type_id'] = EmployeeType::where('slug','labour')->pluck('id')->first();
            Employee::create($labourData);
            $request->session()->flash('success', 'Labour Created successfully.');
            return redirect('/labour/create');
        }catch(\Exception $e){
            $data = [
                'action' => 'Create Labour',
                'exception' => $e->getMessage(),
                'params' => $request->all()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getManageView(Request $request){
        try{
            return view('labour.manage');
        }catch(\Exception $e){
            $data = [
                'action' => 'Get Labour Manage view',
                'exception' => $e->getMessage(),
                'params' => $request->all()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function labourListing(Request $request){
        try{
            $listingData = Employee::get();
            $iTotalRecords = count($listingData);
            $records = array();
            $records['data'] = array();
            $end = $request->length < 0 ? count($listingData) : $request->length;
            for($iterator = 0,$pagination = $request->start; $iterator < $end && $pagination < count($listingData); $iterator++,$pagination++ ){
                if( $listingData[$pagination]['is_active'] == true){
                    $labourStatus = '<td><span class="label label-sm label-success"> Enabled </span></td>';
                    $status = 'Disable';
                }else{
                    $labourStatus = '<td><span class="label label-sm label-danger"> Disabled</span></td>';
                    $status = 'Enable';
                }
                $projectSiteName = ($listingData[$pagination]['project_site_id'] != null) ? $listingData[$pagination]->projectSite->name : '-';
                $records['data'][$iterator] = [
                    $listingData[$pagination]['employee_id'],
                    $listingData[$pagination]['name'],
                    $listingData[$pagination]['mobile'],
                    $listingData[$pagination]['per_day_wages'],
                    $projectSiteName,
                    $labourStatus,
                    '<div class="btn-group">
                        <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                            Actions
                            <i class="fa fa-angle-down"></i>
                        </button>
                        <ul class="dropdown-menu pull-left" role="menu">
                            <li>
                                <a href="/labour/edit/'.$listingData[$pagination]['id'].'">
                                    <i class="icon-docs"></i> Edit </a>
                            </li>
                            <li>
                                <a href="/labour/change-status/'.$listingData[$pagination]['id'].'">
                                    <i class="icon-docs"></i> '.$status.' </a>
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
                'action' => 'Labour Listing',
                'exception' => $e->getMessage(),
                'params' => $request->all()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
        return response()->json($records,200);
    }

    public function changeLabourStatus(Request $request, $labour){
        try{
            $newStatus = (boolean)!$labour->is_active;
            $labour->update(['is_active' => $newStatus]);
            $request->session()->flash('success', 'Labour Status changed successfully.');
            return redirect('/labour/manage');
        }catch(\Exception $e){
            $data = [
                'action' => 'Change Labour status',
                'params' => $request->all(),
                'exception'=> $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getEditView(Request $request,$labour){
        try{
            $projectSites = ProjectSite::select('id','name')->get()->toArray();
            return view('labour.edit')->with(compact('labour','projectSites'));
        }catch(\Exception $e){
            $data = [
                'action' => "Get role edit view",
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function editLabour(Request $request,$labour){
        try{
            if($request['project_site_id'] != -1){
                $updateLabourData = $request->except('_token');
            }else{
                $updateLabourData = $request->except('_token','project_site_id');
            }
            $updateLabourData['employee_type_id'] = EmployeeType::where('slug','labour')->pluck('id')->first();
            Employee::where('id',$labour['id'])->update($updateLabourData);
            $request->session()->flash('success', 'Labour Edited successfully.');
            return redirect('/labour/edit/'.$labour->id);
        }catch(\Exception $e){
            $data = [
                'action' => 'Edit Labour',
                'exception' => $e->getMessage(),
                'params' => $request->all()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }
}
