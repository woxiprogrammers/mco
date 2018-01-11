<?php

namespace App\Http\Controllers\Subcontractor;

use App\DprMainCategory;
use App\Employee;
use App\Project;
use App\ProjectSite;
use App\Quotation;
use App\QuotationStatus;
use App\Subcontractor;
use App\SubcontractorDPRCategoryRelation;
use App\SubcontractorStructure;
use App\SubcontractorStructureType;
use App\Summary;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use App\Client;
use Illuminate\Support\Facades\Session;

class SubcontractorController extends Controller
{
    public function __construct(){
        $this->middleware('custom.auth');
    }

    public function getCreateView(Request $request){
        try{
            return view('subcontractor.create');
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
            $scData = $request->except('_token');
            $scData['is_active'] = (boolean)false;
            Subcontractor::create($scData);
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
            $listingData = Subcontractor::get();
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
                $records['data'][$iterator] = [
                    $listingData[$pagination]['subcontractor_name'],
                    $listingData[$pagination]['company_name'],
                    $listingData[$pagination]['primary_cont_person_name'],
                    $listingData[$pagination]['primary_cont_person_mob_number'],
                    $listingData[$pagination]['escalation_cont_person_name'],
                    $listingData[$pagination]['escalation_cont_person_mob_number'],
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

    public function getEditView(Request $request, $subcontractor){
        try{
            return view('subcontractor.edit')->with(compact('subcontractor'));
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

    public function editSubcontractor(Request $request,$subcontractor){
        try{
            $updateLabourData = $request->except('_token');
            Subcontractor::where('id',$subcontractor['id'])->update($updateLabourData);
            $request->session()->flash('success', 'Subcontractor Edited successfully.');
            return redirect('/subcontractor/edit/'.$subcontractor->id);
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
            $approvedQuotationStatus = QuotationStatus::where('slug','approved')->first();
            $projectSiteIds = Quotation::where('quotation_status_id',$approvedQuotationStatus['id'])->pluck('project_site_id')->toArray();
            $projectIds = ProjectSite::whereIn('id',$projectSiteIds)->pluck('project_id')->toArray();
            $clientIds = Project::whereIn('id',$projectIds)->pluck('client_id')->toArray();
            $clients = Client::whereIn('id',$clientIds)->where('is_active',true)->orderBy('id','asc')->get()->toArray();
            $projectSites = ProjectSite::select('id','name')->get()->toArray();
            $subcontractor = Subcontractor::where('is_active',true)->orderBy('id','asc')->get(['id','subcontractor_name'])->toArray();
            $summary = Summary::where('is_active',true)->orderBy('id','asc')->get(['id','name'])->toArray();
            $ScStrutureTypes = SubcontractorStructureType::orderBy('id','asc')->get(['id','name','slug'])->toArray();
            return view('subcontractor.structure.create')->with(compact('projectSites','clients','subcontractor','summary','ScStrutureTypes'));
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

    public function createSubcontractorStructure(Request $request) {
        try{
            $now = Carbon::now();
            $selectGlobalProjectSite = 0;
            if(Session::has('global_project_site')){
                $selectGlobalProjectSite = Session::get('global_project_site');
            }
            $ScStrutureData = null;
            if($request->structure_type == 'areawise') {
                $structure_type_id = SubcontractorStructureType::where('slug',$request->structure_type)->pluck('id')->toArray()[0];
                $ScStrutureData['project_site_id'] = $selectGlobalProjectSite;
                $ScStrutureData['subcontractor_id'] = $request->subcontractor_id;
                $ScStrutureData['summary_id'] = $request->summary_id;
                $ScStrutureData['sc_structure_type_id'] = $structure_type_id;
                $ScStrutureData['rate'] = $request->rate;
                $ScStrutureData['total_work_area'] = $request->total_work_area;
                $ScStrutureData['description'] = $request->description;
                $ScStrutureData['created_at'] = $now;
                $ScStrutureData['updated_at'] = $now;
                SubcontractorStructure::create($ScStrutureData);
            } else {
                // here we have to do logic of amountwise
            }
            $request->session()->flash('success', 'Subcontractor Structured Created successfully.');
            return redirect('/subcontractor/subcontractor-structure/create');
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

    public function subcontractorStructureListing(Request $request){
        try{
            $selectGlobalProjectSite = 0;
            if(Session::has('global_project_site')){
                $selectGlobalProjectSite = Session::get('global_project_site');
            }
            $listingData = SubcontractorStructure::where('project_site_id', $selectGlobalProjectSite)->get();
            $iTotalRecords = count($listingData);
            $records = array();
            $records['data'] = array();
            $end = $request->length < 0 ? count($listingData) : $request->length;
            for($iterator = 0,$pagination = $request->start; $iterator < $end && $pagination < count($listingData); $iterator++,$pagination++ ){
                //$projectSiteName = ($listingData[$pagination]['project_site_id'] != null) ? $listingData[$pagination]->projectSite->name : '-';
                $total_amount = $listingData[$pagination]['rate']*$listingData[$pagination]['total_work_area'];
                $records['data'][$iterator] = [
                    $listingData[$pagination]->subcontractor->subcontractor_name,
                    $listingData[$pagination]->summary->name,
                    $listingData[$pagination]->contractType->name,
                    $listingData[$pagination]['rate'],
                    $listingData[$pagination]['total_work_area'],
                    $total_amount,
                    //$projectSiteName,
                    date('d M Y',strtotime($listingData[$pagination]['created_at'])),
                    '<div class="btn-group">
                        <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                            Actions
                            <i class="fa fa-angle-down"></i>
                        </button>
                        <ul class="dropdown-menu pull-left" role="menu">
                            <li>
                                <a href="#">
                                    <i class="icon-docs"></i> Edit </a>
                            </li>
                        </ul>
                    </div>'
                    // need to replece for edit functionality : <a href="/subcontractor/subcontractor-structure/edit/'.$listingData[$pagination]['id'].'">
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

    public function getSubcontractorStructureEditView(Request $request, $subcontractor_struct){
        try{
            $subcontractor = Subcontractor::where('is_active',true)->where('id',$subcontractor_struct->subcontractor_id)->orderBy('id','asc')->get(['id','subcontractor_name'])->toArray();
            $summary = Summary::where('is_active',true)->where('id',$subcontractor_struct->summary_id)->orderBy('id','asc')->get(['id','name'])->toArray();
            $approvedQuotationStatus = QuotationStatus::where('slug','approved')->first();
            $projectSiteIds = Quotation::where('quotation_status_id',$approvedQuotationStatus['id'])->pluck('project_site_id')->toArray();
            $projectIds = ProjectSite::whereIn('id',$projectSiteIds)->pluck('project_id')->toArray();
            $clientIds = Project::whereIn('id',$projectIds)->pluck('client_id')->toArray();
            $clients = Client::whereIn('id',$clientIds)->where('is_active',true)->orderBy('id','asc')->get()->toArray();
            $projectSites = ProjectSite::select('id','name')->get()->toArray();
            return view('subcontractor.structure.edit')->with(compact('summary','subcontractor_struct','projectSites','clients','subcontractor'));
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

    public function dprAutoSuggest(Request $request,$keyword){
        try{
            $status = 200;
            $response = array();
            $dprCategories = DprMainCategory::where('name','ilike','%'.$keyword.'%')->where('status', true)->get();
            $iterator = 0;
            foreach ($dprCategories as $dprCategory){
                $response[$iterator]['dpr_category_id'] = $dprCategory['id'];
                $response[$iterator]['dpr_category_name'] = $dprCategory['name'];
                $iterator++;
            }
        }catch (\Exception $e){
            $data = [
                'action' => 'Dpr category auto suggest',
                'keyword' => $keyword,
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            $status = 500;
            $response = array();
        }
        return response()->json($response,$status);
    }

    public function assignDprCategories(Request $request,$subcontractor){
        try{
            SubcontractorDPRCategoryRelation::where('subcontractor_id',$subcontractor->id)->whereNotIn('dpr_main_category_id',$request->dpr_categories)->delete();
            foreach ($request->dpr_categories as $dprCateogoryId){
                $subcontractorDprCategoryRelation = SubcontractorDPRCategoryRelation::where('subcontractor_id',$subcontractor->id)->where('dpr_main_category_id',$dprCateogoryId)->first();
                if($subcontractorDprCategoryRelation == null){
                    $subcontractorDprCategoryRelationData = [
                        'subcontractor_id' => $subcontractor->id,
                        'dpr_main_category_id' => $dprCateogoryId
                    ];
                    SubcontractorDPRCategoryRelation::create($subcontractorDprCategoryRelationData);
                }
            }
            $request->session()->flash('success','DPR categories assinged to subcontractor');
            return redirect('/subcontractor/edit/'.$subcontractor->id);
        }catch (\Exception $e){
            $data = [
                'action' => 'Assign DPR categories to subcontractor',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }
}
