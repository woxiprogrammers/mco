<?php

namespace App\Http\Controllers\Labour;

use App\Employee;
use App\EmployeeImage;
use App\EmployeeImageType;
use App\EmployeeType;
use App\Labour;
use App\Project;
use App\ProjectSite;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class LabourController extends Controller
{
    public function __construct(){
        $this->middleware('custom.auth');
    }

    public function getCreateView(Request $request){
        try{
            $projectSites = ProjectSite::join('projects','projects.id','=','project_sites.project_id')
                ->where('projects.is_active', true)
                ->select('project_sites.id as id','projects.name as name')->get()->toArray();
            $labourTypes = EmployeeType::get()->toArray();
            return view('labour.create')->with(compact('projectSites','labourTypes'));
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
                $labourData = $request->except('_token','project_site_id','profile_image');
            }else{
                $labourData = $request->except('_token','profile_image');
            }
            $labourData['is_active'] = (boolean)false;
            $labourData['employee_type_id'] = EmployeeType::where('slug',$request['employee_type'])->pluck('id')->first();
            $employee = Employee::create($labourData);
            if($request->has('profile_image')){
                $imageTypeId = EmployeeImageType::where('slug','profile')->pluck('id')->first();
                $employeeDirectoryName = sha1($employee->id);
                $imageUploadPath = public_path().env('EMPLOYEE_IMAGE_UPLOAD').DIRECTORY_SEPARATOR.$employeeDirectoryName.DIRECTORY_SEPARATOR.'profile';
                if(!file_exists($imageUploadPath)){
                    File::makeDirectory($imageUploadPath, $mode = 0777, true, true);
                }
                $profileImage = $request->profile_image;
                $imageArray = explode(';',$profileImage);
                $image = explode(',',$imageArray[1])[1];
                $pos  = strpos($profileImage, ';');
                $type = explode(':', substr($profileImage, 0, $pos))[1];
                $extension = explode('/',$type)[1];
                $filename = mt_rand(1,10000000000).sha1(time()).".{$extension}";
                $fileFullPath = $imageUploadPath.DIRECTORY_SEPARATOR.$filename;
                file_put_contents($fileFullPath,base64_decode($image));
                $employeeImageData = [
                    'employee_id' => $employee->id,
                    'employee_image_type_id' => $imageTypeId,
                    'name' => $filename
                ];
                EmployeeImage::create($employeeImageData);
            }
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
                $projectSiteName = ($listingData[$pagination]['project_site_id'] != null) ? $listingData[$pagination]->projectSite->Project->name : '-';
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
            $projectSites = ProjectSite::join('projects','projects.id','=','project_sites.project_id')
                ->where('projects.is_active', true)
                ->select('project_sites.id as id','projects.name as name')->get()->toArray();
            $profilePicTypeId = EmployeeImageType::where('slug','profile')->pluck('id')->first();
            $employeeProfilePic = EmployeeImage::where('employee_id',$labour->id)->where('employee_image_type_id',$profilePicTypeId)->first();
            if($employeeProfilePic == null){
                $profileImagePath = null;
            }else{
                $employeeDirectoryName = sha1($labour->id);
                $imageUploadPath = env('EMPLOYEE_IMAGE_UPLOAD').DIRECTORY_SEPARATOR.$employeeDirectoryName.DIRECTORY_SEPARATOR.'profile';
                $profileImagePath = $imageUploadPath.DIRECTORY_SEPARATOR.$employeeProfilePic->name;
            }
            return view('labour.edit')->with(compact('labour','projectSites','profileImagePath'));
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
                $updateLabourData = $request->except('_token','profile_image');
            }else{
                $updateLabourData = $request->except('_token','project_site_id','profile_image');
            }
            $updateLabourData['employee_type_id'] = EmployeeType::where('slug','labour')->pluck('id')->first();
            Employee::where('id',$labour['id'])->update($updateLabourData);
            if($request->has('profile_image')){
                $imageTypeId = EmployeeImageType::where('slug','profile')->pluck('id')->first();
                $employeeProfilePic = EmployeeImage::where('employee_id',$labour->id)->where('employee_image_type_id',$imageTypeId)->first();
                $employeeDirectoryName = sha1($labour->id);
                $imageUploadPath = public_path().env('EMPLOYEE_IMAGE_UPLOAD').DIRECTORY_SEPARATOR.$employeeDirectoryName.DIRECTORY_SEPARATOR.'profile';
                if(!file_exists($imageUploadPath)){
                    File::makeDirectory($imageUploadPath, $mode = 0777, true, true);
                }
                $profileImage = $request->profile_image;
                $imageArray = explode(';',$profileImage);
                $image = explode(',',$imageArray[1])[1];
                $pos  = strpos($profileImage, ';');
                $type = explode(':', substr($profileImage, 0, $pos))[1];
                $extension = explode('/',$type)[1];
                $filename = mt_rand(1,10000000000).sha1(time()).".{$extension}";
                $fileFullPath = $imageUploadPath.DIRECTORY_SEPARATOR.$filename;
                file_put_contents($fileFullPath,base64_decode($image));
                if($employeeProfilePic == null){
                    $employeeImageData = [
                        'employee_id' => $labour->id,
                        'employee_image_type_id' => $imageTypeId,
                        'name' => $filename
                    ];
                    EmployeeImage::create($employeeImageData);
                }else{
                    $employeeProfilePic->update(['name' => $filename]);
                }
            }
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

    public function getEmployeeId(Request $request,$employeeType){
        try{
            $employeeTypeId = EmployeeType::where('slug',$employeeType)->pluck('id')->first();
            switch($employeeType){
                case 'labour' :
                    $labourCount = Employee::where('employee_type_id',$employeeTypeId)->count();
                    $employeeID = ' L'.($labourCount + 1);
                    break;

                case 'staff' :
                    $labourCount = Employee::where('employee_type_id',$employeeTypeId)->count();
                    $employeeID = ' S'.($labourCount + 1);
                    break;

                case 'partner' :
                    $labourCount = Employee::where('employee_type_id',$employeeTypeId)->count();
                    $employeeID = ' P'.($labourCount + 1);
                    break;

                case 'contractor-labour' :
                    $labourCount = Employee::where('employee_type_id',$employeeTypeId)->count();
                    $employeeID = 'CL'.($labourCount + 1);
                    break;
            }
        }catch(\Exception $e){
            $data = [
                'action' => 'Get Employee ID',
                'exception' => $e->getMessage(),
                'params' => $request->all()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
        return $employeeID;
    }
}
