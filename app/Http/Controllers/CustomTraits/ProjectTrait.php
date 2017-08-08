<?php
/**
 * Created by Ameya Joshi.
 * Date: 14/6/17
 * Time: 6:06 PM
 */

namespace App\Http\Controllers\CustomTraits;


use App\Client;
use App\HsnCode;
use App\Project;
use App\ProjectSite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

trait ProjectTrait{

    public function getCreateView(Request $request){
        try{
            $clients = Client::where('is_active', true)->get();
            $hsnCodes = HsnCode::select('id','code','description')->get();
            return view('admin.project.create')->with(compact('clients','hsnCodes'));
        }catch(\Exception $e){
            $data = [
                'action' => 'Get Project create view',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getManageView(Request $request){
        try{
            return view('admin.project.manage');
        }catch(\Exception $e){
            $data = [
                'action' => 'Get Project Manage view',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function projectListing(Request $request){
        try{
            $listingData = array();
            $k = 0;
            $clientData = Client::where('is_active',true)->orderBy('updated_at','desc')->get()->toArray();
            for($i = 0 ; $i < count($clientData) ; $i++){
                $project = Project::where('client_id',$clientData[$i]['id'])->orderBy('updated_at','desc')->get()->toArray();
                for($j = 0 ; $j < count($project) ; $j++){
                    $project_site = ProjectSite::where('project_id',$project[$j]['id'])->orderBy('updated_at','desc')->get()->toArray();
                    for($l = 0 ; $l < count($project_site) ; $l++){
                        $listingData[$k]['company'] = $clientData[$i]['company'];
                        $listingData[$k]['project_name'] = $project[$j]['name'];
                        $listingData[$k]['project_id'] = $project[$j]['id'];
                        $listingData[$k]['project_is_active'] = $project[$j]['is_active'];
                        $listingData[$k]['project_site_id'] = $project_site[$l]['id'];
                        $listingData[$k]['project_site_name'] = $project_site[$l]['name'];
                        $k++;
                    }

                }
            }
            $iTotalRecords = count($listingData);
            $records = array();
            $records['data'] = array();
            $end = $request->length < 0 ? count($listingData) : $request->length;
            for($iterator = 0,$pagination = $request->start; $iterator < $end && $pagination < count($listingData); $iterator++,$pagination++ ){
                if( $listingData[$pagination]['project_is_active'] == true){
                    $projectStatus = '<td><span class="label label-sm label-success"> Enabled </span></td>';
                    $status = 'Disable';
                }else{
                    $projectStatus = '<td><span class="label label-sm label-danger"> Disabled</span></td>';
                    $status = 'Enable';
                }
                $records['data'][$iterator] = [
                    $listingData[$pagination]['company'],
                    $listingData[$pagination]['project_name'],
                    $listingData[$pagination]['project_site_name'],
                    $projectStatus,
                    '<div class="btn-group">
                        <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                            Actions
                            <i class="fa fa-angle-down"></i>
                        </button>
                        <ul class="dropdown-menu pull-left" role="menu">
                            <li>
                                <a href="/project/edit/'.$listingData[$pagination]['project_id'].'">
                                    <i class="icon-docs"></i> Edit </a>
                            </li>
                            <li>
                                <a href="/project/change-status/'.$listingData[$pagination]['project_id'].'">
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
                'action' => 'Product Listing',
                'params' => $request->all(),
                'exception'=> $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
        return response()->json($records,200);
    }

    public function checkProjectName(Request $request){
        try{
            $projectName = $request->name;
            if($request->has('project_id')){
                $nameCount = Project::where('name','ilike',$projectName)->where('id','!=',$request->project_id)->count();
            }else{
                $nameCount = Project::where('name','ilike',$projectName)->count();
            }
            if($nameCount > 0){
                return 'false';
            }else{
                return 'true';
            }
        }catch(\Exception $e){
            $data = [
                'action' => 'Check Category name',
                'param' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function createProject(Request $request){
        try{
            $projectData = array();
            $projectData['name'] = ucwords($request->project_name);
            $projectData['client_id'] = $request->client_id;
            $projectData['is_active'] = false;
            $projectData['hsn_code'] = trim($request->hsn_code);
            $project = Project::create($projectData);
            $projectSiteData = array();
            $projectSiteData['project_id'] = $project->id;
            $projectSiteData['name'] = ucwords($request->project_site_name);
            $projectSiteData['address'] = $request->address;
            $projectSite = ProjectSite::create($projectSiteData);
            $request->session()->flash('success', 'Project Created successfully.');
            return redirect('/project/create');
        }catch(\Exception $e){
            $data = [
                'action' => 'Create Project',
                'params' => $request->all(),
                'exception'=> $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function changeProjectStatus(Request $request, $project){
        try{
            $newStatus = (boolean)!$project->is_active;
            $project->update(['is_active' => $newStatus]);
            $request->session()->flash('success', 'Project Status changed successfully.');
            return redirect('/project/manage');
        }catch(\Exception $e){
            $data = [
                'action' => 'change Project status',
                'params' => $request->all(),
                'exception'=> $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getEditView(Request $request, $project){
        try{
            $projectData['client'] = $project->client->company;
            $projectData['id'] = $project->id;
            $projectData['project'] = $project->name;
            $projectData['project_hsn_code'] = $project->hsn_code_id;
            $project->project_site = $project->project_site->toArray();
            $projectData['project_site'] = $project->project_site[0]['name'];
            $projectData['project_site_address'] = $project->project_site[0]['address'];
            $hsnCodes = HsnCode::select('id','code','description')->get();
            return view('admin.project.edit')->with(compact('projectData','hsnCodes'));
        }catch(\Exception $e){
            $data = [
                'action' => 'change Project status',
                'params' => $request->all(),
                'exception'=> $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function editProject(Request $request, $project){
        try{
            $project->update([
                'name'=>$request->project_name,
                'hsn_code_id' => $request->hsn_code
            ]);
            ProjectSite::where('project_id',$project->id)->update([
                'name' => $request->project_site_name,
                'address' => $request->address
            ]);
            $request->session()->flash('success', 'Project edited successfully.');
            return redirect('/project/edit/'.$project->id);
        }catch(\Exception $e){
            $data = [
                'action' => 'change Project status',
                'params' => $request->all(),
                'exception'=> $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }
}