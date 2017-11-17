<?php

namespace App\Http\Controllers\Subcontractor;

use App\Employee;
use App\Project;
use App\ProjectSite;
use App\Quotation;
use App\QuotationStatus;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use App\Client;

class SubcontractorController extends Controller
{
    public function __construct(){
        $this->middleware('custom.auth');
    }

    public function getCreateView(Request $request){
        try{
            $projectSites = ProjectSite::select('id','name')->get()->toArray();
            return view('subcontractor.create')->with(compact('projectSites'));
        }catch (\Exception $e){
            $data = [
                'action' => 'Get Subcontractor Create View',
                'exception' => $e->getMessage(),
                'request' => $request->all()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function createSubcontractor(Request $request){
        try{
            if($request['project_site_id'] == -1){
                $labourData = $request->except('_token','project_site_id');
            }else{
                $labourData = $request->except('_token');
            }
            $labourData['is_active'] = (boolean)false;
            $labourData['employee_type_id'] = EmployeeType::where('slug','labour')->pluck('id')->first();
            Employee::create($labourData);
            $request->session()->flash('success', 'Subcontractor Created successfully.');
            return redirect('/subcontractor/create');
        }catch(\Exception $e){
            $data = [
                'action' => 'Create Subcontractor',
                'exception' => $e->getMessage(),
                'params' => $request->all()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getManageView(Request $request){
        try{
            return view('subcontractor.manage');
        }catch(\Exception $e){
            $data = [
                'action' => 'Get Subcontractor Manage view',
                'exception' => $e->getMessage(),
                'params' => $request->all()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function subcontractorListing(Request $request){
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
                                <a href="/subcontractor/edit/'.$listingData[$pagination]['id'].'">
                                    <i class="icon-docs"></i> Edit </a>
                            </li>
                            <li>
                                <a href="/subcontractor/change-status/'.$listingData[$pagination]['id'].'">
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
                'action' => 'Subcontractor Listing',
                'exception' => $e->getMessage(),
                'params' => $request->all()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
        return response()->json($records,200);
    }

    public function changeSubcontractorStatus(Request $request, $labour){
        try{
            $newStatus = (boolean)!$labour->is_active;
            $labour->update(['is_active' => $newStatus]);
            $request->session()->flash('success', 'Subcontractor Status changed successfully.');
            return redirect('/subcontractor/manage');
        }catch(\Exception $e){
            $data = [
                'action' => 'Change Subcontractor status',
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
            return view('subcontractor.edit')->with(compact('labour','projectSites'));
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

    public function editSubcontractor(Request $request,$labour){
        try{
            if($request['project_site_id'] != -1){
                $updateLabourData = $request->except('_token');
            }else{
                $updateLabourData = $request->except('_token','project_site_id');
            }
            $updateLabourData['employee_type_id'] = EmployeeType::where('slug','labour')->pluck('id')->first();
            Employee::where('id',$labour['id'])->update($updateLabourData);
            $request->session()->flash('success', 'Subcontractor Edited successfully.');
            return redirect('/subcontractor/edit/'.$labour->id);
        }catch(\Exception $e){
            $data = [
                'action' => 'Edit Subcontractor',
                'exception' => $e->getMessage(),
                'params' => $request->all()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getManageStructureView(Request $request) {
        try{
            $approvedQuotationStatus = QuotationStatus::where('slug','approved')->first();
            $projectSiteIds = Quotation::where('quotation_status_id',$approvedQuotationStatus['id'])->pluck('project_site_id')->toArray();
            $projectIds = ProjectSite::whereIn('id',$projectSiteIds)->pluck('project_id')->toArray();
            $clientIds = Project::whereIn('id',$projectIds)->pluck('client_id')->toArray();
            $clients = Client::whereIn('id',$clientIds)->where('is_active',true)->orderBy('id','asc')->get()->toArray();
            return view('subcontractor.structure.manage')->with(compact('clients'));
        }catch(\Exception $e){
            $data = [
                'action' => 'Get Subcontractor Structure Manage view',
                'exception' => $e->getMessage(),
                'params' => $request->all()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getSubcontractorStructureView(Request $request) {
        try{
            $projectSites = ProjectSite::select('id','name')->get()->toArray();
            return view('subcontractor.structure.create')->with(compact('projectSites'));
        }catch (\Exception $e){
            $data = [
                'action' => 'Get Subcontractor Structure Create View',
                'exception' => $e->getMessage(),
                'request' => $request->all()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function subcontractorStructureListing(Request $request){
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
                                <a href="/subcontractor/subcontractor-structure/edit/'.$listingData[$pagination]['id'].'">
                                    <i class="icon-docs"></i> Edit </a>
                            </li>
                            <!--<li>
                                <a href="/subcontractor/change-status/'.$listingData[$pagination]['id'].'">
                                    <i class="icon-docs"></i> '.$status.' </a>
                            </li>-->
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
                'action' => 'Subcontractor Listing',
                'exception' => $e->getMessage(),
                'params' => $request->all()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
        return response()->json($records,200);
    }

    public function getSubcontractorStructureEditView(Request $request,$labour){
        try{
            $projectSites = ProjectSite::select('id','name')->get()->toArray();
            return view('subcontractor.structure.edit')->with(compact('labour','projectSites'));
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

    public function editSubcontractorStructure(Request $request,$labour){
        try{
            if($request['project_site_id'] != -1){
                $updateLabourData = $request->except('_token');
            }else{
                $updateLabourData = $request->except('_token','project_site_id');
            }
            $updateLabourData['employee_type_id'] = EmployeeType::where('slug','labour')->pluck('id')->first();
            Employee::where('id',$labour['id'])->update($updateLabourData);
            $request->session()->flash('success', 'Subcontractor Edited successfully.');
            return redirect('/subcontractor/edit/'.$labour->id);
        }catch(\Exception $e){
            $data = [
                'action' => 'Edit Subcontractor',
                'exception' => $e->getMessage(),
                'params' => $request->all()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }
}
