<?php

namespace App\Http\Controllers\Checklist;

use App\ChecklistCategory;
use App\ChecklistStatus;
use App\Client;
use App\ProjectSiteChecklist;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ChecklistUserAssignmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('custom.auth');
    }

    public function getManageView(Request $request)
    {
        try {
            return view('checklist.user-assignment.manage');
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

    public function getCreateView(Request $request){
        try {
            $clients = Client::join('projects','projects.client_id','=','clients.id')
                            ->join('project_sites','project_sites.project_id','=','projects.id')
                            ->join('quotations','quotations.project_site_id','=','project_sites.id')
                            ->where('clients.is_active', true)
                            ->select('clients.id as id','clients.company as company')
                            ->distinct('id')
                            ->get();
            return view('checklist.user-assignment.create')->with(compact('clients'));
        } catch (\Exception $e) {
            $data = [
                'action' => "Get Check List User Assignment Create view",
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getCategories(Request $request){
        try{
            switch ($request->slug){
                case 'main-category':
                    $mainCategoryIds = ChecklistCategory::join('project_site_checklists','project_site_checklists.checklist_category_id','=','checklist_categories.id')
                                        ->where('project_site_checklists.project_site_id', $request->project_site_id)
                                        ->where('project_site_checklists.quotation_floor_id', $request->quotation_floor_id)
                                        ->select('checklist_categories.category_id as id')
                                        ->distinct('id')
                                        ->get();
                    if(count($mainCategoryIds) > 0){
                        $mainCategoryIds = $mainCategoryIds->toArray();
                        $mainCategories = ChecklistCategory::whereIn('id',$mainCategoryIds)->select('id','name')->get();
                        $categories = '<option value="">--Select Main Category--</option>';
                        foreach($mainCategories as $mainCategory){
                            $categories .= '<option value="'.$mainCategory['id'].'">'.$mainCategory['name'].'</option>';
                        }
                    }else{
                        $categories = '<option value="">--No checklist assigned --</option>';
                    }
                    break;

                case 'sub-category':
                    $mainCategoryIds = ChecklistCategory::join('project_site_checklists','project_site_checklists.checklist_category_id','=','checklist_categories.id')
                        ->where('project_site_checklists.project_site_id', $request->project_site_id)
                        ->where('project_site_checklists.quotation_floor_id', $request->quotation_floor_id)
                        ->where('checklist_categories.category_id', $request->category_id)
                        ->select('checklist_categories.id as id')
                        ->distinct('id')
                        ->get();
                    if(count($mainCategoryIds) > 0){
                        $mainCategoryIds = $mainCategoryIds->toArray();
                        $mainCategories = ChecklistCategory::whereIn('id',$mainCategoryIds)->select('id','name')->get();
                        $categories = '<option value="">--Select Sub Category--</option>';
                        foreach($mainCategories as $mainCategory){
                            $categories .= '<option value="'.$mainCategory['id'].'">'.$mainCategory['name'].'</option>';
                        }
                    }else{
                        $categories = '<option value="">--No checklist assigned --</option>';
                    }
                    break;

                default:
                    $categories = '';
            }
            $status = 200;
        }catch (\Exception $e){
            $data = [
                'action' => "Get Check List User Assignment Create view",
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            $status = 500;
            $categories = [];
        }
        return response()->json($categories,$status);
    }

    public function getUsers(Request $request){
        try{
            $status = 200;
            $response = '';
            $users = User::join('user_has_permissions','users.id','=','user_has_permissions.user_id')
                            ->join('permissions','permissions.id','=','user_has_permissions.permission_id')
                            ->join('user_has_roles','user_has_roles.user_id','=','users.id')
                            ->join('roles','user_has_roles.role_id','=','roles.id')
                            ->join('user_project_site_relation','user_project_site_relation.user_id','=','users.id')
                            ->where('user_project_site_relation.project_site_id', $request->project_site_id)
                            ->where('permissions.name','create-checklist-management')
                            ->where('permissions.is_web',true)
                            ->where('users.is_active', true)
                            ->whereNotIn('roles.slug',['superadmin','admin'])
                            ->select('users.id as id','users.first_name as first_name','users.last_name as last_name')
                            ->get();
            foreach ($users as $user){
                $response .= '<li class="form-control" style="margin-top: 0.5%;padding: 6px 6px !important;">
                                <input type="checkbox" name="users[]" value="'.$user['id'].'"> '.$user['first_name'].' '.$user['last_name'].'
                            </li>';
            }
        }catch (\Exception $e){
            $data = [
                'action' => "Get Check List User Assignment Create view",
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            $status = 500;
            $response = '';
        }
        return response()->json($response,$status);
    }

    public function createUserAssignment(Request $request){
        try{
            dd($request->all());
            $projectSiteChecklist = ProjectSiteChecklist::where('project_site_id',$request->project_site_id)
                                                        ->where('quotation_floor_id', $request->quotation_floor_id)
                                                        ->where('checklist_category_id', $request->sub_category_id)
                                                        ->first();

            $projectSiteUserAssignmentData = [
                'project_site_checklist_id' => $projectSiteChecklist->id,
                'checklist_status_id' => ChecklistStatus::where('slug','assigned')->pluck('id')->first(),
                'assigned_by' => Auth::user()->id
            ];
            if($request->has('users')){
                foreach($request->users as $userId){
                    $projectSiteUserAssignmentData['assigned_to'] = $userId;
                }
            }
        }catch(\Exception $e){
            $data = [
                'action' => "Get Check List User Assignment Create view",
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }
}
