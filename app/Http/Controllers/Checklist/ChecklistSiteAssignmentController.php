<?php

namespace App\Http\Controllers\Checklist;

use App\ChecklistCategory;
use App\ChecklistCheckpoint;
use App\Client;
use App\Project;
use App\ProjectSite;
use App\ProjectSiteChecklist;
use App\ProjectSiteChecklistCheckpoint;
use App\ProjectSiteChecklistCheckpointImages;
use App\Quotation;
use App\QuotationFloor;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class ChecklistSiteAssignmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('custom.auth');
    }

    public function getManageView(Request $request)
    {
        try {
            return view('checklist.site-assignment.manage');
        } catch (\Exception $e) {
            $data = [
                'action' => "Get Check List manage view",
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getCreateView(Request $request)
    {
        try {
            $clients = Client::where('is_active', true)->select('id','company')->get();
            $mainCategories = ChecklistCategory::whereNull('category_id')->where('is_active', true)->select('id','name')->get();
            return view('checklist.site-assignment.create')->with(compact('mainCategories','clients'));
        } catch (\Exception $e) {
            $data = [
                'action' => "Get check list create view",
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getProjects(Request $request){
        try{
            $projects = Project::where('client_id',$request->client_id)->where('is_active', true)->select('id','name')->get();
            $projectOptions = '<option value="">--Select Project--</option>';
            foreach($projects as $project){
                $projectOptions .= '<option value="'.$project['id'].'">'.$project['name'].'</option>';
            }
            $status = 200;
        }catch(\Exception $e){
            $data = [
                'action' => "Get Projects",
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            $status = 500;
            $projectOptions = '';
        }
        return response()->json($projectOptions,$status);
    }

    public function getProjectSites(Request $request){
        try{
            $projects = ProjectSite::where('project_id',$request->project_id)->select('id','name')->get();
            $projectOptions = '<option value="">--Select Project Site--</option>';
            foreach($projects as $project){
                $projectOptions .= '<option value="'.$project['id'].'">'.$project['name'].'</option>';
            }
            $status = 200;
        }catch(\Exception $e){
            $data = [
                'action' => "Get Project Sites",
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            $status = 500;
            $projectOptions = '';
        }
        return response()->json($projectOptions,$status);
    }

    public function getQuotationFloors(Request $request){
        try{
            $quotationId = Quotation::where('project_site_id', $request->project_site_id)->pluck('id')->first();
            $quotationFloors = QuotationFloor::where('quotation_id',$quotationId)->select('id','name')->get();
            $quotationFloorsOptions = '<option value="">-- Select Quotation Floor --</option>';
            foreach($quotationFloors as $quotationFloor){
                $quotationFloorsOptions .= '<option value="'.$quotationFloor['id'].'">'.$quotationFloor['name'].'</option>';
            }
            $status = 200;
        }catch(\Exception $e){
            $data = [
                'action' => "Get Quotation Floors",
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            $status = 500;
            $quotationFloorsOptions = '';
        }
        return response()->json($quotationFloorsOptions,$status);
    }

    public function getCheckpoints(Request $request,$checklistCategory){
        try{
            return view('partials.checklist.site-assignment.checkpoint-table')->with(compact('checklistCategory'));
        }catch(\Exception $e){
            $data = [
                'action' => "Get Quotation Floors",
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            return response()->json([],500);
        }
    }

    public function siteAssignmentCreate(Request $request){
        try{
            $projectSiteChecklistData = [
                'project_site_id' => $request->project_site_id,
                'title' => $request->title,
                'quotation_floor_id' => $request->quotation_floor_id,
                'detail' => $request->detail,
                'checklist_category_id' => $request->sub_category_id
            ];
            $projectSiteChecklist = ProjectSiteChecklist::create($projectSiteChecklistData);
            $projectSiteChecklistCheckpointData = [
                'project_site_checklist_id' => $projectSiteChecklist->id
            ];
            foreach($request->checkpoint_ids as $checkpoint_id){
                $checkpoint = ChecklistCheckpoint::findOrFail($checkpoint_id);
                $projectSiteChecklistCheckpointData['description'] = $checkpoint->description;
                $projectSiteChecklistCheckpointData['is_remark_required'] = $checkpoint->is_remark_required;
                $projectSiteChecklistCheckpoint = ProjectSiteChecklistCheckpoint::create($projectSiteChecklistCheckpointData);
                $projectSiteChecklistCheckpointImageData = [
                    'project_site_checklist_checkpoint_id' => $projectSiteChecklistCheckpoint->id
                ];
                foreach($checkpoint->checklistCheckpointsImages as $checklistCheckpointsImage){
                    $projectSiteChecklistCheckpointImageData['caption'] = $checklistCheckpointsImage['caption'];
                    $projectSiteChecklistCheckpointImageData['is_required'] = $checklistCheckpointsImage['is_required'];
                    $projectSiteChecklistCheckpointImage = ProjectSiteChecklistCheckpointImages::create($projectSiteChecklistCheckpointImageData);
                }
            }
            $request->session()->flash('success', 'Checklist assigned to Project Site successfully');
            return redirect('/checklist/site-assignment/manage');
        }catch(\Exception $e){
            $data = [
                'action' => 'Create Checklist Site assignment',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function siteAssignmentListing(Request $request){
        try{
            $siteAssignmentData = ProjectSiteChecklist::orderBy('id','desc')->get();
            $records = array();
            $records["draw"] = intval($request->draw);
            $records["recordsFiltered"] = $records["recordsTotal"] = count($siteAssignmentData);
            $records['data'] = array();
            $end = $request->length < 0 ? count($siteAssignmentData) : $request->length;
            for($iterator = 0,$pagination = $request->start; $iterator < $end && $pagination < count($siteAssignmentData); $iterator++,$pagination++ ){
                $records['data'][] = [
                    $siteAssignmentData[$pagination]->id,
                    $siteAssignmentData[$pagination]->projectSite->project->name.' - '.$siteAssignmentData[$pagination]->projectSite->name,
                    $siteAssignmentData[$pagination]->checklistCategory->mainCategory->name,
                    $siteAssignmentData[$pagination]->checklistCategory->name,
                    $siteAssignmentData[$pagination]->title,
                    count($siteAssignmentData[$pagination]->projectSiteChecklistCheckpoints),
                    '<a class="btn blue" href="/checklist/site-assignment/edit/'.$siteAssignmentData[$pagination]->id.'" >Edit</a>'
                ];
            }
            $status = 200;
        }catch(\Exception $e){
            $data = [
                'action' => 'Get Checklist site assignment Listing',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            $status = 500;
            $records = array();
        }
        return response()->json($records,$status);
    }

    public function getSiteAssignmentEditView(Request $request,$siteChecklist){
        try{
            return view('checklist.site-assignment.edit')->with(compact('siteChecklist'));
        }catch (\Exception $e){
            $data = [
                'action' => 'Get Checklist site assignment Listing',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }
}
